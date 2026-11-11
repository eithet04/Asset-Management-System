<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['asset_id', 'recorded_by_user_id', 'supplier', 'purchase_order_reference', 'invoice_path', 'purchase_cost', 'received_at'])]
class AssetPurchaseRecord extends Model
{
    protected function casts(): array
    {
        return ['received_at' => 'date', 'purchase_cost' => 'decimal:2'];
    }

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }

    public function recordedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by_user_id');
    }
}
