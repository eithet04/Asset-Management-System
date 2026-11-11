<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\AssetActivity;
use App\Models\AssetStatusHistory;
use App\Models\Category;
use App\Models\Location;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AssetController extends Controller
{
    public function index(Request $request): View
    {
        $validated = $request->validate([
            'q' => ['nullable', 'string', 'max:255'],
            'status' => ['nullable', Rule::in([
                'available',
                'assigned',
                'under_maintenance',
                'damaged',
                'disposed',
            ])],
        ]);

        $assets = Asset::query()
            ->with(['category', 'location', 'currentEmployee'])
            ->when($validated['q'] ?? null, function ($query, string $search): void {
                $query->where(function ($query) use ($search): void {
                    $query->where('asset_code', 'like', "%{$search}%")
                        ->orWhere('name', 'like', "%{$search}%")
                        ->orWhere('serial_number', 'like', "%{$search}%")
                        ->orWhereHas('category', fn ($query) => $query->where('name', 'like', "%{$search}%"));
                });
            })
            ->when($validated['status'] ?? null, fn ($query, string $status) => $query->where('status', $status))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.dashboard', [
            'assets' => $assets,
            'categories' => Category::query()
                ->where('is_active', true)
                ->when(old('category_id'), fn ($query, $id) => $query->orWhereKey($id))
                ->orderBy('name')
                ->get(),
            'locations' => Location::query()
                ->where('is_active', true)
                ->when(old('location_id'), fn ($query, $id) => $query->orWhereKey($id))
                ->orderBy('name')
                ->get(),
            'totalAssets' => Asset::query()->count(),
            'availableAssets' => Asset::query()->where('status', 'available')->count(),
            'assignedAssets' => Asset::query()->where('status', 'assigned')->count(),
            'maintenanceAssets' => Asset::query()->where('status', 'under_maintenance')->count(),
            'damagedAssets' => Asset::query()->where('status', 'damaged')->count(),
            'disposedAssets' => Asset::query()->where('status', 'disposed')->count(),
            'recentActivities' => AssetActivity::query()
                ->with(['asset', 'user'])
                ->where('action_type', 'not like', 'warranty%')
                ->latest('occurred_at')
                ->limit(8)
                ->get(),
            'filters' => $validated,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'asset_code' => ['required', 'string', 'max:255', 'unique:assets,asset_code'],
            'name' => ['required', 'string', 'max:255'],
            'category_id' => ['required', Rule::exists('categories', 'id')->where('is_active', true)],
            'location_id' => ['nullable', Rule::exists('locations', 'id')->where('is_active', true)],
            'brand' => ['nullable', 'string', 'max:255'],
            'model' => ['nullable', 'string', 'max:255'],
            'serial_number' => ['nullable', 'string', 'max:255', 'unique:assets,serial_number'],
            'purchase_date' => ['nullable', 'date'],
            'purchase_cost' => ['nullable', 'numeric', 'min:0'],
            'warranty_expiry_date' => ['nullable', 'date'],
            'condition' => ['required', Rule::in(['good', 'fair', 'poor'])],
            'status' => ['required', Rule::in(['available', 'under_maintenance', 'damaged', 'disposed'])],
            'description' => ['nullable', 'string', 'max:5000'],
        ]);

        DB::transaction(function () use ($validated, $request): void {
            $asset = Asset::query()->create([
                ...$validated,
                'created_by' => $request->user()->id,
                'updated_by' => $request->user()->id,
            ]);

            AssetActivity::query()->create([
                'asset_id' => $asset->id,
                'user_id' => $request->user()->id,
                'action_type' => 'registered',
                'description' => "Registered asset {$asset->asset_code}.",
                'details' => ['asset_code' => $asset->asset_code, 'status' => $asset->status],
                'occurred_at' => now(),
            ]);

            AssetStatusHistory::query()->create([
                'asset_id' => $asset->id,
                'changed_by_user_id' => $request->user()->id,
                'new_status' => $asset->status,
                'new_condition' => $asset->condition,
                'changed_at' => now(),
                'remarks' => 'Initial asset registration.',
            ]);
        });

        return to_route('admin.dashboard')
            ->with('status', 'Asset added to the registry.');
    }

    public function update(Request $request, Asset $asset): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'category_id' => ['required', Rule::exists('categories', 'id')->where(
                fn ($query) => $query->where('is_active', true)->orWhere('id', $asset->category_id)
            )],
            'location_id' => ['nullable', Rule::exists('locations', 'id')->where(
                fn ($query) => $query->where('is_active', true)->orWhere('id', $asset->location_id)
            )],
            'asset_code' => ['required', 'string', 'max:255', Rule::unique('assets', 'asset_code')->ignore($asset->id)],
            'serial_number' => ['nullable', 'string', 'max:255', Rule::unique('assets', 'serial_number')->ignore($asset->id)],
            'brand' => ['nullable', 'string', 'max:255'],
            'model' => ['nullable', 'string', 'max:255'],
            'purchase_date' => ['nullable', 'date'],
            'purchase_cost' => ['nullable', 'numeric', 'min:0'],
            'warranty_expiry_date' => ['nullable', 'date'],
            'condition' => ['required', Rule::in(['good', 'fair', 'poor'])],
            'description' => ['nullable', 'string', 'max:5000'],
        ]);

        DB::transaction(function () use ($validated, $request, $asset): void {
            $asset = Asset::query()->lockForUpdate()->findOrFail($asset->id);
            $asset->fill([...$validated, 'updated_by' => $request->user()->id]);
            $changes = $asset->getDirty();
            $changedFields = array_values(array_diff(array_keys($changes), ['updated_by']));

            if ($changedFields === []) {
                return;
            }

            $asset->save();
            AssetActivity::query()->create([
                'asset_id' => $asset->id,
                'user_id' => $request->user()->id,
                'action_type' => 'updated',
                'description' => "Updated {$asset->asset_code}: ".implode(', ', $changedFields).'.',
                'details' => ['changed_fields' => $changedFields],
                'occurred_at' => now(),
            ]);
        });

        return to_route('admin.dashboard')
            ->with('status', 'Asset details updated.');
    }
}
