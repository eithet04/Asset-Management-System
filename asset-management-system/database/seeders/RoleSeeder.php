<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Seed the application's role records.
     */
    public function run(): void
    {
        foreach ([
            User::ROLE_ADMIN => 'Administrator',
            User::ROLE_MANAGER => 'Manager',
            User::ROLE_EMPLOYEE => 'Employee',
            User::ROLE_ASSET_CONTROLLER => 'Asset Controller',
            User::ROLE_HR_ADMIN_STAFF => 'HR / Admin Staff',
            User::ROLE_IT_TECHNICIAN => 'IT Staff / Technician',
            User::ROLE_DEPARTMENT_MANAGER => 'Department Manager',
            User::ROLE_MANAGEMENT_VIEWER => 'Management / Viewer',
        ] as $slug => $name) {
            Role::query()->updateOrCreate(
                ['slug' => $slug],
                ['name' => $name],
            );
        }
    }
}
