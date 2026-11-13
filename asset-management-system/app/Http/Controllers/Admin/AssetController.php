<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Assets\ImportAssetsFromCsv;
use App\Actions\Assets\RegisterAsset;
use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\AssetActivity;
use App\Models\Category;
use App\Models\Location;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use RuntimeException;
use Symfony\Component\HttpFoundation\StreamedResponse;

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

        $assets = $this->applyAssetFilters(
            Asset::query()->with(['category', 'location', 'currentEmployee']),
            $validated,
        )
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
                ->limit(3)
                ->get(),
            'filters' => $validated,
        ]);
    }

    public function store(Request $request, RegisterAsset $registerAsset): RedirectResponse
    {
        $validated = $request->validate([
            'asset_code' => ['required', 'string', 'max:255', 'unique:assets,asset_code'],
            'name' => ['required', 'string', 'max:255'],
            'category_id' => ['required', Rule::exists('categories', 'id')->where('is_active', true)],
            'location_id' => ['nullable', Rule::exists('locations', 'id')->where('is_active', true)],
            'serial_number' => ['nullable', 'string', 'max:255', 'unique:assets,serial_number'],
            'purchase_date' => ['nullable', 'date'],
            'purchase_cost' => ['nullable', 'numeric', 'min:0'],
            'warranty_expiry_date' => ['nullable', 'date'],
            'condition' => ['required', Rule::in(['good', 'fair', 'poor'])],
            'status' => ['required', Rule::in(['available', 'under_maintenance', 'damaged', 'disposed'])],
        ]);

        DB::transaction(function () use ($validated, $request, $registerAsset): void {
            $registerAsset->handle($validated, $request->user()->id);
        });

        return to_route('admin.dashboard')
            ->with('asset_form_success', 'Asset added to the registry.');
    }

    public function import(Request $request, ImportAssetsFromCsv $importAssets): RedirectResponse
    {
        $validated = $request->validateWithBag('import', [
            'csv_file' => ['required', 'file', 'mimes:csv,txt', 'extensions:csv', 'max:10240'],
        ]);
        $importedCount = $importAssets->handle($validated['csv_file'], $request->user()->id);

        return to_route('admin.dashboard')
            ->with('status', "Imported {$importedCount} assets from CSV.");
    }

    public function export(Request $request): StreamedResponse
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
        $assets = $this->applyAssetFilters(
            Asset::query()->with(['category', 'location']),
            $validated,
        )->lazyById(500);

        return response()->streamDownload(function () use ($assets): void {
            $stream = fopen('php://output', 'wb');

            if ($stream === false) {
                throw new RuntimeException('Unable to open the CSV output stream.');
            }

            try {
                fwrite($stream, "\xEF\xBB\xBF");
                fputcsv($stream, [
                    'Asset',
                    'Asset code',
                    'Category',
                    'Serial number',
                    'Location',
                    'Purchase Date',
                    'Purchase Cost',
                    'Warranty expiry',
                    'Condition',
                    'Status',
                ], ',', '"', '');

                foreach ($assets as $asset) {
                    fputcsv($stream, [
                        $this->spreadsheetSafeCsvValue($asset->name),
                        $this->spreadsheetSafeCsvValue($asset->asset_code),
                        $this->spreadsheetSafeCsvValue($asset->category?->name),
                        $this->spreadsheetSafeCsvValue($asset->serial_number),
                        $this->spreadsheetSafeCsvValue($asset->location?->name),
                        $this->spreadsheetSafeCsvValue($asset->purchase_date?->format('Y-m-d')),
                        $this->spreadsheetSafeCsvValue($asset->purchase_cost),
                        $this->spreadsheetSafeCsvValue($asset->warranty_expiry_date?->format('Y-m-d')),
                        $this->spreadsheetSafeCsvValue(Str::headline($asset->condition)),
                        $this->spreadsheetSafeCsvValue(Str::headline($asset->status)),
                    ], ',', '"', '');
                }
            } finally {
                fclose($stream);
            }
        }, 'asset-registry.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
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
            'purchase_date' => ['nullable', 'date'],
            'purchase_cost' => ['nullable', 'numeric', 'min:0'],
            'warranty_expiry_date' => ['nullable', 'date'],
            'condition' => ['required', Rule::in(['good', 'fair', 'poor'])],
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
            ->withInput([...$validated, '_method' => 'PUT', 'asset_id' => $asset->id])
            ->with('asset_form_success', 'Asset details updated.');
    }

    /**
     * @param  array{q?: string|null, status?: string|null}  $filters
     */
    private function applyAssetFilters(Builder $query, array $filters): Builder
    {
        return $query
            ->when($filters['q'] ?? null, function (Builder $query, string $search): void {
                $query->where(function (Builder $query) use ($search): void {
                    $query->where('asset_code', 'like', "%{$search}%")
                        ->orWhere('name', 'like', "%{$search}%")
                        ->orWhere('serial_number', 'like', "%{$search}%")
                        ->orWhereHas('category', fn (Builder $query) => $query->where('name', 'like', "%{$search}%"));
                });
            })
            ->when($filters['status'] ?? null, fn (Builder $query, string $status) => $query->where('status', $status));
    }

    private function spreadsheetSafeCsvValue(?string $value): string
    {
        $value ??= '';

        return preg_match('/^[\t\r ]*[=+\-@]/', $value) === 1 ? "'".$value : $value;
    }
}
