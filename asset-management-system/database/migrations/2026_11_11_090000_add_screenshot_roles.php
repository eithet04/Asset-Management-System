<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private array $roles = [
        'asset_controller' => 'Asset Controller',
        'hr_admin_staff' => 'HR / Admin Staff',
        'it_technician' => 'IT Staff / Technician',
        'department_manager' => 'Department Manager',
        'management_viewer' => 'Management / Viewer',
    ];

    public function up(): void
    {
        foreach ($this->roles as $slug => $name) {
            DB::table('roles')->updateOrInsert(
                ['slug' => $slug],
                ['name' => $name, 'description' => $this->description($slug), 'updated_at' => now(), 'created_at' => now()],
            );
        }
    }

    public function down(): void
    {
        DB::table('roles')->whereIn('slug', array_keys($this->roles))
            ->whereNotIn('id', DB::table('users')->select('role_id')->whereNotNull('role_id'))
            ->delete();
    }

    private function description(string $slug): string
    {
        return match ($slug) {
            'asset_controller' => 'Manage day-to-day asset operations.',
            'hr_admin_staff' => 'View employee-related asset information and support staff changes.',
            'it_technician' => 'Manage IT equipment condition and assigned maintenance work.',
            'department_manager' => 'Monitor department assets and review department requests and reports.',
            'management_viewer' => 'View company-wide asset dashboards and reports without editing records.',
        };
    }
};
