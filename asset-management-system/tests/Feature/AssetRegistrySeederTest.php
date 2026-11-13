<?php

namespace Tests\Feature;

use App\Models\Asset;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AssetRegistrySeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_database_seeder_includes_registry_data_and_can_be_repeated(): void
    {
        $this->seed(DatabaseSeeder::class);
        $this->seed(DatabaseSeeder::class);

        $this->assertDatabaseCount('assets', 15);
        $this->assertDatabaseCount('locations', 1);
        $this->assertDatabaseHas('assets', [
            'asset_code' => 'DEMO-AST-1001',
            'name' => 'Demo Laptop 01',
            'serial_number' => 'DEMO-SN-0001',
            'current_employee_id' => null,
            'created_by' => null,
            'updated_by' => null,
        ]);
        $this->assertDatabaseHas('categories', ['code' => 'TABLET', 'name' => 'Tablet']);
        $this->assertDatabaseHas('locations', ['name' => 'Demo Main Office']);

        $asset = Asset::query()->where('asset_code', 'DEMO-AST-1001')->firstOrFail();
        $this->assertSame('2024-01-15', $asset->purchase_date->format('Y-m-d'));
        $this->assertSame('1250.00', $asset->purchase_cost);
        $this->assertSame('2026-01-15', $asset->warranty_expiry_date->format('Y-m-d'));

        $adminRole = Role::query()->where('slug', User::ROLE_ADMIN)->firstOrFail();
        $admin = User::factory()->create([
            'role_id' => $adminRole->id,
            'is_active' => true,
        ]);

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSeeText('Demo Laptop 01')
            ->assertSeeText('DEMO-AST-1001');
    }

    public function test_database_seeder_skips_demo_assets_in_production(): void
    {
        $this->app['env'] = 'production';
        $this->artisan('db:seed', [
            '--class' => DatabaseSeeder::class,
            '--force' => true,
        ])->assertExitCode(0);

        $this->assertDatabaseCount('assets', 0);
    }
}
