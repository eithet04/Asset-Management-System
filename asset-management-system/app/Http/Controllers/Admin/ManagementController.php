<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\AssetActivity;
use App\Models\AssetStatusHistory;
use App\Models\Branch;
use App\Models\Category;
use App\Models\CompanySetting;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Location;
use App\Models\MaintenanceRecord;
use App\Models\Role;
use App\Models\User;
use App\Notifications\AssetOperationNotification;
use App\Support\MyanmarPhoneNumber;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ManagementController extends Controller
{
    public function index(string $section = 'categories'): View
    {
        $pageTitles = [
            'categories' => 'Categories',
            'locations' => 'Locations',
            'branches' => 'Branches',
            'departments' => 'Departments',
            'users' => 'User roles',
            'maintenance' => 'Maintenance',
            'settings' => 'Company settings',
        ];

        abort_unless(isset($pageTitles[$section]), 404);

        $data = match ($section) {
            'categories' => [
                'categories' => Category::query()->withCount('assets')->orderBy('name')->get(),
            ],
            'locations' => [
                'locations' => Location::query()->with('branch')->withCount('assets')->orderBy('name')->get(),
                'branches' => Branch::query()->orderBy('name')->get(),
            ],
            'branches' => [
                'branches' => Branch::query()->withCount(['locations', 'employees'])->orderBy('name')->get(),
            ],
            'departments' => [
                'departments' => Department::query()->withCount(['employees', 'assetAssignments'])->orderBy('name')->get(),
            ],
            'users' => [
                'users' => User::query()->with(['role', 'employee.department'])->orderBy('name')->get(),
                'roles' => Role::query()->orderBy('name')->get(),
                'departments' => Department::query()->where('is_active', true)->orderBy('name')->get(),
            ],
            'maintenance' => [
                'assets' => Asset::query()->orderBy('asset_code')->get(),
                'maintenanceRecords' => MaintenanceRecord::query()
                    ->with(['asset', 'assignedTo'])
                    ->latest('reported_at')
                    ->get(),
                'technicians' => User::query()->where('is_active', true)->orderBy('name')->get(),
            ],
            'settings' => [
                'settings' => CompanySetting::query()->firstOrCreate(['id' => 1]),
            ],
        };

        return view('admin.management', [
            ...$data,
            'section' => $section,
            'pageTitle' => $pageTitles[$section],
        ]);
    }

    public function storeCategory(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:categories,name'],
        ]);

        Category::query()->create([
            'name' => $validated['name'],
            'code' => $this->categoryCodeFor($validated['name']),
        ]);

        return $this->backTo('categories', 'Category created.');
    }

    public function updateCategory(Request $request, Category $category): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('categories', 'name')->ignore($category->id)],
            'is_active' => ['required', 'boolean'],
        ]);

        $category->update($validated);

        return $this->backTo('categories', 'Category updated.');
    }

    public function destroyCategory(Category $category): RedirectResponse
    {
        $category->update(['is_active' => false]);

        return $this->backTo('categories', 'Category deleted from active use. Existing asset records were retained.');
    }

    private function categoryCodeFor(string $name): string
    {
        $base = Str::upper(Str::slug($name, '-')) ?: 'CATEGORY';
        $code = $base;
        $suffix = 2;

        while (Category::query()->where('code', $code)->exists()) {
            $code = $base.'-'.$suffix++;
        }

        return $code;
    }

    public function storeLocation(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'location_type' => ['required', Rule::in(['branch', 'office', 'room', 'storage', 'other'])],
            'branch_id' => ['nullable', Rule::exists('branches', 'id')->where('is_active', true)],
        ]);

        Location::query()->create($validated);

        return $this->backTo('locations', 'Location created.');
    }

    public function updateLocation(Request $request, Location $location): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'location_type' => ['required', Rule::in(['branch', 'office', 'room', 'storage', 'other'])],
            'branch_id' => [
                'nullable',
                Rule::exists('branches', 'id')->where(function ($query) use ($location): void {
                    $query->where('is_active', true)->orWhere('id', $location->branch_id);
                }),
            ],
            'is_active' => ['required', 'boolean'],
        ]);

        $location->update($validated);

        return $this->backTo('locations', 'Location updated.');
    }

    public function destroyLocation(Location $location): RedirectResponse
    {
        $location->update(['is_active' => false]);

        return $this->backTo('locations', 'Location deleted from active use. Existing asset records were retained.');
    }

    public function storeDepartment(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:departments,name'],
            'description' => ['nullable', 'string', 'max:5000'],
        ]);

        Department::query()->create([
            ...$validated,
            'code' => $this->uniqueCodeFor(Department::class, $validated['name']),
        ]);

        return $this->backTo('departments', 'Department created.');
    }

    public function updateDepartment(Request $request, Department $department): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('departments', 'name')->ignore($department->id)],
            'description' => ['nullable', 'string', 'max:5000'],
            'is_active' => ['required', 'boolean'],
        ]);

        $department->update($validated);

        return $this->backTo('departments', 'Department updated.');
    }

    public function destroyDepartment(Department $department): RedirectResponse
    {
        $department->update(['is_active' => false]);

        return $this->backTo('departments', 'Department deactivated. Existing employee and assignment records were retained.');
    }

    public function storeBranch(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:255'],
        ]);

        Branch::query()->create([
            ...$validated,
            'code' => $this->uniqueCodeFor(Branch::class, $validated['name']),
        ]);

        return $this->backTo('branches', 'Branch created.');
    }

    public function updateBranch(Request $request, Branch $branch): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:255'],
            'is_active' => ['required', 'boolean'],
        ]);

        $branch->update($validated);

        return $this->backTo('branches', 'Branch updated.');
    }

    public function destroyBranch(Branch $branch): RedirectResponse
    {
        $branch->update(['is_active' => false]);

        return $this->backTo('branches', 'Branch deactivated. Existing location and employee records were retained.');
    }

    /**
     * @param  class-string<Department|Branch>  $model
     */
    private function uniqueCodeFor(string $model, string $name): string
    {
        $base = Str::upper(Str::slug($name, '-')) ?: 'RECORD';
        $code = $base;
        $suffix = 2;

        while ($model::query()->where('code', $code)->exists()) {
            $code = $base.'-'.$suffix++;
        }

        return $code;
    }

    public function updateUser(Request $request, User $user): RedirectResponse
    {
        $phoneProvided = $request->exists('phone');
        $phone = trim((string) $request->input('phone', ''));
        $request->merge([
            'phone' => $phone === '' ? null : (MyanmarPhoneNumber::normalize($phone) ?? $phone),
        ]);
        $validated = $request->validate([
            'role_id' => ['required', 'exists:roles,id'],
            'is_active' => ['required', 'boolean'],
            'department_id' => ['nullable', 'exists:departments,id'],
            'phone' => [
                'nullable',
                'regex:/^\+959[2-9]\d{6,8}$/',
                Rule::unique('users', 'phone')->ignore($user->id),
            ],
        ]);

        if ($user->is($request->user()) && (! (bool) $validated['is_active'] || $user->role_id != $validated['role_id'])) {
            return $this->backTo('users', 'You cannot deactivate your own account or change your own role.');
        }

        DB::transaction(function () use ($validated, $user, $phoneProvided): void {
            $user = User::query()->lockForUpdate()->findOrFail($user->id);
            $wasActiveAdmin = $user->is_active && $user->role?->slug === User::ROLE_ADMIN;
            $willBeActiveAdmin = (bool) $validated['is_active']
                && Role::query()->whereKey($validated['role_id'])->value('slug') === User::ROLE_ADMIN;
            $willHaveEmployeeProfile = Role::query()->whereKey($validated['role_id'])->value('slug') !== User::ROLE_ADMIN;

            if ($wasActiveAdmin && ! $willBeActiveAdmin) {
                $activeAdmins = User::query()
                    ->where('is_active', true)
                    ->whereHas('role', fn ($query) => $query->where('slug', User::ROLE_ADMIN))
                    ->count();

                abort_if($activeAdmins <= 1, 422, 'At least one active administrator account must remain.');
            }

            $userFields = [
                'role_id' => $validated['role_id'],
                'is_active' => $validated['is_active'],
            ];
            if ($phoneProvided) {
                $userFields['phone'] = $validated['phone'] ?? null;
            }
            $user->update($userFields);
            if ($willHaveEmployeeProfile) {
                $profile = Employee::createForUser($user);
                $profileFields = [];
                if (array_key_exists('department_id', $validated)) {
                    $profileFields['department_id'] = $validated['department_id'];
                }
                if ($phoneProvided) {
                    $profileFields['phone'] = $validated['phone'] ?? null;
                }
                if ($profileFields !== []) {
                    $profile->update($profileFields);
                }
            }
        });

        return $this->backTo('users', 'User access updated.');
    }

    public function destroyUser(Request $request, User $user): RedirectResponse
    {
        abort_unless($user->isNot($request->user()), 422, 'You cannot deactivate your own account.');

        DB::transaction(function () use ($user): void {
            $user = User::query()->lockForUpdate()->findOrFail($user->id);
            if ($user->is_active && $user->role?->slug === User::ROLE_ADMIN) {
                $activeAdmins = User::query()
                    ->where('is_active', true)
                    ->whereHas('role', fn ($query) => $query->where('slug', User::ROLE_ADMIN))
                    ->count();

                abort_if($activeAdmins <= 1, 422, 'At least one active administrator account must remain.');
            }

            $user->update(['is_active' => false]);
        });

        return $this->backTo('users', 'User account deactivated.');
    }

    public function storeMaintenance(Request $request): RedirectResponse
    {
        abort_unless(in_array($request->user()->role?->slug, [
            User::ROLE_ADMIN,
            User::ROLE_MANAGER,
            User::ROLE_ASSET_CONTROLLER,
            User::ROLE_IT_TECHNICIAN,
        ], true), 403);
        $validated = $request->validate([
            'asset_id' => ['required', 'exists:assets,id'],
            'description' => ['required', 'string', 'max:5000'],
            'assigned_to_user_id' => ['nullable', Rule::exists('users', 'id')->where('is_active', true)],
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:10240'],
        ]);
        if ($request->user()->role?->slug === User::ROLE_IT_TECHNICIAN) {
            abort_unless(empty($validated['assigned_to_user_id']) || (int) $validated['assigned_to_user_id'] === $request->user()->id, 403);
            $validated['assigned_to_user_id'] = $request->user()->id;
        }

        DB::transaction(function () use ($validated, $request): void {
            $asset = Asset::query()->lockForUpdate()->findOrFail($validated['asset_id']);

            abort_if(
                in_array($asset->status, ['disposed', 'under_maintenance'], true)
                    || MaintenanceRecord::query()
                        ->where('asset_id', $asset->id)
                        ->whereIn('status', ['requested', 'in_progress'])
                        ->exists(),
                422,
                'This asset is disposed or already has an open maintenance record.',
            );

            $record = MaintenanceRecord::query()->create([
                ...$validated,
                'created_by_user_id' => $request->user()->id,
                'status' => 'requested',
                'reported_at' => now(),
                'attachment_path' => $request->file('photo')?->store('maintenance-photos', 'local'),
            ]);
            $oldStatus = $asset->status;
            $asset->update([
                'status' => 'under_maintenance',
                'updated_by' => $request->user()->id,
            ]);
            AssetStatusHistory::query()->create([
                'asset_id' => $asset->id,
                'changed_by_user_id' => $request->user()->id,
                'old_status' => $oldStatus,
                'new_status' => 'under_maintenance',
                'old_condition' => $asset->condition,
                'new_condition' => $asset->condition,
                'changed_at' => now(),
                'remarks' => "Maintenance record #{$record->id} requested.",
            ]);

            AssetActivity::query()->create([
                'asset_id' => $asset->id,
                'user_id' => $request->user()->id,
                'action_type' => 'maintenance_requested',
                'description' => "Maintenance requested for {$asset->asset_code}.",
                'details' => ['maintenance_record_id' => $record->id],
                'occurred_at' => now(),
            ]);
            $this->notifyMaintenanceParticipants(
                $record,
                'Maintenance request',
                "Maintenance requested for {$asset->asset_code}.",
            );
        });

        return to_route($request->user()->role?->slug === User::ROLE_ADMIN ? 'admin.maintenance' : 'operations.maintenance')
            ->with('status', 'Maintenance request recorded.');
    }

    public function updateMaintenance(Request $request, MaintenanceRecord $maintenanceRecord): RedirectResponse
    {
        $role = $request->user()->role?->slug;
        abort_unless(in_array($role, [
            User::ROLE_ADMIN,
            User::ROLE_MANAGER,
            User::ROLE_ASSET_CONTROLLER,
            User::ROLE_IT_TECHNICIAN,
        ], true), 403);
        if ($role === User::ROLE_IT_TECHNICIAN) {
            abort_unless(
                $maintenanceRecord->assigned_to_user_id === $request->user()->id || $maintenanceRecord->assigned_to_user_id === null,
                403,
            );
        }
        $validated = $request->validate([
            'status' => ['required', Rule::in(['requested', 'in_progress', 'completed'])],
            'assigned_to_user_id' => ['nullable', Rule::exists('users', 'id')->where('is_active', true)],
            'cost' => ['nullable', 'numeric', 'min:0'],
            'completed_at' => ['nullable', 'date'],
        ]);
        if ($role === User::ROLE_IT_TECHNICIAN) {
            abort_unless(empty($validated['assigned_to_user_id']) || (int) $validated['assigned_to_user_id'] === $request->user()->id, 403);
            $validated['assigned_to_user_id'] = $request->user()->id;
        }

        DB::transaction(function () use ($validated, $maintenanceRecord, $request): void {
            $record = MaintenanceRecord::query()->lockForUpdate()->findOrFail($maintenanceRecord->id);
            if ($request->user()->role?->slug === User::ROLE_IT_TECHNICIAN) {
                abort_unless(
                    $record->assigned_to_user_id === $request->user()->id || $record->assigned_to_user_id === null,
                    403,
                );
            }
            $asset = Asset::query()->lockForUpdate()->findOrFail($record->asset_id);
            abort_if(
                $validated['status'] !== 'completed'
                    && $asset->status === 'disposed',
                422,
                'Disposed assets cannot have open maintenance work.',
            );
            abort_if(
                in_array($validated['status'], ['requested', 'in_progress'], true)
                    && MaintenanceRecord::query()
                        ->where('asset_id', $asset->id)
                        ->where('id', '!=', $record->id)
                        ->whereIn('status', ['requested', 'in_progress'])
                        ->exists(),
                422,
                'This asset already has another open maintenance record.',
            );
            $oldStatus = $asset->status;
            $oldCondition = $asset->condition;
            $record->update([
                'assigned_to_user_id' => $validated['assigned_to_user_id'] ?? null,
                'cost' => $validated['cost'] ?? null,
                'status' => $validated['status'],
                'completed_at' => $validated['status'] === 'completed'
                    ? ($validated['completed_at'] ?? $record->completed_at ?? now())
                    : null,
            ]);

            if (in_array($validated['status'], ['requested', 'in_progress'], true)) {
                $newAssetStatus = 'under_maintenance';
            } elseif ($validated['status'] === 'completed') {
                $newAssetStatus = $asset->current_employee_id === null ? 'available' : 'assigned';
            } else {
                $newAssetStatus = $asset->current_employee_id === null ? 'available' : 'assigned';
            }

            if ($asset->status !== $newAssetStatus) {
                $asset->update([
                    'status' => $newAssetStatus,
                    'updated_by' => $request->user()->id,
                ]);

                AssetStatusHistory::query()->create([
                    'asset_id' => $asset->id,
                    'changed_by_user_id' => $request->user()->id,
                    'old_status' => $oldStatus,
                    'new_status' => $newAssetStatus,
                    'old_condition' => $oldCondition,
                    'new_condition' => $asset->condition,
                    'changed_at' => now(),
                    'remarks' => "Maintenance record #{$record->id} updated.",
                ]);
            }

            AssetActivity::query()->create([
                'asset_id' => $asset->id,
                'user_id' => $request->user()->id,
                'action_type' => 'maintenance_'.$validated['status'],
                'description' => "Maintenance record #{$record->id} marked {$validated['status']}.",
                'details' => ['maintenance_record_id' => $record->id],
                'occurred_at' => now(),
            ]);
            $this->notifyMaintenanceParticipants(
                $record,
                'Maintenance request updated',
                "Maintenance for {$asset->asset_code} is now {$record->status}.",
            );
        });

        return to_route($request->user()->role?->slug === User::ROLE_ADMIN ? 'admin.maintenance' : 'operations.maintenance')
            ->with('status', 'Maintenance record updated.');
    }

    public function destroyMaintenance(MaintenanceRecord $maintenanceRecord): RedirectResponse
    {
        abort_unless($maintenanceRecord->status === 'completed', 422, 'Complete the maintenance work before deleting its record.');
        if ($maintenanceRecord->attachment_path) {
            Storage::disk('local')->delete($maintenanceRecord->attachment_path);
        }
        $maintenanceRecord->forceDelete();

        return $this->backTo('maintenance', 'Completed maintenance record permanently deleted.');
    }

    private function notifyMaintenanceParticipants(MaintenanceRecord $record, string $title, string $message): void
    {
        $roleUsers = User::query()
            ->where('is_active', true)
            ->whereHas('role', fn ($query) => $query->whereIn('slug', [User::ROLE_ADMIN, User::ROLE_MANAGER]))
            ->get();
        $recipients = collect([$record->createdBy, $record->assignedTo])
            ->filter()
            ->merge($roleUsers)
            ->unique('id')
            ->values();
        Notification::send($recipients, new AssetOperationNotification(
            kind: 'maintenance_update',
            title: $title,
            message: $message,
            url: route('operations.maintenance'),
            details: ['maintenance_record_id' => $record->id, 'asset_id' => $record->asset_id],
        ));
    }

    public function updateSettings(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'company_name' => ['required', 'string', 'max:255'],
            'contact_email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'timezone' => ['required', 'timezone'],
            'currency' => ['required', 'string', 'size:3', 'alpha'],
        ]);

        CompanySetting::query()->updateOrCreate(['id' => 1], $validated);

        return $this->backTo('settings', 'Company settings saved.');
    }

    private function backTo(string $section, string $message): RedirectResponse
    {
        $route = match ($section) {
            'categories' => 'admin.categories',
            'locations' => 'admin.locations',
            'branches' => 'admin.branches',
            'departments' => 'admin.departments',
            'users' => 'admin.users',
            'maintenance' => 'admin.maintenance',
            'settings' => 'admin.settings',
        };

        return to_route($route)
            ->with('status', $message);
    }
}
