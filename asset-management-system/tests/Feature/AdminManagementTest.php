<?php

namespace Tests\Feature;

use App\Models\Asset;
use App\Models\Branch;
use App\Models\Category;
use App\Models\Department;
use App\Models\Location;
use App\Models\MaintenanceRecord;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_each_management_link_opens_a_page_with_only_its_section(): void
    {
        $admin = $this->createUserWithRole(User::ROLE_ADMIN);

        foreach ([
            'admin.categories' => ['Manage Categories', 'Add category', 'Add location'],
            'admin.locations' => ['Manage Locations', 'Add location', 'Add category'],
            'admin.branches' => ['Manage Branches', 'Add branch', 'Add department'],
            'admin.departments' => ['Manage Departments', 'Add department', 'Add branch'],
            'admin.users' => ['Manage User roles', 'Assign a role', 'Add category'],
            'admin.maintenance' => ['Manage Maintenance', 'Create request', 'Add category'],
            'admin.settings' => ['Manage Company settings', 'Update settings', 'Add category'],
        ] as $route => [$heading, $expectedContent, $absentContent]) {
            $this->actingAs($admin)
                ->get(route($route))
                ->assertOk()
                ->assertSeeText($heading)
                ->assertSeeText($expectedContent)
                ->assertSeeText('MANAGE')
                ->assertDontSeeText($absentContent);
        }
    }

    public function test_admin_can_create_update_and_archive_categories(): void
    {
        $admin = $this->createUserWithRole(User::ROLE_ADMIN);

        $this->actingAs($admin)->get(route('admin.categories'))
            ->assertOk()
            ->assertDontSee('name="code"', false)
            ->assertDontSee('name="description"', false);

        $this->actingAs($admin)->post(route('admin.categories.store'), [
            'name' => 'Tablet',
        ])->assertRedirect(route('admin.categories'));

        $category = Category::query()->where('code', 'TABLET')->firstOrFail();
        $this->actingAs($admin)->get(route('admin.categories'))
            ->assertOk()->assertSeeText('Update')->assertSeeText('Delete');
        $this->actingAs($admin)->put(route('admin.categories.update', $category), [
            'name' => 'Tablet computers',
            'is_active' => '0',
        ])->assertRedirect(route('admin.categories'));

        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'name' => 'Tablet computers',
            'is_active' => false,
        ]);
        $this->assertSame('TABLET', $category->fresh()->code);
    }

    public function test_category_delete_archives_without_removing_existing_assets(): void
    {
        $admin = $this->createUserWithRole(User::ROLE_ADMIN);
        $category = Category::query()->create(['code' => 'DELETE-CAT', 'name' => 'Delete category']);
        $asset = Asset::query()->create([
            'category_id' => $category->id,
            'asset_code' => 'KEEP-CAT-ASSET',
            'name' => 'Historical asset',
            'status' => 'available',
            'condition' => 'good',
        ]);

        $this->actingAs($admin)->delete(route('admin.categories.destroy', $category))
            ->assertRedirect(route('admin.categories'));

        $this->assertDatabaseHas('categories', ['id' => $category->id, 'is_active' => false]);
        $this->assertDatabaseHas('assets', ['id' => $asset->id, 'category_id' => $category->id]);
    }

    public function test_admin_can_create_and_archive_locations(): void
    {
        $admin = $this->createUserWithRole(User::ROLE_ADMIN);
        $branch = Branch::query()->create([
            'code' => 'HQ',
            'name' => 'Head office',
        ]);

        $this->actingAs($admin)->post(route('admin.locations.store'), [
            'name' => 'Room 1',
            'location_type' => 'room',
            'branch_id' => $branch->id,
        ])->assertRedirect(route('admin.locations'));

        $location = Location::query()->where('name', 'Room 1')->firstOrFail();
        $this->assertNull($location->code);
        $this->actingAs($admin)->put(route('admin.locations.update', $location), [
            'name' => 'Room 1',
            'location_type' => 'room',
            'branch_id' => $branch->id,
            'description' => '',
            'is_active' => '0',
        ])->assertRedirect(route('admin.locations'));

        $this->assertDatabaseHas('locations', [
            'id' => $location->id,
            'is_active' => false,
        ]);
        $this->assertSame($branch->id, $location->fresh()->branch_id);
        $this->actingAs($admin)->get(route('admin.locations'))
            ->assertOk()
            ->assertDontSee('name="code"', false)
            ->assertDontSee('name="parent_id"', false)
            ->assertDontSeeText('Parent location')
            ->assertSeeText('Update')
            ->assertSeeText('Delete');
    }

    public function test_location_delete_button_deactivates_without_removing_historical_asset_reference(): void
    {
        $admin = $this->createUserWithRole(User::ROLE_ADMIN);
        $category = Category::query()->create(['code' => 'LOCATION-HISTORY', 'name' => 'Location history']);
        $location = Location::query()->create(['name' => 'Historical room']);
        $asset = Asset::query()->create([
            'category_id' => $category->id,
            'location_id' => $location->id,
            'asset_code' => 'LOCATION-HISTORY-ASSET',
            'name' => 'Asset with historical location',
            'status' => 'available',
            'condition' => 'good',
        ]);

        $this->actingAs($admin)->delete(route('admin.locations.destroy', $location))
            ->assertRedirect(route('admin.locations'));

        $this->assertDatabaseHas('locations', [
            'id' => $location->id,
            'is_active' => false,
        ]);
        $this->assertDatabaseHas('assets', [
            'id' => $asset->id,
            'location_id' => $location->id,
        ]);
    }

    public function test_admin_can_create_and_edit_departments_and_branches(): void
    {
        $admin = $this->createUserWithRole(User::ROLE_ADMIN);

        $this->actingAs($admin)->post(route('admin.departments.store'), [
            'name' => 'Finance',
            'description' => 'Finance department',
        ])->assertRedirect(route('admin.departments'));
        $department = Department::query()->where('name', 'Finance')->firstOrFail();
        $this->actingAs($admin)->get(route('admin.departments'))
            ->assertOk()->assertSeeText('Update')->assertSeeText('Delete');
        $this->actingAs($admin)->put(route('admin.departments.update', $department), [
            'name' => 'Finance and Accounts',
            'description' => 'Finance team',
            'is_active' => '1',
        ])->assertRedirect(route('admin.departments'));
        $this->assertDatabaseHas('departments', [
            'id' => $department->id,
            'name' => 'Finance and Accounts',
            'code' => 'FINANCE',
        ]);

        $this->actingAs($admin)->post(route('admin.branches.store'), [
            'name' => 'Yangon Office',
            'address' => 'Downtown',
            'phone' => '09-123456',
        ])->assertRedirect(route('admin.branches'));
        $branch = Branch::query()->where('name', 'Yangon Office')->firstOrFail();
        $this->actingAs($admin)->get(route('admin.branches'))
            ->assertOk()->assertSeeText('Update')->assertSeeText('Delete');
        $this->actingAs($admin)->put(route('admin.branches.update', $branch), [
            'name' => 'Yangon Main Office',
            'address' => 'City Center',
            'phone' => '09-654321',
            'is_active' => '0',
        ])->assertRedirect(route('admin.branches'));
        $this->assertDatabaseHas('branches', [
            'id' => $branch->id,
            'name' => 'Yangon Main Office',
            'code' => 'YANGON-OFFICE',
            'is_active' => false,
        ]);
    }

    public function test_department_and_branch_delete_deactivate_records(): void
    {
        $admin = $this->createUserWithRole(User::ROLE_ADMIN);
        $department = Department::query()->create(['code' => 'ARCHIVE-DEPT', 'name' => 'Archive department']);
        $branch = Branch::query()->create(['code' => 'ARCHIVE-BRANCH', 'name' => 'Archive branch']);

        $this->actingAs($admin)->delete(route('admin.departments.destroy', $department))
            ->assertRedirect(route('admin.departments'));
        $this->actingAs($admin)->delete(route('admin.branches.destroy', $branch))
            ->assertRedirect(route('admin.branches'));

        $this->assertDatabaseHas('departments', ['id' => $department->id, 'is_active' => false]);
        $this->assertDatabaseHas('branches', ['id' => $branch->id, 'is_active' => false]);
    }

    public function test_user_delete_deactivates_account_and_protects_self_and_last_admin(): void
    {
        $admin = $this->createUserWithRole(User::ROLE_ADMIN);
        $employee = $this->createUserWithRole(User::ROLE_EMPLOYEE);

        $this->actingAs($admin)->get(route('admin.users'))
            ->assertOk()->assertSeeText('Update')->assertSeeText('Delete');
        $this->actingAs($admin)->delete(route('admin.users.destroy', $employee))
            ->assertRedirect(route('admin.users'));
        $this->assertDatabaseHas('users', ['id' => $employee->id, 'is_active' => false]);

        $this->actingAs($admin)->delete(route('admin.users.destroy', $admin))->assertUnprocessable();
        $this->assertDatabaseHas('users', ['id' => $admin->id, 'is_active' => true]);
    }

    public function test_maintenance_delete_permanently_deletes_completed_records_only(): void
    {
        $admin = $this->createUserWithRole(User::ROLE_ADMIN);
        $category = Category::query()->create(['code' => 'MAINT-DELETE', 'name' => 'Maintenance delete']);
        $asset = Asset::query()->create([
            'category_id' => $category->id,
            'asset_code' => 'MAINT-DELETE-ASSET',
            'name' => 'Maintenance asset',
            'status' => 'available',
            'condition' => 'good',
        ]);
        $completed = MaintenanceRecord::query()->create([
            'asset_id' => $asset->id,
            'created_by_user_id' => $admin->id,
            'description' => 'Completed repair',
            'status' => 'completed',
            'reported_at' => now()->subDay(),
            'completed_at' => now(),
        ]);
        $open = MaintenanceRecord::query()->create([
            'asset_id' => $asset->id,
            'created_by_user_id' => $admin->id,
            'description' => 'Open repair',
            'status' => 'in_progress',
            'reported_at' => now(),
        ]);

        $this->actingAs($admin)->delete(route('admin.maintenance.destroy', $open))->assertUnprocessable();
        $this->actingAs($admin)->delete(route('admin.maintenance.destroy', $completed))
            ->assertRedirect(route('admin.maintenance'));

        $this->assertDatabaseMissing('maintenance_records', ['id' => $completed->id]);
        $this->assertDatabaseHas('maintenance_records', ['id' => $open->id, 'deleted_at' => null]);
    }

    public function test_deactivated_categories_and_locations_cannot_be_used_for_new_assets(): void
    {
        $admin = $this->createUserWithRole(User::ROLE_ADMIN);
        $category = Category::query()->create(['code' => 'ARCHIVED-CAT', 'name' => 'Archived category']);
        $location = Location::query()->create(['name' => 'Archived location']);
        $existingAsset = Asset::query()->create([
            'category_id' => $category->id,
            'location_id' => $location->id,
            'asset_code' => 'EXISTING-ARCHIVED-ASSET',
            'name' => 'Existing asset history',
            'status' => 'available',
            'condition' => 'good',
        ]);
        $this->actingAs($admin)->put(route('admin.categories.update', $category), [
            'name' => $category->name,
            'is_active' => '0',
        ])->assertRedirect(route('admin.categories'));
        $this->actingAs($admin)->put(route('admin.locations.update', $location), [
            'name' => $location->name,
            'location_type' => 'office',
            'branch_id' => '',
            'description' => '',
            'is_active' => '0',
        ])->assertRedirect(route('admin.locations'));

        $this->actingAs($admin)->post(route('admin.assets.store'), [
            'asset_code' => 'INACTIVE-CATEGORY-ASSET',
            'name' => 'Invalid category asset',
            'category_id' => $category->id,
            'location_id' => $location->id,
            'condition' => 'good',
            'status' => 'available',
        ])->assertSessionHasErrors('category_id');

        $activeCategory = Category::query()->create(['code' => 'ACTIVE-CAT', 'name' => 'Active category']);
        $this->actingAs($admin)->post(route('admin.assets.store'), [
            'asset_code' => 'INACTIVE-LOCATION-ASSET',
            'name' => 'Invalid location asset',
            'category_id' => $activeCategory->id,
            'location_id' => $location->id,
            'condition' => 'good',
            'status' => 'available',
        ])->assertSessionHasErrors('location_id');

        $this->assertDatabaseHas('categories', ['id' => $category->id, 'is_active' => false]);
        $this->assertDatabaseHas('locations', ['id' => $location->id, 'is_active' => false]);
        $this->assertDatabaseHas('assets', [
            'id' => $existingAsset->id,
            'category_id' => $category->id,
            'location_id' => $location->id,
        ]);
    }

    public function test_admin_can_change_user_roles_but_cannot_remove_the_last_admin(): void
    {
        $admin = $this->createUserWithRole(User::ROLE_ADMIN);
        $employee = $this->createUserWithRole(User::ROLE_EMPLOYEE);

        $managerRole = Role::query()->firstOrCreate(
            ['slug' => User::ROLE_MANAGER],
            ['name' => 'Manager'],
        );
        $this->actingAs($admin)->put(route('admin.users.update', $employee), [
            'role_id' => $managerRole->id,
            'is_active' => '1',
        ])->assertRedirect(route('admin.users'));

        $this->assertDatabaseHas('users', [
            'id' => $employee->id,
            'role_id' => $managerRole->id,
        ]);

        $this->actingAs($admin)->put(route('admin.users.update', $admin), [
            'role_id' => $admin->role_id,
            'is_active' => '0',
        ])->assertRedirect(route('admin.users'));

        $this->assertDatabaseHas('users', ['id' => $admin->id, 'is_active' => true]);
    }

    public function test_admin_can_assign_a_new_role_and_department_to_a_user(): void
    {
        $admin = $this->createUserWithRole(User::ROLE_ADMIN);
        $employee = $this->createUserWithRole(User::ROLE_EMPLOYEE);
        $department = Department::query()->create(['code' => 'FIN', 'name' => 'Finance']);
        $departmentManagerRole = Role::query()->firstOrCreate(
            ['slug' => User::ROLE_DEPARTMENT_MANAGER],
            ['name' => 'Department Manager'],
        );

        $this->actingAs($admin)->get(route('admin.users'))
            ->assertOk()->assertSeeText('Department Manager')->assertSeeText('Department');
        $this->actingAs($admin)->put(route('admin.users.update', $employee), [
            'role_id' => $departmentManagerRole->id,
            'is_active' => '1',
            'department_id' => $department->id,
        ])->assertRedirect(route('admin.users'));

        $this->assertDatabaseHas('employees', [
            'user_id' => $employee->id,
            'department_id' => $department->id,
        ]);
    }

    public function test_admin_can_track_maintenance_and_asset_status_history(): void
    {
        $admin = $this->createUserWithRole(User::ROLE_ADMIN);
        $category = Category::query()->create(['code' => 'LAPTOP', 'name' => 'Laptop']);
        $asset = Asset::query()->create([
            'category_id' => $category->id,
            'asset_code' => 'AST-MAINT-1',
            'name' => 'Repair laptop',
            'condition' => 'fair',
            'status' => 'available',
        ]);

        $this->actingAs($admin)->post(route('admin.maintenance.store'), [
            'asset_id' => $asset->id,
            'description' => 'Battery needs replacement',
        ])->assertRedirect(route('admin.maintenance'));

        $record = MaintenanceRecord::query()->where('asset_id', $asset->id)->firstOrFail();
        $this->actingAs($admin)->get(route('admin.maintenance'))
            ->assertOk()->assertSeeText('Update')->assertSeeText('Delete');
        $this->actingAs($admin)->put(route('admin.maintenance.update', $record), [
            'status' => 'in_progress',
            'assigned_to_user_id' => $admin->id,
            'cost' => '35.50',
        ])->assertRedirect(route('admin.maintenance'));

        $this->assertDatabaseHas('assets', [
            'id' => $asset->id,
            'status' => 'under_maintenance',
        ]);

        $this->actingAs($admin)->put(route('admin.maintenance.update', $record), [
            'status' => 'completed',
            'assigned_to_user_id' => $admin->id,
            'cost' => '35.50',
        ])->assertRedirect(route('admin.maintenance'));

        $this->assertDatabaseHas('maintenance_records', [
            'id' => $record->id,
            'status' => 'completed',
            'cost' => '35.50',
        ]);
        $this->assertDatabaseHas('assets', [
            'id' => $asset->id,
            'status' => 'available',
        ]);
        $this->assertDatabaseHas('asset_status_histories', [
            'asset_id' => $asset->id,
            'new_status' => 'under_maintenance',
        ]);
        $this->assertDatabaseHas('asset_status_histories', [
            'asset_id' => $asset->id,
            'new_status' => 'available',
        ]);
    }

    public function test_admin_can_save_company_settings(): void
    {
        $admin = $this->createUserWithRole(User::ROLE_ADMIN);

        $this->actingAs($admin)->put(route('admin.settings.update'), [
            'company_name' => 'Example Company',
            'contact_email' => 'assets@example.com',
            'phone' => '+1 555 0100',
            'timezone' => 'Asia/Yangon',
            'currency' => 'MMK',
        ])->assertRedirect(route('admin.settings'));

        $this->assertDatabaseHas('company_settings', [
            'id' => 1,
            'company_name' => 'Example Company',
            'contact_email' => 'assets@example.com',
            'timezone' => 'Asia/Yangon',
            'currency' => 'MMK',
        ]);
    }

    public function test_non_admin_cannot_access_management_or_update_settings(): void
    {
        $employee = $this->createUserWithRole(User::ROLE_EMPLOYEE);

        $this->actingAs($employee)->get(route('admin.categories'))->assertForbidden();
        $this->actingAs($employee)->put(route('admin.settings.update'))->assertForbidden();
    }

    private function createUserWithRole(string $slug): User
    {
        $role = Role::query()->firstOrCreate(
            ['slug' => $slug],
            ['name' => ucfirst($slug)],
        );

        return User::factory()->create([
            'role_id' => $role->id,
            'is_active' => true,
        ]);
    }
}
