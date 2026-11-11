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

    public static function createForUser(User $user): self
    {
        $profile = self::query()->where('user_id', $user->id)->first();
        if ($profile) {
            return $profile;
        }

        $nameParts = preg_split('/\s+/', trim($user->name), 2) ?: [];
        $baseCode = 'EMP-'.str_pad((string) $user->id, 6, '0', STR_PAD_LEFT);
        $employeeCode = $baseCode;
        $suffix = 1;
        while (self::query()->where('employee_code', $employeeCode)->exists()) {
            $employeeCode = $baseCode.'-'.$suffix++;
        }

        return self::query()->firstOrCreate(
            ['user_id' => $user->id],
            [
                'employee_code' => $employeeCode,
                'first_name' => $nameParts[0] ?? $user->name,
                'last_name' => $nameParts[1] ?? '',
                'email' => $user->email,
            ],
        );
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
