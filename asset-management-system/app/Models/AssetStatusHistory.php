<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'asset_id',
    'changed_by_user_id',
    'old_status',
    'new_status',
    'old_condition',
    'new_condition',
    'changed_at',
    'remarks',
])]
class AssetStatusHistory extends Model
{
    protected function casts(): array
    {
        return ['changed_at' => 'datetime'];
    }

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }

    public function changedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by_user_id');
    }
}
