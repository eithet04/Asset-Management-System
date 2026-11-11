<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['asset_warranty_id', 'created_by_user_id', 'claim_reference', 'description', 'submitted_at', 'status', 'resolution'])]
class WarrantyClaim extends Model
{
    protected function casts(): array
    {
        return ['submitted_at' => 'date'];
    }

    public function warranty(): BelongsTo
    {
        return $this->belongsTo(AssetWarranty::class, 'asset_warranty_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }
}
