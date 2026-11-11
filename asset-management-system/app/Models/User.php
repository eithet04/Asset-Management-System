<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'role_id', 'is_active'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    public const ROLE_ADMIN = 'admin';

    public const ROLE_MANAGER = 'manager';

    public const ROLE_EMPLOYEE = 'employee';

    public const ROLE_ASSET_CONTROLLER = 'asset_controller';

    public const ROLE_HR_ADMIN_STAFF = 'hr_admin_staff';

    public const ROLE_IT_TECHNICIAN = 'it_technician';

    public const ROLE_DEPARTMENT_MANAGER = 'department_manager';

    public const ROLE_MANAGEMENT_VIEWER = 'management_viewer';

    public const ROLES = [
        self::ROLE_ADMIN,
        self::ROLE_MANAGER,
        self::ROLE_EMPLOYEE,
        self::ROLE_ASSET_CONTROLLER,
        self::ROLE_HR_ADMIN_STAFF,
        self::ROLE_IT_TECHNICIAN,
        self::ROLE_DEPARTMENT_MANAGER,
        self::ROLE_MANAGEMENT_VIEWER,
    ];

    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    public function dashboardRoute(): ?string
    {
        return match ($this->role?->slug) {
            self::ROLE_ADMIN => 'admin.dashboard',
            self::ROLE_MANAGER => 'manager.dashboard',
            self::ROLE_EMPLOYEE => 'employee.dashboard',
            self::ROLE_ASSET_CONTROLLER => 'manager.dashboard',
            self::ROLE_DEPARTMENT_MANAGER => 'manager.dashboard',
            self::ROLE_IT_TECHNICIAN => 'operations.maintenance',
            self::ROLE_HR_ADMIN_STAFF => 'operations.people',
            self::ROLE_MANAGEMENT_VIEWER => 'operations.reports',
            default => null,
        };
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function employee(): HasOne
    {
        return $this->hasOne(Employee::class);
    }

    public function createdAssets(): HasMany
    {
        return $this->hasMany(Asset::class, 'created_by');
    }

    public function updatedAssets(): HasMany
    {
        return $this->hasMany(Asset::class, 'updated_by');
    }

    public function assetRequests(): HasMany
    {
        return $this->hasMany(AssetRequest::class, 'requester_user_id');
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }
}
