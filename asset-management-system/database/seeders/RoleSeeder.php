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
        ] as $slug => $name) {
            Role::query()->updateOrCreate(
                ['slug' => $slug],
                ['name' => $name],
            );
        }
    }
}
