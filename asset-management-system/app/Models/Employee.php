<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'user_id',
    'department_id',
    'branch_id',
    'location_id',
    'employee_code',
    'first_name',
    'last_name',
    'email',
    'phone',
    'job_title',
    'employment_status',
    'hire_date',
    'termination_date',
])]
class Employee extends Model
{
    protected function casts(): array
    {
        return [
            'hire_date' => 'date',
            'termination_date' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function assets(): HasMany
    {
        return $this->hasMany(Asset::class, 'current_employee_id');
    }

    public function assetAssignments(): HasMany
    {
        return $this->hasMany(AssetAssignment::class);
    }

    public function transfersFrom(): HasMany
    {
        return $this->hasMany(AssetTransfer::class, 'from_employee_id');
    }

    public function transfersTo(): HasMany
    {
        return $this->hasMany(AssetTransfer::class, 'to_employee_id');
    }
}
