<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'category_id',
    'location_id',
    'current_employee_id',
    'created_by',
    'updated_by',
    'asset_code',
    'name',
    'brand',
    'model',
    'serial_number',
    'purchase_date',
    'purchase_cost',
    'warranty_expiry_date',
    'condition',
    'status',
    'description',
])]
class Asset extends Model
{
    protected function casts(): array
    {
        return [
            'purchase_date' => 'date',
            'purchase_cost' => 'decimal:2',
            'warranty_expiry_date' => 'date',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function currentEmployee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'current_employee_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(AssetAssignment::class);
    }

    public function returns(): HasMany
    {
        return $this->hasMany(AssetReturn::class);
    }

    public function transfers(): HasMany
    {
        return $this->hasMany(AssetTransfer::class);
    }

    public function statusHistories(): HasMany
    {
        return $this->hasMany(AssetStatusHistory::class);
    }

    public function activities(): HasMany
    {
        return $this->hasMany(AssetActivity::class);
    }
}
