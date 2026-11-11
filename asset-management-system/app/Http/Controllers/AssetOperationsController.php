<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\AssetActivity;
use App\Models\AssetAssignment;
use App\Models\AssetDisposal;
use App\Models\AssetPurchaseRecord;
use App\Models\AssetRequest;
use App\Models\AssetReturn;
use App\Models\AssetStatusHistory;
use App\Models\AssetTransfer;
use App\Models\AssetVerificationItem;
use App\Models\AssetVerificationSession;
use App\Models\AssetWarranty;
use App\Models\Category;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Location;
use App\Models\MaintenanceRecord;
use App\Models\User;
use App\Models\WarrantyClaim;
use App\Notifications\AssetOperationNotification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AssetOperationsController extends Controller
{
    private const STATUSES = ['available', 'assigned', 'under_maintenance', 'damaged', 'disposed'];

    private const CONDITIONS = ['good', 'fair', 'poor'];

    private const REPORT_TYPES = ['assets', 'assignments', 'available', 'department', 'location', 'category', 'movements'];

    public function index(Request $request, string $section): View|StreamedResponse
    {
        $this->authorizeSection($request->user(), $section);

        $titles = [
            'assignments' => 'Asset assignments',
            'returns' => 'Asset returns',
            'transfers' => 'Asset transfers',
            'status' => 'Asset status & condition',
            'history' => 'Asset history',
            'reports' => 'Basic reports',
            'requests' => 'Asset requests',
            'my-requests' => 'My asset requests',
            'purchases' => 'Purchase management',
            'warranties' => 'Warranty management',
            'verification' => 'Asset verification',
            'disposals' => 'Asset disposal',
            'notifications' => 'Notifications',
            'maintenance' => 'Maintenance requests',
            'people' => 'Employee assets',
        ];
        abort_unless(isset($titles[$section]), 404);

        if ($section === 'reports' && $request->query('download') === '1') {
            return $this->downloadReport($request);
        }
        $reportType = $section === 'reports' ? $this->resolveReportType($request) : 'assets';

        $data = match ($section) {
            'assignments' => [
                'availableAssets' => $this->availableAssets()->get(),
                'employees' => $this->activeEmployees($request->user())->get(),
                'departments' => Department::query()->where('is_active', true)
                    ->when($this->isDepartmentManager($request->user()), fn (Builder $query) => $query->whereKey($this->departmentId($request->user())))
                    ->orderBy('name')->get(),
                'assignments' => AssetAssignment::query()->with(['asset', 'employee', 'department', 'assignedBy', 'assetReturn'])
                    ->when($this->isDepartmentManager($request->user()), fn (Builder $query) => $query->where('department_id', $this->departmentId($request->user())))
                    ->latest('assigned_at')->paginate(15),
            ],
            'returns' => [
                'openAssignments' => AssetAssignment::query()->with(['asset', 'employee', 'department'])
                    ->when($this->isDepartmentManager($request->user()), fn (Builder $query) => $query->where('department_id', $this->departmentId($request->user())))
                    ->whereNull('closed_at')->latest('assigned_at')->get(),
                'returns' => AssetReturn::query()->with(['asset', 'assetAssignment.employee', 'receivedBy'])
                    ->when($this->isDepartmentManager($request->user()), fn (Builder $query) => $query->whereHas('assetAssignment', fn (Builder $assignment) => $assignment->where('department_id', $this->departmentId($request->user()))))
                    ->latest('returned_at')->paginate(15),
            ],
            'transfers' => [
                'assets' => Asset::query()->with(['currentEmployee', 'location'])
                    ->when($this->isDepartmentManager($request->user()), fn (Builder $query) => $query->whereIn('id', $this->departmentAssetIds($request->user())))
                    ->whereNotIn('status', ['disposed', 'under_maintenance'])->orderBy('asset_code')->get(),
                'employees' => $this->activeEmployees($request->user())->get(),
                'locations' => Location::query()->where('is_active', true)->orderBy('name')->get(),
                'transfers' => AssetTransfer::query()->with([
                    'asset', 'fromEmployee', 'toEmployee', 'fromLocation', 'toLocation', 'authorizedBy',
                ])->when($this->isDepartmentManager($request->user()), fn (Builder $query) => $query->where(function (Builder $scope) use ($request): void {
                    $scope->whereHas('fromEmployee', fn (Builder $employee) => $employee->where('department_id', $this->departmentId($request->user())))
                        ->orWhereHas('toEmployee', fn (Builder $employee) => $employee->where('department_id', $this->departmentId($request->user())));
                }))->latest('transferred_at')->paginate(15),
            ],
            'status' => [
                'assets' => Asset::query()->with(['category', 'location', 'currentEmployee.user'])
                    ->when($this->isDepartmentManager($request->user()), fn (Builder $query) => $query->whereIn('id', $this->departmentAssetIds($request->user())))
                    ->orderBy('asset_code')->paginate(20),
            ],
            'history' => [
                'assets' => $this->departmentAssetQuery($request->user())->orderBy('asset_code')->get(['id', 'asset_code', 'name']),
                'activities' => AssetActivity::query()->with(['asset', 'user'])
                    ->when($this->isDepartmentManager($request->user()), fn (Builder $query) => $query->whereIn('asset_id', $this->departmentAssetIds($request->user())))
                    ->when($request->integer('asset_id'), fn (Builder $query, int $assetId) => $query->where('asset_id', $assetId))
                    ->latest('occurred_at')->paginate(30)->withQueryString(),
                'statusHistories' => AssetStatusHistory::query()->with(['asset', 'changedBy'])
                    ->when($this->isDepartmentManager($request->user()), fn (Builder $query) => $query->whereIn('asset_id', $this->departmentAssetIds($request->user())))
                    ->when($request->integer('asset_id'), fn (Builder $query, int $assetId) => $query->where('asset_id', $assetId))
                    ->latest('changed_at')->paginate(30)->withQueryString(),
            ],
            'reports' => $this->reportData($reportType, $request->user()),
            'requests' => [
                'requests' => AssetRequest::query()->with(['requester', 'category', 'department', 'reviewedBy', 'fulfilledAsset'])
                    ->when($this->isDepartmentManager($request->user()), fn (Builder $query) => $query->whereHas('requester.employee', fn (Builder $employee) => $employee->where('department_id', $this->departmentId($request->user()))))
                    ->latest()->paginate(20),
                'availableAssets' => $this->availableAssets()->get(),
            ],
            'my-requests' => [
                'requests' => AssetRequest::query()->with(['category', 'department', 'reviewedBy', 'fulfilledAsset'])
                    ->where('requester_user_id', $request->user()->id)->latest()->paginate(20),
                'categories' => Category::query()->where('is_active', true)->orderBy('name')->get(),
                'departments' => Department::query()->where('is_active', true)->orderBy('name')->get(),
            ],
            'purchases' => [
                'assets' => Asset::query()->orderBy('asset_code')->get(),
                'purchaseRecords' => AssetPurchaseRecord::query()->with(['asset', 'recordedBy'])
                    ->latest('received_at')->paginate(20),
            ],
            'warranties' => [
                'assets' => Asset::query()->orderBy('asset_code')->get(),
                'warranties' => AssetWarranty::query()->with(['asset', 'recordedBy', 'claims.createdBy'])
                    ->latest('expires_at')->paginate(20),
            ],
            'verification' => [
                'assets' => Asset::query()->orderBy('asset_code')->get(),
                'verificationSessions' => AssetVerificationSession::query()
                    ->with(['verifier', 'items.asset'])->latest('verified_at')->paginate(15),
            ],
            'disposals' => [
                'assets' => Asset::query()->where('status', '!=', 'disposed')->orderBy('asset_code')->get(),
                'disposals' => AssetDisposal::query()->with(['asset', 'requestedBy', 'reviewedBy'])
                    ->latest()->paginate(20),
            ],
            'notifications' => [
                'notifications' => $request->user()->notifications()->latest()->paginate(30),
                'unreadNotificationCount' => $request->user()->unreadNotifications()->count(),
            ],
            'maintenance' => [
                'assets' => Asset::query()->whereNotIn('status', ['disposed', 'under_maintenance'])->orderBy('asset_code')->get(),
                'maintenanceRecords' => MaintenanceRecord::query()->with(['asset', 'assignedTo', 'createdBy'])
                    ->when($request->user()->role?->slug === User::ROLE_IT_TECHNICIAN, fn (Builder $query) => $query->where(fn (Builder $scope) => $scope
                        ->where('assigned_to_user_id', $request->user()->id)->orWhereNull('assigned_to_user_id')))
                    ->latest('reported_at')->paginate(20),
                'technicians' => User::query()->where('is_active', true)->orderBy('name')->get(),
            ],
            'people' => [
                'employees' => Employee::query()->with(['user', 'department', 'assets.category', 'assets.location'])
                    ->where('employment_status', 'active')
                    ->where(fn (Builder $query) => $query->whereNull('user_id')
                        ->orWhereHas('user', fn (Builder $userQuery) => $userQuery->where('is_active', true)))
                    ->orderBy('first_name')->orderBy('last_name')->paginate(30),
            ],
        };

        return view('operations.index', [
            ...$data,
            'section' => $section,
            'pageTitle' => $titles[$section],
            'reportType' => $reportType,
        ]);
    }

    public function employeeDashboard(Request $request): View
    {
        $employeeId = Employee::createForUser($request->user())->id;
        $assets = Asset::query()
            ->with('category')
            ->with(['assignments' => fn (Builder $query) => $query
                ->where('employee_id', $employeeId ?? 0)
                ->whereNull('closed_at')
                ->latest('assigned_at')])
            ->where('current_employee_id', $employeeId ?? 0)
            ->orderBy('asset_code')
            ->get();

        return view('employee.dashboard', [
            'assignedAssets' => $assets,
            'pendingRequestCount' => AssetRequest::query()
                ->where('requester_user_id', $request->user()->id)
                ->where('status', 'pending')->count(),
        ]);
    }

    public function storeAssignment(Request $request): RedirectResponse
    {
        $this->authorizeMutation($request->user(), 'assignments');
        $validated = $request->validate([
            'asset_id' => ['required', 'exists:assets,id'],
            'employee_id' => ['required', Rule::exists('employees', 'id')->where('employment_status', 'active')],
            'department_id' => ['nullable', Rule::exists('departments', 'id')->where('is_active', true)],
            'assigned_at' => ['required', 'date'],
            'remarks' => ['nullable', 'string', 'max:5000'],
        ]);

        DB::transaction(function () use ($validated, $request): void {
            $asset = Asset::query()->lockForUpdate()->findOrFail($validated['asset_id']);
            $employee = Employee::query()->with('user')->lockForUpdate()->findOrFail($validated['employee_id']);
            if ($this->isDepartmentManager($request->user())) {
                abort_unless($employee->department_id === $this->departmentId($request->user()), 403);
            }
            abort_if($employee->employment_status !== 'active' || ($employee->user && ! $employee->user->is_active), 422,
                'Only active employees can receive assets.');
            abort_unless($asset->status === 'available' && $asset->current_employee_id === null, 422,
                'Only available assets can be assigned.');
            abort_if(AssetAssignment::query()->where('asset_id', $asset->id)->whereNull('closed_at')->exists(), 422,
                'This asset already has an open assignment record.');

            $oldStatus = $asset->status;
            AssetAssignment::query()->create([
                ...$validated,
                'department_id' => $this->isDepartmentManager($request->user())
                    ? $this->departmentId($request->user())
                    : ($validated['department_id'] ?? $employee->department_id),
                'assigned_by_user_id' => $request->user()->id,
            ]);
            $asset->update([
                'status' => 'assigned',
                'current_employee_id' => $employee->id,
                'updated_by' => $request->user()->id,
            ]);
            $this->recordStatusChange($asset, $request, $oldStatus, 'assigned', $asset->condition, $asset->condition, $validated['remarks'] ?? null);
            $this->recordActivity($asset, $request, 'assigned', "Assigned {$asset->asset_code} to {$employee->first_name} {$employee->last_name}.", [
                'employee_id' => $employee->id,
            ]);
            $employee->user?->notify(new AssetOperationNotification(
                kind: 'asset_assignment',
                title: 'Asset assigned to you',
                message: "{$asset->asset_code} — {$asset->name} has been assigned to you.",
                url: route('employee.dashboard'),
                details: ['asset_id' => $asset->id],
            ));
        });

        return to_route('operations.assignments')->with('status', 'Asset assigned successfully.');
    }

    public function storeReturn(Request $request, AssetAssignment $assignment): RedirectResponse
    {
        $this->authorizeMutation($request->user(), 'returns');
        if ($this->isDepartmentManager($request->user())) {
            abort_unless($assignment->department_id === $this->departmentId($request->user()), 403);
        }
        $validated = $request->validate([
            'returned_at' => ['required', 'date'],
            'returned_condition' => ['required', Rule::in(self::CONDITIONS)],
            'next_status' => ['required', Rule::in(['available', 'under_maintenance', 'damaged'])],
            'remarks' => ['nullable', 'string', 'max:5000'],
        ]);

        DB::transaction(function () use ($validated, $request, $assignment): void {
            $asset = Asset::query()->lockForUpdate()->findOrFail($assignment->asset_id);
            $lockedAssignment = AssetAssignment::query()->lockForUpdate()->findOrFail($assignment->id);
            abort_if($lockedAssignment->closed_at !== null, 422, 'This assignment has already been returned.');
            abort_unless($asset->current_employee_id === $lockedAssignment->employee_id, 422,
                'The asset holder no longer matches this assignment.');
            abort_if($lockedAssignment->assigned_at && Carbon::parse($validated['returned_at'])->lt($lockedAssignment->assigned_at), 422,
                'The return date cannot be earlier than the assignment date.');
            $hasOpenMaintenance = MaintenanceRecord::query()->where('asset_id', $asset->id)
                ->whereIn('status', ['requested', 'in_progress'])->exists();
            abort_if($hasOpenMaintenance && $validated['next_status'] !== 'under_maintenance', 422,
                'This asset has an open maintenance record and must be returned to Under Maintenance.');
            abort_if(! $hasOpenMaintenance && $validated['next_status'] === 'under_maintenance', 422,
                'Create a maintenance request before returning this asset to Under Maintenance.');

            $oldStatus = $asset->status;
            $oldCondition = $asset->condition;
            AssetReturn::query()->create([
                ...$validated,
                'asset_assignment_id' => $lockedAssignment->id,
                'asset_id' => $asset->id,
                'received_by_user_id' => $request->user()->id,
            ]);
            $lockedAssignment->update(['closed_at' => $validated['returned_at']]);
            $asset->update([
                'current_employee_id' => null,
                'status' => $validated['next_status'],
                'condition' => $validated['returned_condition'],
                'updated_by' => $request->user()->id,
            ]);
            $this->recordStatusChange($asset, $request, $oldStatus, $asset->status, $oldCondition, $asset->condition, $validated['remarks'] ?? null, $validated['returned_at']);
            $this->recordActivity($asset, $request, 'returned', "Returned {$asset->asset_code}; next status: {$asset->status}.", [
                'assignment_id' => $lockedAssignment->id,
                'condition' => $asset->condition,
            ], $validated['returned_at']);
            $lockedAssignment->employee?->user?->notify(new AssetOperationNotification(
                kind: 'asset_return',
                title: 'Asset return recorded',
                message: "{$asset->asset_code} was recorded as returned.",
                url: route('employee.dashboard'),
                details: ['asset_id' => $asset->id, 'assignment_id' => $lockedAssignment->id],
            ));
        });

        return to_route('operations.returns')->with('status', 'Asset return recorded.');
    }

    public function storeTransfer(Request $request): RedirectResponse
    {
        $this->authorizeMutation($request->user(), 'transfers');
        $validated = $request->validate([
            'asset_id' => ['required', 'exists:assets,id'],
            'to_employee_id' => ['nullable', Rule::exists('employees', 'id')->where('employment_status', 'active')],
            'to_location_id' => ['nullable', Rule::exists('locations', 'id')->where('is_active', true)],
            'transferred_at' => ['required', 'date'],
            'reason' => ['required', 'string', 'max:5000'],
        ]);
        if (empty($validated['to_employee_id']) && empty($validated['to_location_id'])) {
            return back()->withErrors(['to_employee_id' => 'Select a new holder or a new location.'])->withInput();
        }

        DB::transaction(function () use ($validated, $request): void {
            $asset = Asset::query()->lockForUpdate()->findOrFail($validated['asset_id']);
            abort_if(in_array($asset->status, ['disposed', 'under_maintenance'], true), 422,
                'Disposed or under-maintenance assets cannot be transferred.');
            $newEmployeeId = $validated['to_employee_id'] ?? null;
            abort_if($newEmployeeId && $asset->status === 'damaged', 422,
                'Damaged assets cannot be transferred to an employee.');
            $newLocationId = $validated['to_location_id'] ?? $asset->location_id;
            abort_if(
                $asset->current_employee_id === $newEmployeeId && $asset->location_id === $newLocationId,
                422,
                'Choose a destination different from the current holder and location.',
            );

            $fromEmployeeId = $asset->current_employee_id;
            $fromLocationId = $asset->location_id;
            $newEmployee = $newEmployeeId
                ? Employee::query()->with('user')->lockForUpdate()->findOrFail($newEmployeeId)
                : null;
            if ($this->isDepartmentManager($request->user())) {
                $departmentId = $this->departmentId($request->user());
                abort_unless($asset->currentEmployee()->value('department_id') === $departmentId, 403);
                abort_if($newEmployee && $newEmployee->department_id !== $departmentId, 403);
            }
            abort_if($newEmployee && ($newEmployee->employment_status !== 'active' || ($newEmployee->user && ! $newEmployee->user->is_active)), 422,
                'Only active employees can receive assets.');
            $openAssignments = AssetAssignment::query()->where('asset_id', $asset->id)->whereNull('closed_at')
                ->lockForUpdate()->get();
            abort_if($openAssignments->count() > 1, 422, 'This asset has multiple open assignment records; resolve them before transferring.');
            abort_if($openAssignments->isNotEmpty()
                && $openAssignments->first()->employee_id !== $fromEmployeeId, 422,
                'The open assignment does not match the current asset holder.');
            abort_if($openAssignments->isNotEmpty()
                && Carbon::parse($validated['transferred_at'])->lt($openAssignments->first()->assigned_at), 422,
                'The transfer date cannot be earlier than the current assignment date.');
            foreach ($openAssignments as $assignment) {
                $assignment->update(['closed_at' => $validated['transferred_at']]);
            }

            $oldStatus = $asset->status;
            $oldCondition = $asset->condition;
            AssetTransfer::query()->create([
                'asset_id' => $asset->id,
                'from_employee_id' => $fromEmployeeId,
                'to_employee_id' => $newEmployeeId,
                'from_location_id' => $fromLocationId,
                'to_location_id' => $newLocationId,
                'authorized_by_user_id' => $request->user()->id,
                'transferred_at' => $validated['transferred_at'],
                'reason' => $validated['reason'],
            ]);
            $asset->update([
                'current_employee_id' => $newEmployeeId,
                'location_id' => $newLocationId,
                'status' => $newEmployeeId ? 'assigned' : ($fromEmployeeId ? 'available' : $oldStatus),
                'updated_by' => $request->user()->id,
            ]);
            if ($newEmployeeId) {
                AssetAssignment::query()->create([
                    'asset_id' => $asset->id,
                    'employee_id' => $newEmployee->id,
                    'department_id' => $newEmployee->department_id,
                    'assigned_by_user_id' => $request->user()->id,
                    'assigned_at' => $validated['transferred_at'],
                    'remarks' => 'Created by asset transfer: '.$validated['reason'],
                ]);
            }
            $this->recordStatusChange($asset, $request, $oldStatus, $asset->status, $oldCondition, $asset->condition, $validated['reason'], $validated['transferred_at']);
            $this->recordActivity($asset, $request, 'transferred', "Transferred {$asset->asset_code}.", [
                'from_employee_id' => $fromEmployeeId,
                'to_employee_id' => $newEmployeeId,
                'from_location_id' => $fromLocationId,
                'to_location_id' => $newLocationId,
                'reason' => $validated['reason'],
            ], $validated['transferred_at']);
            $newEmployee?->user?->notify(new AssetOperationNotification(
                kind: 'asset_assignment',
                title: 'Asset transferred to you',
                message: "{$asset->asset_code} — {$asset->name} has been transferred to you.",
                url: route('employee.dashboard'),
                details: ['asset_id' => $asset->id],
            ));
        });

        return to_route('operations.transfers')->with('status', 'Asset transfer recorded.');
    }

    public function updateStatus(Request $request, Asset $asset): RedirectResponse
    {
        $this->authorizeMutation($request->user(), 'status');
        $validated = $request->validate([
            'status' => ['required', Rule::in(self::STATUSES)],
            'condition' => ['required', Rule::in(self::CONDITIONS)],
            'remarks' => ['nullable', 'string', 'max:5000'],
        ]);

        DB::transaction(function () use ($validated, $request, $asset): void {
            $asset = Asset::query()->lockForUpdate()->findOrFail($asset->id);
            abort_if($validated['status'] === 'disposed' && $asset->status !== 'disposed', 422,
                'Submit a disposal request and receive approval before disposing an asset.');
            abort_if($validated['status'] === 'assigned' && ! $asset->current_employee_id, 422,
                'Assign this asset to an employee before setting its status to assigned.');
            abort_if($validated['status'] === 'available' && $asset->current_employee_id, 422,
                'Return or transfer this asset before setting its status to available.');
            abort_if($validated['status'] === 'disposed' && $asset->current_employee_id, 422,
                'Return or transfer this asset before setting it to Disposed.');
            $hasOpenMaintenance = MaintenanceRecord::query()->where('asset_id', $asset->id)
                ->whereIn('status', ['requested', 'in_progress'])->exists();
            abort_if($hasOpenMaintenance && $validated['status'] !== 'under_maintenance', 422,
                'Complete or cancel the open maintenance record before changing this asset status.');
            abort_if(! $hasOpenMaintenance && $validated['status'] === 'under_maintenance', 422,
                'Create a maintenance request before setting this asset to Under Maintenance.');
            $oldStatus = $asset->status;
            $openAssignment = AssetAssignment::query()->where('asset_id', $asset->id)
                ->whereNull('closed_at')->lockForUpdate()->first();
            abort_if($asset->current_employee_id === null && $openAssignment, 422,
                'Resolve the open assignment record before changing this asset.');
            abort_if($openAssignment && $openAssignment->employee_id !== $asset->current_employee_id, 422,
                'The open assignment does not match the current asset holder.');
            $oldCondition = $asset->condition;
            $asset->update([
                'status' => $validated['status'],
                'condition' => $validated['condition'],
                'updated_by' => $request->user()->id,
            ]);
            $this->recordStatusChange($asset, $request, $oldStatus, $asset->status, $oldCondition, $asset->condition, $validated['remarks'] ?? null);
            $this->recordActivity($asset, $request, 'status_changed', "Updated {$asset->asset_code} to {$asset->status} ({$asset->condition}).");
        });

        return to_route('operations.status')->with('status', 'Asset status and condition updated.');
    }

    public function storePurchase(Request $request): RedirectResponse
    {
        $this->authorizeMutation($request->user(), 'purchases');
        $validated = $request->validate([
            'asset_id' => ['required', 'exists:assets,id'],
            'supplier' => ['required', 'string', 'max:255'],
            'purchase_order_reference' => ['nullable', 'string', 'max:255'],
            'invoice' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:10240'],
            'purchase_cost' => ['required', 'numeric', 'min:0'],
            'received_at' => ['required', 'date'],
        ]);

        DB::transaction(function () use ($validated, $request): void {
            $asset = Asset::query()->lockForUpdate()->findOrFail($validated['asset_id']);
            $invoicePath = $request->file('invoice')?->store('asset-invoices', 'local');
            AssetPurchaseRecord::query()->create([
                'asset_id' => $asset->id,
                'recorded_by_user_id' => $request->user()->id,
                'supplier' => $validated['supplier'],
                'purchase_order_reference' => $validated['purchase_order_reference'] ?? null,
                'invoice_path' => $invoicePath,
                'purchase_cost' => $validated['purchase_cost'],
                'received_at' => $validated['received_at'],
            ]);
            $asset->update([
                'purchase_cost' => $validated['purchase_cost'],
                'purchase_date' => $validated['received_at'],
            ]);
            $this->recordActivity($asset, $request, 'purchase_recorded',
                "Purchase details recorded for {$asset->asset_code}.", [
                    'supplier' => $validated['supplier'],
                    'purchase_order_reference' => $validated['purchase_order_reference'] ?? null,
                    'purchase_cost' => $validated['purchase_cost'],
                    'received_at' => $validated['received_at'],
                ]);
        });

        return to_route('operations.purchases')->with('status', 'Purchase details saved.');
    }

    public function storeWarranty(Request $request): RedirectResponse
    {
        $this->authorizeMutation($request->user(), 'warranties');
        $validated = $request->validate([
            'asset_id' => ['required', 'exists:assets,id'],
            'provider' => ['required', 'string', 'max:255'],
            'starts_at' => ['required', 'date'],
            'expires_at' => ['required', 'date', 'after_or_equal:starts_at'],
            'document' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:10240'],
        ]);

        DB::transaction(function () use ($validated, $request): void {
            $asset = Asset::query()->lockForUpdate()->findOrFail($validated['asset_id']);
            $documentPath = $request->file('document')?->store('warranty-documents', 'local');
            $warranty = AssetWarranty::query()->create([
                'asset_id' => $asset->id,
                'recorded_by_user_id' => $request->user()->id,
                'provider' => $validated['provider'],
                'starts_at' => $validated['starts_at'],
                'expires_at' => $validated['expires_at'],
                'document_path' => $documentPath,
            ]);
            $asset->update(['warranty_expiry_date' => $validated['expires_at']]);
            $this->recordActivity($asset, $request, 'warranty_recorded',
                "Warranty recorded for {$asset->asset_code}.", [
                    'warranty_id' => $warranty->id,
                    'provider' => $warranty->provider,
                    'expires_at' => $warranty->expires_at->toDateString(),
                ]);
        });

        return to_route('operations.warranties')->with('status', 'Warranty details saved.');
    }

    public function storeWarrantyClaim(Request $request, AssetWarranty $warranty): RedirectResponse
    {
        $this->authorizeMutation($request->user(), 'warranties');
        $validated = $request->validate([
            'claim_reference' => ['nullable', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:5000'],
            'submitted_at' => ['required', 'date'],
        ]);
        $claim = $warranty->claims()->create([
            ...$validated,
            'created_by_user_id' => $request->user()->id,
            'status' => 'submitted',
        ]);
        $this->recordActivity($warranty->asset, $request, 'warranty_claim_submitted',
            "Warranty claim #{$claim->id} submitted for {$warranty->asset->asset_code}.",
            ['warranty_id' => $warranty->id, 'claim_id' => $claim->id]);
        $this->notifyRoleUsers(
            'warranty_claim',
            'Warranty claim submitted',
            "A warranty claim was submitted for {$warranty->asset->asset_code}.",
            route('operations.warranties'),
            ['claim_id' => $claim->id, 'asset_id' => $warranty->asset_id],
            [User::ROLE_ADMIN, User::ROLE_MANAGER, User::ROLE_ASSET_CONTROLLER],
        );

        return to_route('operations.warranties')->with('status', 'Warranty claim recorded.');
    }

    public function reviewWarrantyClaim(Request $request, WarrantyClaim $claim): RedirectResponse
    {
        $this->authorizeMutation($request->user(), 'warranties');
        $validated = $request->validate([
            'status' => ['required', Rule::in(['submitted', 'in_review', 'approved', 'rejected', 'completed'])],
            'resolution' => ['nullable', 'string', 'max:5000'],
        ]);
        $claim->update($validated);
        $claim->createdBy?->notify(new AssetOperationNotification(
            kind: 'warranty_claim',
            title: 'Warranty claim updated',
            message: "Your warranty claim #{$claim->id} is now {$claim->status}.",
            url: route('operations.warranties'),
            details: ['claim_id' => $claim->id],
        ));

        return to_route('operations.warranties')->with('status', 'Warranty claim updated.');
    }

    public function storeVerification(Request $request): RedirectResponse
    {
        $this->authorizeMutation($request->user(), 'verification');
        $validated = $request->validate([
            'asset_id' => ['nullable', 'exists:assets,id', 'required_unless:finding,unregistered'],
            'observed_asset_code' => ['nullable', 'string', 'max:255'],
            'finding' => ['required', Rule::in(['verified', 'missing', 'mismatch', 'unregistered'])],
            'notes' => ['nullable', 'string', 'max:5000'],
            'verified_at' => ['required', 'date'],
        ]);
        $session = DB::transaction(function () use ($validated, $request): AssetVerificationSession {
            $session = AssetVerificationSession::query()->create([
                'verified_by_user_id' => $request->user()->id,
                'verified_at' => $validated['verified_at'],
                'notes' => $validated['notes'] ?? null,
            ]);
            $item = $session->items()->create([
                'asset_id' => $validated['asset_id'] ?? null,
                'observed_asset_code' => $validated['observed_asset_code'] ?? null,
                'finding' => $validated['finding'],
                'notes' => $validated['notes'] ?? null,
            ]);
            if ($item->asset_id) {
                $this->recordActivity($item->asset, $request, 'asset_verified',
                    "Asset verification finding: {$item->finding}.", [
                        'verification_session_id' => $session->id,
                        'finding' => $item->finding,
                        'observed_asset_code' => $item->observed_asset_code,
                    ], $session->verified_at);
            }

            return $session;
        });

        if ($validated['finding'] !== 'verified') {
            $this->notifyRoleUsers(
                'asset_verification',
                'Asset verification finding',
                "A {$validated['finding']} finding was recorded during asset verification.",
                route('operations.verification'),
                ['verification_session_id' => $session->id],
                [User::ROLE_ADMIN, User::ROLE_MANAGER, User::ROLE_ASSET_CONTROLLER],
            );
        }

        return to_route('operations.verification')->with('status', 'Verification finding recorded.');
    }

    public function storeDisposal(Request $request): RedirectResponse
    {
        $this->authorizeMutation($request->user(), 'disposals');
        $validated = $request->validate([
            'asset_id' => ['required', 'exists:assets,id'],
            'reason' => ['required', 'string', 'min:10', 'max:5000'],
        ]);
        $disposal = DB::transaction(function () use ($validated, $request): AssetDisposal {
            $asset = Asset::query()->lockForUpdate()->findOrFail($validated['asset_id']);
            abort_if($asset->status === 'disposed', 422, 'This asset has already been disposed.');
            abort_if(AssetDisposal::query()->where('asset_id', $asset->id)->where('status', 'pending')->exists(), 422,
                'A disposal request is already awaiting approval for this asset.');

            return AssetDisposal::query()->create([
                'asset_id' => $asset->id,
                'requested_by_user_id' => $request->user()->id,
                'reason' => $validated['reason'],
                'status' => 'pending',
            ]);
        });
        $this->recordActivity($disposal->asset, $request, 'disposal_requested',
            "Disposal requested for {$disposal->asset->asset_code}: {$disposal->reason}",
            ['disposal_id' => $disposal->id]);
        $this->notifyRoleUsers(
            'disposal_approval',
            'Asset disposal approval needed',
            "Disposal request for {$disposal->asset->asset_code} needs review.",
            route('operations.disposals'),
            ['disposal_id' => $disposal->id, 'asset_id' => $disposal->asset_id],
            [User::ROLE_ADMIN, User::ROLE_MANAGER, User::ROLE_ASSET_CONTROLLER],
        );

        return to_route('operations.disposals')->with('status', 'Disposal request submitted for approval.');
    }

    public function reviewDisposal(Request $request, AssetDisposal $disposal): RedirectResponse
    {
        $this->authorizeMutation($request->user(), 'disposals');
        $validated = $request->validate([
            'decision' => ['required', Rule::in(['approve', 'reject'])],
            'disposed_at' => ['required_if:decision,approve', 'nullable', 'date'],
            'reviewer_remarks' => ['nullable', 'string', 'max:5000'],
        ]);

        DB::transaction(function () use ($validated, $request, $disposal): void {
            $disposal = AssetDisposal::query()->lockForUpdate()->findOrFail($disposal->id);
            abort_unless($disposal->status === 'pending', 422, 'Only pending disposal requests can be reviewed.');
            $asset = Asset::query()->lockForUpdate()->findOrFail($disposal->asset_id);
            $status = $validated['decision'] === 'approve' ? 'approved' : 'rejected';
            $disposedAt = $validated['decision'] === 'approve' ? ($validated['disposed_at'] ?? now()) : null;

            if ($status === 'approved') {
                abort_if($asset->current_employee_id !== null, 422, 'Return or transfer the asset before disposal.');
                abort_if(AssetAssignment::query()->where('asset_id', $asset->id)->whereNull('closed_at')->exists(), 422,
                    'Close the active asset assignment before disposal.');
                abort_if(MaintenanceRecord::query()->where('asset_id', $asset->id)
                    ->whereIn('status', ['requested', 'in_progress'])->exists(), 422,
                    'Complete maintenance work before disposal.');
                $oldStatus = $asset->status;
                $asset->update(['status' => 'disposed', 'updated_by' => $request->user()->id]);
                $this->recordStatusChange($asset, $request, $oldStatus, 'disposed', $asset->condition, $asset->condition,
                    $disposal->reason, $disposedAt);
                $this->recordActivity($asset, $request, 'disposed',
                    "Disposed {$asset->asset_code}: {$disposal->reason}", ['disposal_id' => $disposal->id], $disposedAt);
            }

            $disposal->update([
                'status' => $status,
                'reviewed_by_user_id' => $request->user()->id,
                'reviewed_at' => now(),
                'disposed_at' => $disposedAt,
                'reviewer_remarks' => $validated['reviewer_remarks'] ?? null,
            ]);
        });
        $disposal->requestedBy->notify(new AssetOperationNotification(
            kind: 'disposal_review',
            title: 'Disposal request reviewed',
            message: "Your disposal request for {$disposal->asset->asset_code} was ".($validated['decision'] === 'approve' ? 'approved' : 'rejected').'.',
            url: route('operations.disposals'),
            details: ['disposal_id' => $disposal->id],
        ));

        return to_route('operations.disposals')->with('status', 'Disposal request reviewed.');
    }

    public function updateRecord(Request $request, string $type, int $record): RedirectResponse
    {
        $section = match ($type) {
            'assignment' => 'assignments',
            'return' => 'returns',
            'transfer' => 'transfers',
            'purchase' => 'purchases',
            'warranty' => 'warranties',
            'verification' => 'verification',
            default => abort(404),
        };
        $this->authorizeMutation($request->user(), $section);

        switch ($type) {
            case 'assignment':
                $this->updateAssignmentRecord($request, $record);
                break;
            case 'return':
                $this->updateReturnRecord($request, $record);
                break;
            case 'transfer':
                $this->updateTransferRecord($request, $record);
                break;
            case 'purchase':
                $this->updatePurchaseRecord($request, $record);
                break;
            case 'warranty':
                $this->updateWarrantyRecord($request, $record);
                break;
            case 'verification':
                $this->updateVerificationRecord($request, $record);
                break;
        }

        return to_route('operations.'.$section)->with('status', 'Record updated.');
    }

    public function destroyRecord(Request $request, string $type, string $record): RedirectResponse
    {
        $section = match ($type) {
            'assignment' => 'assignments',
            'return' => 'returns',
            'transfer' => 'transfers',
            'request' => 'requests',
            'purchase' => 'purchases',
            'warranty', 'claim' => 'warranties',
            'verification' => 'verification',
            'disposal' => 'disposals',
            'maintenance' => 'maintenance',
            'notification' => 'notifications',
            default => abort(404),
        };
        if (! ($type === 'request' && $request->user()->role?->slug === 'employee')
            && ! ($type === 'notification' && $request->user()->role?->slug === 'employee')) {
            $this->authorizeMutation($request->user(), $section);
        }

        switch ($type) {
            case 'assignment':
                $this->deleteAssignmentRecord($request, $record);
                break;
            case 'return':
                $this->deleteReturnRecord($request, $record);
                break;
            case 'transfer':
                $transfer = AssetTransfer::query()->with(['fromEmployee', 'toEmployee'])->findOrFail($record);
                if ($this->isDepartmentManager($request->user())) {
                    abort_unless(
                        $transfer->fromEmployee?->department_id === $this->departmentId($request->user())
                            || $transfer->toEmployee?->department_id === $this->departmentId($request->user()),
                        403,
                    );
                }
                $transfer->delete();
                break;
            case 'request':
                $this->deleteRequestRecord($request, $record);
                break;
            case 'purchase':
                $this->deletePurchaseRecord($record);
                break;
            case 'warranty':
                $this->deleteWarrantyRecord($record);
                break;
            case 'claim':
                WarrantyClaim::query()->findOrFail($record)->delete();
                break;
            case 'verification':
                $this->deleteVerificationRecord($record);
                break;
            case 'disposal':
                $this->deleteDisposalRecord($record);
                break;
            case 'maintenance':
                $this->deleteMaintenanceRecord($record);
                break;
            case 'notification':
                $request->user()->notifications()->findOrFail($record)->delete();
                break;
        }

        return to_route('operations.'.$section)->with('status', 'Record permanently deleted.');
    }

    private function updateAssignmentRecord(Request $request, int $record): void
    {
        $assignment = AssetAssignment::query()->findOrFail($record);
        $this->assertDepartmentAssignmentAccess($request, $assignment);
        $validated = $request->validate([
            'assigned_at' => ['required', 'date'],
            'remarks' => ['nullable', 'string', 'max:5000'],
        ]);
        abort_if($assignment->closed_at && Carbon::parse($validated['assigned_at'])->gt($assignment->closed_at), 422,
            'The assignment date cannot be later than its return date.');
        $assignment->update($validated);
    }

    private function updateReturnRecord(Request $request, int $record): void
    {
        $return = AssetReturn::query()->with('assetAssignment')->findOrFail($record);
        $this->assertDepartmentAssignmentAccess($request, $return->assetAssignment);
        $validated = $request->validate([
            'returned_at' => ['required', 'date', 'after_or_equal:'.$return->assetAssignment->assigned_at],
            'returned_condition' => ['required', Rule::in(self::CONDITIONS)],
            'next_status' => ['required', Rule::in(['available', 'under_maintenance', 'damaged'])],
            'remarks' => ['nullable', 'string', 'max:5000'],
        ]);
        abort_if(AssetAssignment::query()->where('asset_id', $return->asset_id)
            ->where('assigned_at', '>', $return->assetAssignment->assigned_at)->exists(), 422,
            'A return with a later assignment cannot be changed.');
        $history = AssetStatusHistory::query()->where('asset_id', $return->asset_id)
            ->where('changed_at', $return->returned_at)->latest('id')->first();
        abort_unless($history, 422, 'The return cannot be changed because its asset history record is unavailable.');
        $latestHistoryId = AssetStatusHistory::query()->where('asset_id', $return->asset_id)
            ->latest('changed_at')->latest('id')->value('id');
        abort_unless((int) $latestHistoryId === $history->id
            && $return->asset->status === $history->new_status
            && $return->asset->condition === $history->new_condition
            && $return->asset->current_employee_id === null, 422,
            'This return cannot be changed because the asset has a newer status or assignment.');

        DB::transaction(function () use ($return, $validated, $request, $history): void {
            $return->update($validated);
            $return->assetAssignment->update(['closed_at' => $validated['returned_at']]);
            $return->asset->update([
                'status' => $validated['next_status'],
                'condition' => $validated['returned_condition'],
                'updated_by' => $request->user()->id,
            ]);
            $history->update([
                'new_status' => $validated['next_status'],
                'new_condition' => $validated['returned_condition'],
                'changed_at' => $validated['returned_at'],
                'remarks' => $validated['remarks'] ?? null,
            ]);
            AssetActivity::query()->where('asset_id', $return->asset_id)
                ->where('action_type', 'returned')
                ->whereJsonContains('details->assignment_id', $return->asset_assignment_id)
                ->update(['occurred_at' => $validated['returned_at']]);
        });
    }

    private function updateTransferRecord(Request $request, int $record): void
    {
        $transfer = AssetTransfer::query()->findOrFail($record);
        if ($this->isDepartmentManager($request->user())) {
            abort_unless(
                $transfer->fromEmployee?->department_id === $this->departmentId($request->user())
                    || $transfer->toEmployee?->department_id === $this->departmentId($request->user()),
                403,
            );
        }
        $transfer->update($request->validate([
            'transferred_at' => ['required', 'date'],
            'reason' => ['required', 'string', 'max:5000'],
        ]));
    }

    private function updatePurchaseRecord(Request $request, int $record): void
    {
        $purchase = AssetPurchaseRecord::query()->findOrFail($record);
        $validated = $request->validate([
            'supplier' => ['required', 'string', 'max:255'],
            'purchase_order_reference' => ['nullable', 'string', 'max:255'],
            'purchase_cost' => ['required', 'numeric', 'min:0'],
            'received_at' => ['required', 'date'],
        ]);
        $purchase->update($validated);
        if (! AssetPurchaseRecord::query()->where('asset_id', $purchase->asset_id)
            ->where('received_at', '>', $purchase->received_at)->exists()) {
            $purchase->asset->update([
                'purchase_cost' => $validated['purchase_cost'],
                'purchase_date' => $validated['received_at'],
            ]);
        }
    }

    private function updateWarrantyRecord(Request $request, int $record): void
    {
        $warranty = AssetWarranty::query()->findOrFail($record);
        $validated = $request->validate([
            'provider' => ['required', 'string', 'max:255'],
            'starts_at' => ['required', 'date'],
            'expires_at' => ['required', 'date', 'after_or_equal:starts_at'],
        ]);
        $warranty->update([...$validated, 'reminder_days_sent' => null]);
        $latestExpiry = AssetWarranty::query()->where('asset_id', $warranty->asset_id)->max('expires_at');
        $warranty->asset->update(['warranty_expiry_date' => $latestExpiry]);
    }

    private function updateVerificationRecord(Request $request, int $record): void
    {
        $item = AssetVerificationItem::query()->with('session')->findOrFail($record);
        $validated = $request->validate([
            'finding' => ['required', Rule::in(['verified', 'missing', 'mismatch', 'unregistered'])],
            'observed_asset_code' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:5000'],
            'verified_at' => ['required', 'date'],
        ]);
        $item->update(collect($validated)->except('verified_at')->all());
        $item->session->update(['verified_at' => $validated['verified_at'], 'notes' => $validated['notes']]);
    }

    private function deleteAssignmentRecord(Request $request, int $record): void
    {
        $assignment = AssetAssignment::query()->findOrFail($record);
        $this->assertDepartmentAssignmentAccess($request, $assignment);
        abort_if($assignment->assetReturn()->exists(), 422,
            'Delete the associated return first, then delete this assignment.');
        if ($assignment->closed_at === null) {
            $latestHistory = AssetStatusHistory::query()->where('asset_id', $assignment->asset_id)
                ->latest('changed_at')->latest('id')->first();
            abort_unless($latestHistory?->new_status === 'assigned'
                && $latestHistory->changed_at->gte($assignment->assigned_at), 422,
                'This assignment cannot be deleted because the asset has newer status history.');
        }

        DB::transaction(function () use ($assignment, $request): void {
            $asset = Asset::query()->lockForUpdate()->findOrFail($assignment->asset_id);
            if ($assignment->closed_at === null) {
                abort_unless($asset->status === 'assigned', 422,
                    'This assignment cannot be deleted because the asset is no longer assigned.');
                abort_unless($asset->current_employee_id === $assignment->employee_id, 422,
                    'This assignment cannot be deleted because another employee currently holds the asset.');
                $asset->update([
                    'status' => 'available',
                    'current_employee_id' => null,
                    'updated_by' => $request->user()->id,
                ]);
            }
            $assignment->delete();
        });
    }

    private function deleteReturnRecord(Request $request, int $record): void
    {
        $return = AssetReturn::query()->with('assetAssignment')->findOrFail($record);
        $this->assertDepartmentAssignmentAccess($request, $return->assetAssignment);
        abort_if(AssetAssignment::query()->where('asset_id', $return->asset_id)
            ->where('assigned_at', '>', $return->assetAssignment->assigned_at)->exists(), 422,
            'This return cannot be deleted because the asset has a later assignment.');
        $history = AssetStatusHistory::query()->where('asset_id', $return->asset_id)
            ->where('changed_at', $return->returned_at)->latest('id')->first();
        abort_unless($history, 422, 'The return cannot be deleted because its prior asset state is unavailable.');
        $latestHistoryId = AssetStatusHistory::query()->where('asset_id', $return->asset_id)
            ->latest('changed_at')->latest('id')->value('id');
        abort_unless((int) $latestHistoryId === $history->id
            && $return->asset->status === $history->new_status
            && $return->asset->condition === $history->new_condition
            && $return->asset->current_employee_id === null, 422,
            'This return cannot be deleted because the asset has a newer status or assignment.');

        DB::transaction(function () use ($return, $history, $request): void {
            $return->asset->update([
                'status' => $history->old_status ?? 'assigned',
                'condition' => $history->old_condition ?? $return->returned_condition,
                'current_employee_id' => $return->assetAssignment->employee_id,
                'updated_by' => $request->user()->id,
            ]);
            $return->assetAssignment->update(['closed_at' => null]);
            $return->delete();
            $history->delete();
            AssetActivity::query()->where('asset_id', $return->asset_id)
                ->where('action_type', 'returned')
                ->whereJsonContains('details->assignment_id', $return->asset_assignment_id)
                ->delete();
        });
    }

    private function deleteRequestRecord(Request $request, int $record): void
    {
        $assetRequest = AssetRequest::query()->with('requester.employee')->findOrFail($record);
        if ($request->user()->role?->slug === 'employee') {
            abort_unless($assetRequest->requester_user_id === $request->user()->id
                && $assetRequest->status === 'pending', 403);
        }
        if ($this->isDepartmentManager($request->user())) {
            abort_unless($assetRequest->requester?->employee?->department_id === $this->departmentId($request->user()), 403);
        }
        $assetRequest->delete();
    }

    private function deletePurchaseRecord(int $record): void
    {
        $purchase = AssetPurchaseRecord::query()->findOrFail($record);
        $assetId = $purchase->asset_id;
        if ($purchase->invoice_path) {
            Storage::disk('local')->delete($purchase->invoice_path);
        }
        $purchase->delete();
        $latest = AssetPurchaseRecord::query()->where('asset_id', $assetId)->latest('received_at')->first();
        $purchase->asset->update([
            'purchase_cost' => $latest?->purchase_cost,
            'purchase_date' => $latest?->received_at,
        ]);
    }

    private function deleteWarrantyRecord(int $record): void
    {
        $warranty = AssetWarranty::query()->findOrFail($record);
        $asset = $warranty->asset;
        if ($warranty->document_path) {
            Storage::disk('local')->delete($warranty->document_path);
        }
        $warranty->delete();
        $asset->update([
            'warranty_expiry_date' => AssetWarranty::query()->where('asset_id', $asset->id)->max('expires_at'),
        ]);
    }

    private function deleteVerificationRecord(int $record): void
    {
        $item = AssetVerificationItem::query()->findOrFail($record);
        $session = $item->session;
        $item->delete();
        if (! $session->items()->exists()) {
            $session->delete();
        }
    }

    private function deleteDisposalRecord(int $record): void
    {
        $disposal = AssetDisposal::query()->findOrFail($record);
        abort_unless(in_array($disposal->status, ['pending', 'rejected'], true), 422,
            'Approved or completed disposals cannot be deleted.');
        $disposal->delete();
    }

    private function deleteMaintenanceRecord(int $record): void
    {
        $maintenance = MaintenanceRecord::query()->findOrFail($record);
        abort_unless($maintenance->status === 'completed', 422,
            'Only completed maintenance records can be deleted.');
        if ($maintenance->attachment_path) {
            Storage::disk('local')->delete($maintenance->attachment_path);
        }
        $maintenance->forceDelete();
    }

    private function assertDepartmentAssignmentAccess(Request $request, AssetAssignment $assignment): void
    {
        if ($this->isDepartmentManager($request->user())) {
            abort_unless($assignment->department_id === $this->departmentId($request->user()), 403);
        }
    }

    public function markNotificationRead(Request $request, string $notification): RedirectResponse
    {
        $record = $request->user()->notifications()->whereKey($notification)->firstOrFail();
        $record->markAsRead();

        return back()->with('status', 'Notification marked as read.');
    }

    public function markAllNotificationsRead(Request $request): RedirectResponse
    {
        $request->user()->unreadNotifications->markAsRead();

        return back()->with('status', 'All notifications marked as read.');
    }

    public function downloadAttachment(Request $request, string $type, int $id)
    {
        $role = $request->user()->role?->slug;
        if ($type !== 'maintenance') {
            abort_unless(in_array($role, [User::ROLE_ADMIN, User::ROLE_MANAGER, User::ROLE_ASSET_CONTROLLER], true), 403);
        }
        $path = match ($type) {
            'invoice' => AssetPurchaseRecord::query()->findOrFail($id)->invoice_path,
            'warranty' => AssetWarranty::query()->findOrFail($id)->document_path,
            'maintenance' => $this->maintenanceAttachmentPath($request, $id),
            default => abort(404),
        };
        abort_unless($path && Storage::disk('local')->exists($path), 404);

        return response()->download(Storage::disk('local')->path($path), basename($path));
    }

    public function storeRequest(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'category_id' => ['nullable', Rule::exists('categories', 'id')->where('is_active', true)],
            'department_id' => ['nullable', Rule::exists('departments', 'id')->where('is_active', true)],
            'business_reason' => ['required', 'string', 'min:10', 'max:5000'],
        ]);
        $departmentId = $request->user()->employee?->department_id ?? ($validated['department_id'] ?? null);
        AssetRequest::query()->create([
            ...$validated,
            'requester_user_id' => $request->user()->id,
            'department_id' => $departmentId,
        ]);
        $this->notifyRoleUsers(
            'asset_request',
            'New asset request',
            "{$request->user()->name} submitted an asset request for review.",
            route('operations.requests'),
            ['requester_user_id' => $request->user()->id],
            [User::ROLE_ADMIN, User::ROLE_MANAGER, User::ROLE_ASSET_CONTROLLER, User::ROLE_DEPARTMENT_MANAGER],
            $departmentId,
        );

        return to_route('employee.requests')->with('status', 'Your asset request has been submitted for approval.');
    }

    public function destroyOwnRequest(Request $request, AssetRequest $assetRequest): RedirectResponse
    {
        abort_unless($assetRequest->requester_user_id === $request->user()->id
            && $assetRequest->status === 'pending', 403);
        $assetRequest->delete();

        return to_route('employee.requests')->with('status', 'Asset request permanently deleted.');
    }

    public function updateOwnRequest(Request $request, AssetRequest $assetRequest): RedirectResponse
    {
        abort_unless($assetRequest->requester_user_id === $request->user()->id
            && $assetRequest->status === 'pending', 403);
        $validated = $request->validate([
            'category_id' => ['nullable', Rule::exists('categories', 'id')->where('is_active', true)],
            'department_id' => ['nullable', Rule::exists('departments', 'id')->where('is_active', true)],
            'business_reason' => ['required', 'string', 'min:10', 'max:5000'],
        ]);
        if ($request->user()->employee?->department_id) {
            $validated['department_id'] = $request->user()->employee->department_id;
        }
        $assetRequest->update($validated);

        return to_route('employee.requests')->with('status', 'Asset request updated.');
    }

    public function destroyOwnNotification(Request $request, string $notification): RedirectResponse
    {
        $request->user()->notifications()->whereKey($notification)->firstOrFail()->delete();

        return back()->with('status', 'Notification permanently deleted.');
    }

    public function reviewRequest(Request $request, AssetRequest $assetRequest): RedirectResponse
    {
        $this->authorizeMutation($request->user(), 'requests');
        if ($this->isDepartmentManager($request->user())) {
            abort_unless($assetRequest->requester->employee?->department_id === $this->departmentId($request->user()), 403);
        }
        $validated = $request->validate([
            'decision' => ['required', Rule::in(['approve', 'reject'])],
            'asset_id' => ['nullable', 'exists:assets,id'],
            'reviewer_remarks' => ['nullable', 'string', 'max:5000'],
        ]);

        DB::transaction(function () use ($validated, $request, $assetRequest): void {
            $assetRequest = AssetRequest::query()->lockForUpdate()->findOrFail($assetRequest->id);
            abort_unless($assetRequest->status === 'pending', 422, 'Only pending requests can be reviewed.');
            abort_if($validated['decision'] === 'reject' && ! empty($validated['asset_id']), 422,
                'An asset can only be selected when approving a request.');
            $state = $validated['decision'] === 'approve' ? 'approved' : 'rejected';
            $assetRequest->update([
                'status' => $state,
                'reviewed_by_user_id' => $request->user()->id,
                'reviewed_at' => now(),
                'reviewer_remarks' => $validated['reviewer_remarks'] ?? null,
            ]);

            if ($validated['decision'] === 'approve' && ! empty($validated['asset_id'])) {
                $asset = Asset::query()->lockForUpdate()->findOrFail($validated['asset_id']);
                abort_unless($asset->status === 'available' && $asset->current_employee_id === null, 422,
                    'The selected asset is no longer available.');
                abort_if($assetRequest->category_id && $asset->category_id !== $assetRequest->category_id, 422,
                    'The selected asset does not match the requested category.');
                $employee = Employee::query()->where('user_id', $assetRequest->requester_user_id)
                    ->where('employment_status', 'active')
                    ->whereHas('user', fn (Builder $query) => $query->where('is_active', true))
                    ->first();
                abort_unless($employee, 422,
                    'Create and link an active employee record for this user before assigning an asset.');
                if ($this->isDepartmentManager($request->user())) {
                    abort_unless($employee->department_id === $this->departmentId($request->user()), 403);
                }
                $this->assignLockedAsset($asset, $employee, $request, 'Assigned when approving asset request #'.$assetRequest->id);
                $assetRequest->update(['status' => 'fulfilled', 'fulfilled_asset_id' => $asset->id]);
            }
            if ($assetRequest->fulfilled_asset_id) {
                $this->recordActivity($assetRequest->fulfilledAsset, $request, 'request_reviewed',
                    "Asset request #{$assetRequest->id} was {$assetRequest->status}.", ['request_id' => $assetRequest->id]);
            }
            $assetRequest->requester->notify(new AssetOperationNotification(
                kind: 'asset_request_review',
                title: 'Asset request reviewed',
                message: "Your asset request was {$assetRequest->status}.",
                url: route('employee.requests'),
                details: ['request_id' => $assetRequest->id, 'status' => $assetRequest->status],
            ));
        });

        return to_route('operations.requests')->with('status', 'Asset request reviewed.');
    }

    public function fulfillRequest(Request $request, AssetRequest $assetRequest): RedirectResponse
    {
        $this->authorizeMutation($request->user(), 'requests');
        if ($this->isDepartmentManager($request->user())) {
            abort_unless($assetRequest->requester->employee?->department_id === $this->departmentId($request->user()), 403);
        }
        $validated = $request->validate(['asset_id' => ['required', 'exists:assets,id']]);

        DB::transaction(function () use ($validated, $request, $assetRequest): void {
            $assetRequest = AssetRequest::query()->lockForUpdate()->findOrFail($assetRequest->id);
            abort_unless($assetRequest->status === 'approved' && $assetRequest->fulfilled_asset_id === null, 422,
                'Only approved, unfulfilled requests can be assigned an asset.');
            $asset = Asset::query()->lockForUpdate()->findOrFail($validated['asset_id']);
            abort_unless($asset->status === 'available' && $asset->current_employee_id === null, 422,
                'The selected asset is no longer available.');
            abort_if($assetRequest->category_id && $asset->category_id !== $assetRequest->category_id, 422,
                'The selected asset does not match the requested category.');
            $employee = Employee::query()->where('user_id', $assetRequest->requester_user_id)
                ->where('employment_status', 'active')
                ->whereHas('user', fn (Builder $query) => $query->where('is_active', true))
                ->first();
            abort_unless($employee, 422, 'An active employee profile is required to fulfill this request.');
            if ($this->isDepartmentManager($request->user())) {
                abort_unless($employee->department_id === $this->departmentId($request->user()), 403);
            }

            $this->assignLockedAsset($asset, $employee, $request, 'Assigned to fulfill asset request #'.$assetRequest->id);
            $assetRequest->update(['status' => 'fulfilled', 'fulfilled_asset_id' => $asset->id]);
            $this->recordActivity($asset, $request, 'request_fulfilled',
                "Asset request #{$assetRequest->id} was fulfilled.", ['request_id' => $assetRequest->id]);
            $assetRequest->requester->notify(new AssetOperationNotification(
                kind: 'asset_request_fulfilled',
                title: 'Your asset request is fulfilled',
                message: "{$asset->asset_code} — {$asset->name} has been assigned to you.",
                url: route('employee.requests'),
                details: ['request_id' => $assetRequest->id, 'asset_id' => $asset->id],
            ));
        });

        return to_route('operations.requests')->with('status', 'Approved request fulfilled with the selected asset.');
    }

    private function assignLockedAsset(Asset $asset, Employee $employee, Request $request, string $remarks): void
    {
        $oldStatus = $asset->status;
        AssetAssignment::query()->create([
            'asset_id' => $asset->id,
            'employee_id' => $employee->id,
            'department_id' => $employee->department_id,
            'assigned_by_user_id' => $request->user()->id,
            'assigned_at' => now(),
            'remarks' => $remarks,
        ]);
        $asset->update([
            'status' => 'assigned',
            'current_employee_id' => $employee->id,
            'updated_by' => $request->user()->id,
        ]);
        $this->recordStatusChange($asset, $request, $oldStatus, 'assigned', $asset->condition, $asset->condition, $remarks);
        $this->recordActivity($asset, $request, 'assigned', "Assigned {$asset->asset_code} to fulfill an asset request.", [
            'employee_id' => $employee->id,
        ]);
        $employee->user?->notify(new AssetOperationNotification(
            kind: 'asset_assignment',
            title: 'Asset assigned to you',
            message: "{$asset->asset_code} — {$asset->name} has been assigned to you.",
            url: route('employee.dashboard'),
            details: ['asset_id' => $asset->id],
        ));
    }

    private function recordStatusChange(
        Asset $asset,
        Request $request,
        string $oldStatus,
        string $newStatus,
        string $oldCondition,
        string $newCondition,
        ?string $remarks = null,
        mixed $occurredAt = null,
    ): void {
        if ($oldStatus === $newStatus && $oldCondition === $newCondition) {
            return;
        }
        AssetStatusHistory::query()->create([
            'asset_id' => $asset->id,
            'changed_by_user_id' => $request->user()->id,
            'old_status' => $oldStatus,
            'new_status' => $newStatus,
            'old_condition' => $oldCondition,
            'new_condition' => $newCondition,
            'changed_at' => $occurredAt ?? now(),
            'remarks' => $remarks,
        ]);
    }

    private function recordActivity(
        Asset $asset,
        Request $request,
        string $action,
        string $description,
        array $details = [],
        mixed $occurredAt = null,
    ): void {
        AssetActivity::query()->create([
            'asset_id' => $asset->id,
            'user_id' => $request->user()->id,
            'action_type' => $action,
            'description' => $description,
            'details' => $details ?: null,
            'occurred_at' => $occurredAt ?? now(),
        ]);
    }

    private function notifyRoleUsers(
        string $kind,
        string $title,
        string $message,
        string $url,
        array $details = [],
        array $roles = [User::ROLE_ADMIN, User::ROLE_MANAGER, User::ROLE_ASSET_CONTROLLER],
        ?int $departmentId = null,
    ): void {
        $recipients = User::query()
            ->where('is_active', true)
            ->whereHas('role', fn (Builder $query) => $query->whereIn('slug', $roles))
            ->when($departmentId !== null && in_array(User::ROLE_DEPARTMENT_MANAGER, $roles, true), fn (Builder $query) => $query
                ->where(fn (Builder $scope) => $scope
                    ->whereHas('role', fn (Builder $role) => $role->where('slug', '!=', User::ROLE_DEPARTMENT_MANAGER))
                    ->orWhereHas('employee', fn (Builder $employee) => $employee->where('department_id', $departmentId))))
            ->get();

        Notification::send($recipients, new AssetOperationNotification($kind, $title, $message, $url, $details));
    }

    private function availableAssets(): Builder
    {
        return Asset::query()->with('category')
            ->where('status', 'available')
            ->whereNull('current_employee_id')
            ->orderBy('asset_code');
    }

    private function activeEmployees(?User $user = null): Builder
    {
        return Employee::query()->with('department')
            ->when($user && $this->isDepartmentManager($user), fn (Builder $query) => $query->where('department_id', $this->departmentId($user)))
            ->where('employment_status', 'active')
            ->where(function (Builder $query): void {
                $query->whereNull('user_id')->orWhereHas('user', fn (Builder $userQuery) => $userQuery->where('is_active', true));
            })
            ->orderBy('first_name')->orderBy('last_name');
    }

    private function authorizeSection(User $user, string $section): void
    {
        $role = $user->role?->slug;
        $fullAccessRoles = [User::ROLE_ADMIN, User::ROLE_MANAGER, User::ROLE_ASSET_CONTROLLER];
        if (in_array($role, $fullAccessRoles, true)) {
            return;
        }

        $allowedSections = match ($role) {
            User::ROLE_EMPLOYEE => ['my-requests', 'notifications'],
            User::ROLE_DEPARTMENT_MANAGER => ['assignments', 'returns', 'transfers', 'status', 'history', 'reports', 'requests', 'notifications'],
            User::ROLE_IT_TECHNICIAN => ['maintenance', 'status', 'history', 'notifications'],
            User::ROLE_HR_ADMIN_STAFF => ['people', 'notifications'],
            User::ROLE_MANAGEMENT_VIEWER => ['reports', 'notifications'],
            default => [],
        };
        abort_unless(in_array($section, $allowedSections, true), 403);
        if ($role === User::ROLE_DEPARTMENT_MANAGER) {
            $this->departmentId($user);
        }
    }

    private function authorizeMutation(User $user, string $section): void
    {
        $role = $user->role?->slug;
        $fullAccessRoles = [User::ROLE_ADMIN, User::ROLE_MANAGER, User::ROLE_ASSET_CONTROLLER];
        if (in_array($role, $fullAccessRoles, true)) {
            return;
        }

        $allowedSections = match ($role) {
            User::ROLE_DEPARTMENT_MANAGER => ['assignments', 'returns', 'transfers', 'requests'],
            User::ROLE_IT_TECHNICIAN => ['status'],
            default => [],
        };
        abort_unless(in_array($section, $allowedSections, true), 403);
    }

    private function isDepartmentManager(User $user): bool
    {
        return $user->role?->slug === User::ROLE_DEPARTMENT_MANAGER;
    }

    private function departmentId(User $user): int
    {
        $departmentId = $user->employee()->value('department_id');
        abort_unless($departmentId, 403, 'Assign this department manager to a department before granting access.');

        return (int) $departmentId;
    }

    private function departmentAssetQuery(User $user): Builder
    {
        $query = Asset::query();
        if ($this->isDepartmentManager($user)) {
            $departmentId = $this->departmentId($user);
            $query->where(function (Builder $scope) use ($departmentId): void {
                $scope->whereHas('currentEmployee', fn (Builder $employee) => $employee->where('department_id', $departmentId))
                    ->orWhereHas('assignments', fn (Builder $assignment) => $assignment->where('department_id', $departmentId));
            });
        }

        return $query;
    }

    private function departmentAssetIds(User $user): array
    {
        return $this->departmentAssetQuery($user)->pluck('id')->all();
    }

    private function maintenanceAttachmentPath(Request $request, int $id): ?string
    {
        $record = MaintenanceRecord::query()->findOrFail($id);
        if ($request->user()->role?->slug === User::ROLE_IT_TECHNICIAN) {
            abort_unless($record->assigned_to_user_id === $request->user()->id, 403);
        } else {
            abort_unless(in_array($request->user()->role?->slug, [
                User::ROLE_ADMIN,
                User::ROLE_MANAGER,
                User::ROLE_ASSET_CONTROLLER,
            ], true), 403);
        }

        return $record->attachment_path;
    }

    private function reportData(string $type, User $user): array
    {
        abort_unless(in_array($type, self::REPORT_TYPES, true), 404);
        if ($this->isDepartmentManager($user)) {
            abort_unless(in_array($type, ['assets', 'assignments', 'available', 'movements'], true), 403);
        }

        $rows = match ($type) {
            'assignments' => AssetAssignment::query()->with(['asset', 'employee', 'department', 'assignedBy'])
                ->when($this->isDepartmentManager($user), fn (Builder $query) => $query->where('department_id', $this->departmentId($user)))
                ->latest('assigned_at')->paginate(30)->withQueryString(),
            'movements' => AssetActivity::query()->with(['asset', 'user'])->latest('occurred_at')
                ->when($this->isDepartmentManager($user), fn (Builder $query) => $query->whereIn('asset_id', $this->departmentAssetIds($user)))
                ->paginate(30)->withQueryString(),
            'department' => Department::query()->withCount(['assetAssignments as assigned_assets_count' => fn (Builder $query) => $query->whereNull('closed_at')])
                ->orderBy('name')->paginate(30)->withQueryString(),
            'location' => Location::query()->withCount('assets')->orderBy('name')->paginate(30)->withQueryString(),
            'category' => Category::query()->withCount('assets')->orderBy('name')->paginate(30)->withQueryString(),
            'available' => $this->availableAssets()
                ->when($this->isDepartmentManager($user), fn (Builder $query) => $query->whereIn('id', $this->departmentAssetIds($user)))
                ->paginate(30)->withQueryString(),
            default => $this->departmentAssetQuery($user)->with(['category', 'location', 'currentEmployee'])->orderBy('asset_code')->paginate(30)->withQueryString(),
        };

        return ['reportRows' => $rows, 'reportTypes' => self::REPORT_TYPES];
    }

    private function downloadReport(Request $request): StreamedResponse
    {
        $type = $this->resolveReportType($request);
        $user = $request->user();
        if ($this->isDepartmentManager($user)) {
            abort_unless(in_array($type, ['assets', 'assignments', 'available', 'movements'], true), 403);
        }
        $query = match ($type) {
            'assignments' => AssetAssignment::query()->with(['asset', 'employee', 'department', 'assignedBy'])
                ->when($this->isDepartmentManager($user), fn (Builder $query) => $query->where('department_id', $this->departmentId($user)))
                ->latest('assigned_at'),
            'movements' => AssetActivity::query()->with(['asset', 'user'])
                ->when($this->isDepartmentManager($user), fn (Builder $query) => $query->whereIn('asset_id', $this->departmentAssetIds($user)))
                ->latest('occurred_at'),
            'department' => Department::query()->withCount(['assetAssignments as assigned_assets_count' => fn (Builder $builder) => $builder->whereNull('closed_at')])->orderBy('name'),
            'location' => Location::query()->withCount('assets')->orderBy('name'),
            'category' => Category::query()->withCount('assets')->orderBy('name'),
            'available' => $this->availableAssets()
                ->when($this->isDepartmentManager($user), fn (Builder $query) => $query->whereIn('id', $this->departmentAssetIds($user))),
            default => $this->departmentAssetQuery($user)->with(['category', 'location', 'currentEmployee'])->orderBy('asset_code'),
        };
        $filename = "assetflow-{$type}-".now()->format('Ymd-His').'.csv';

        return response()->streamDownload(function () use ($query, $type): void {
            $stream = fopen('php://output', 'w');
            if ($stream === false) {
                throw new \RuntimeException('Unable to open the report output stream.');
            }
            $headers = match ($type) {
                'assignments' => ['Asset code', 'Asset', 'Employee', 'Department', 'Assigned by', 'Assigned at', 'Returned at'],
                'movements' => ['Asset code', 'Asset', 'Action', 'Description', 'Responsible user', 'Date'],
                'department' => ['Department', 'Current assignments'],
                'location' => ['Location', 'Assets'],
                'category' => ['Category', 'Assets'],
                default => ['Asset code', 'Asset', 'Category', 'Location', 'Holder', 'Status', 'Condition'],
            };
            fputcsv($stream, $headers, ',', '"', '');
            $query->chunk(500, function ($rows) use ($stream, $type): void {
                foreach ($rows as $row) {
                    fputcsv($stream, match ($type) {
                        'assignments' => [
                            $row->asset?->asset_code, $row->asset?->name, trim(($row->employee?->first_name ?? '').' '.($row->employee?->last_name ?? '')),
                            $row->department?->name, $row->assignedBy?->name, $row->assigned_at?->toDateTimeString(), $row->closed_at?->toDateTimeString(),
                        ],
                        'movements' => [
                            $row->asset?->asset_code, $row->asset?->name, $row->action_type, $row->description, $row->user?->name, $row->occurred_at?->toDateTimeString(),
                        ],
                        'department' => [$row->name, $row->assigned_assets_count],
                        'location' => [$row->name, $row->assets_count],
                        'category' => [$row->name, $row->assets_count],
                        default => [
                            $row->asset_code, $row->name, $row->category?->name, $row->location?->name,
                            trim(($row->currentEmployee?->first_name ?? '').' '.($row->currentEmployee?->last_name ?? '')),
                            $row->status, $row->condition,
                        ],
                    }, ',', '"', '');
                }
            });
            fclose($stream);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    private function resolveReportType(Request $request): string
    {
        $type = $request->query('type', 'assets');
        abort_unless(is_string($type) && in_array($type, self::REPORT_TYPES, true), 404);

        return $type;
    }
}
