<?php

namespace App\Actions\Assets;

use App\Models\Asset;
use App\Models\AssetActivity;
use App\Models\AssetStatusHistory;

class RegisterAsset
{
    /**
     * @param array{
     *     asset_code: string,
     *     name: string,
     *     category_id: int|string,
     *     location_id?: int|string|null,
     *     serial_number?: string|null,
     *     purchase_date?: string|null,
     *     purchase_cost?: string|int|float|null,
     *     warranty_expiry_date?: string|null,
     *     condition: string,
     *     status: string
     * } $attributes
     */
    public function handle(array $attributes, int $userId): Asset
    {
        $asset = Asset::query()->create([
            ...$attributes,
            'created_by' => $userId,
            'updated_by' => $userId,
        ]);

        AssetActivity::query()->create([
            'asset_id' => $asset->id,
            'user_id' => $userId,
            'action_type' => 'registered',
            'description' => "Registered asset {$asset->asset_code}.",
            'details' => ['asset_code' => $asset->asset_code, 'status' => $asset->status],
            'occurred_at' => now(),
        ]);

        AssetStatusHistory::query()->create([
            'asset_id' => $asset->id,
            'changed_by_user_id' => $userId,
            'new_status' => $asset->status,
            'new_condition' => $asset->condition,
            'changed_at' => now(),
            'remarks' => 'Initial asset registration.',
        ]);

        return $asset;
    }
}
