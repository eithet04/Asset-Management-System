<?php

namespace Tests\Feature;

use App\Models\Asset;
use App\Models\AssetActivity;
use App\Models\Category;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_the_admin_dashboard(): void
    {
        $admin = $this->createUserWithRole(User::ROLE_ADMIN);

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        $response->assertOk();
        $response->assertViewIs('admin.dashboard');
        $response->assertDontSeeText('Manager view');
        $response->assertDontSeeText('Need a hand?');
        $response->assertSee(route('admin.categories'));
        $response->assertSee(route('admin.locations'));
        $response->assertSeeText('MANAGE');
    }

    public function test_dashboard_greeting_uses_yangon_local_time(): void
    {
        $admin = $this->createUserWithRole(User::ROLE_ADMIN);

        $this->travelTo(\Illuminate\Support\Carbon::parse('2026-11-11 02:00:00', 'UTC'));
        $this->actingAs($admin)->get(route('admin.dashboard'))->assertSeeText('Good morning, '.$admin->name);

        $this->travelTo(\Illuminate\Support\Carbon::parse('2026-11-11 06:00:00', 'UTC'));
        $this->actingAs($admin)->get(route('admin.dashboard'))->assertSeeText('Good afternoon, '.$admin->name);

        $this->travelTo(\Illuminate\Support\Carbon::parse('2026-11-11 12:00:00', 'UTC'));
        $this->actingAs($admin)->get(route('admin.dashboard'))->assertSeeText('Good evening, '.$admin->name);

        $this->travelBack();
    }

    public function test_admin_is_forbidden_from_the_manager_dashboard(): void
    {
        $admin = $this->createUserWithRole(User::ROLE_ADMIN);

        $response = $this->actingAs($admin)->get(route('manager.dashboard'));

        $response->assertForbidden();
    }

    public function test_dashboard_shows_asset_status_counts_and_non_warranty_activity(): void
    {
        $admin = $this->createUserWithRole(User::ROLE_ADMIN);
        $category = Category::query()->create([
            'code' => 'EQUIPMENT',
            'name' => 'Equipment',
        ]);

        foreach (['available', 'assigned', 'under_maintenance', 'damaged', 'disposed'] as $index => $status) {
            Asset::query()->create([
                'category_id' => $category->id,
                'asset_code' => 'AST-DASH-'.$index,
                'name' => ucfirst($status).' equipment',
                'status' => $status,
            ]);
        }

        AssetActivity::query()->create([
            'user_id' => $admin->id,
            'action_type' => 'registered',
            'description' => 'Registered dashboard equipment.',
            'occurred_at' => now(),
        ]);
        AssetActivity::query()->create([
            'user_id' => $admin->id,
            'action_type' => 'warranty_recorded',
            'description' => 'Warranty activity should not appear.',
            'occurred_at' => now()->addSecond(),
        ]);

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        $response->assertOk()
            ->assertSeeText('Total Assets')
            ->assertSeeText('Add new asset')
            ->assertSeeText('Available Assets')
            ->assertSeeText('Assigned Assets')
            ->assertSeeText('Under Maintenance')
            ->assertSeeText('Damaged Assets')
            ->assertSeeText('Disposed Assets')
            ->assertSeeText('Recent Activities')
            ->assertSeeText('Registered dashboard equipment.')
            ->assertDontSeeText('Warranty activity should not appear.')
            ->assertViewHas('totalAssets', 5)
            ->assertViewHas('availableAssets', 1)
            ->assertViewHas('assignedAssets', 1)
            ->assertViewHas('maintenanceAssets', 1)
            ->assertViewHas('damagedAssets', 1)
            ->assertViewHas('disposedAssets', 1)
            ->assertViewHas('recentActivities', fn ($activities) => $activities->count() === 1);
    }

    public function test_admin_can_register_an_asset_and_see_it_on_the_dashboard(): void
    {
        $admin = $this->createUserWithRole(User::ROLE_ADMIN);
        $category = Category::query()->create([
            'code' => 'LAPTOP',
            'name' => 'Laptop',
        ]);

        $response = $this->actingAs($admin)->post(route('admin.assets.store'), [
            'asset_code' => 'AST-TEST-1',
            'name' => 'Test laptop',
            'category_id' => $category->id,
            'serial_number' => 'SERIAL-TEST-1',
            'condition' => 'good',
            'status' => 'available',
        ]);

        $response->assertRedirect(route('admin.dashboard'));
        $response->assertSessionHas('status');
        $this->assertDatabaseHas('assets', [
            'asset_code' => 'AST-TEST-1',
            'name' => 'Test laptop',
            'created_by' => $admin->id,
        ]);
        $this->assertDatabaseHas('asset_activities', [
            'action_type' => 'registered',
            'user_id' => $admin->id,
        ]);
        $this->assertDatabaseHas('asset_status_histories', [
            'new_status' => 'available',
            'new_condition' => 'good',
            'changed_by_user_id' => $admin->id,
        ]);

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSeeText('Test laptop')
            ->assertSeeText('AST-TEST-1')
            ->assertSeeText('Total Assets')
            ->assertSeeText('1');

        $this->actingAs($admin)
            ->get(route('admin.dashboard', ['q' => 'AST-TEST-1', 'status' => 'available']))
            ->assertOk()
            ->assertSeeText('Test laptop');
    }

    public function test_asset_registration_rejects_duplicate_asset_codes(): void
    {
        $admin = $this->createUserWithRole(User::ROLE_ADMIN);
        $category = Category::query()->create([
            'code' => 'DESKTOP',
            'name' => 'Desktop',
        ]);
        $payload = [
            'asset_code' => 'AST-DUPLICATE',
            'name' => 'Office desktop',
            'category_id' => $category->id,
            'condition' => 'good',
            'status' => 'available',
        ];

        $this->actingAs($admin)->post(route('admin.assets.store'), $payload)->assertRedirect();
        $this->actingAs($admin)
            ->from(route('admin.dashboard'))
            ->post(route('admin.assets.store'), $payload)
            ->assertSessionHasErrors('asset_code');
    }

    public function test_admin_can_update_asset_details_from_the_dashboard(): void
    {
        $admin = $this->createUserWithRole(User::ROLE_ADMIN);
        $category = Category::query()->create([
            'code' => 'LAPTOP',
            'name' => 'Laptop',
        ]);
        $asset = Asset::query()->create([
            'category_id' => $category->id,
            'asset_code' => 'AST-UPDATE-1',
            'name' => 'Old laptop name',
            'condition' => 'good',
            'status' => 'available',
        ]);

        $this->actingAs($admin)
            ->put(route('admin.assets.update', $asset), [
                'asset_id' => $asset->id,
                'asset_code' => 'AST-UPDATE-1',
                'name' => 'Updated laptop name',
                'category_id' => $category->id,
                'condition' => 'fair',
                'description' => 'Updated from the dashboard.',
            ])
            ->assertRedirect(route('admin.dashboard'))
            ->assertSessionHas('status', 'Asset details updated.');

        $this->assertDatabaseHas('assets', [
            'id' => $asset->id,
            'name' => 'Updated laptop name',
            'condition' => 'fair',
            'description' => 'Updated from the dashboard.',
            'updated_by' => $admin->id,
        ]);
        $this->assertDatabaseHas('asset_activities', [
            'asset_id' => $asset->id,
            'user_id' => $admin->id,
            'action_type' => 'updated',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('data-edit-asset', false)
            ->assertSeeText('Edit');
    }

    public function test_non_admin_cannot_update_an_asset(): void
    {
        $employee = $this->createUserWithRole(User::ROLE_EMPLOYEE);
        $category = Category::query()->create([
            'code' => 'LAPTOP',
            'name' => 'Laptop',
        ]);
        $asset = Asset::query()->create([
            'category_id' => $category->id,
            'asset_code' => 'AST-UPDATE-2',
            'name' => 'Office laptop',
            'condition' => 'good',
            'status' => 'available',
        ]);

        $this->actingAs($employee)
            ->put(route('admin.assets.update', $asset))
            ->assertForbidden();
    }

    public function test_non_admin_cannot_register_an_asset(): void
    {
        $employee = $this->createUserWithRole(User::ROLE_EMPLOYEE);

        $this->actingAs($employee)
            ->post(route('admin.assets.store'))
            ->assertForbidden();
    }

    private function createUserWithRole(string $slug): User
    {
        $role = Role::query()->create([
            'name' => ucfirst($slug),
            'slug' => $slug,
        ]);

        return User::factory()->create([
            'role_id' => $role->id,
            'is_active' => true,
        ]);
    }
}
