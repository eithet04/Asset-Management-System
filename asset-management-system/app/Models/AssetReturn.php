<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'asset_assignment_id',
    'asset_id',
    'received_by_user_id',
    'returned_at',
    'returned_condition',
    'next_status',
    'remarks',
])]
class AssetReturn extends Model
{
    protected function casts(): array
    {
        return ['returned_at' => 'datetime'];
    }

    public function assetAssignment(): BelongsTo
    {
        return $this->belongsTo(AssetAssignment::class);
    }

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }

    public function receivedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'received_by_user_id');
    }
}
