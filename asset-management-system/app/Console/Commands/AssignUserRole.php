<?php

namespace App\Console\Commands;

use App\Models\Employee;
use App\Models\Role;
use App\Models\User;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('users:assign-role {email} {role : One of the configured application roles}')]
#[Description('Assign a role to an existing user account')]
class AssignUserRole extends Command
{
    public function handle(): int
    {
        $role = (string) $this->argument('role');

        if (! in_array($role, User::ROLES, true)) {
            $this->error('Choose one of the configured roles: '.implode(', ', User::ROLES).'.');

            return self::INVALID;
        }

        $user = User::query()->where('email', $this->argument('email'))->first();

        if ($user === null) {
            $this->error('No user account was found with that email address.');

            return self::FAILURE;
        }

        $roleName = match ($role) {
            User::ROLE_ADMIN => 'Administrator',
            User::ROLE_MANAGER => 'Manager',
            User::ROLE_EMPLOYEE => 'Employee',
            User::ROLE_ASSET_CONTROLLER => 'Asset Controller',
            User::ROLE_HR_ADMIN_STAFF => 'HR / Admin Staff',
            User::ROLE_IT_TECHNICIAN => 'IT Staff / Technician',
            User::ROLE_DEPARTMENT_MANAGER => 'Department Manager',
            User::ROLE_MANAGEMENT_VIEWER => 'Management / Viewer',
        };
        $roleModel = Role::query()->firstOrCreate(['slug' => $role], ['name' => $roleName]);

        $user->forceFill(['role_id' => $roleModel->id])->save();
        if ($role !== User::ROLE_ADMIN) {
            Employee::createForUser($user);
        }
        $this->info("Assigned the {$role} role to {$user->email}.");

        return self::SUCCESS;
    }
}
