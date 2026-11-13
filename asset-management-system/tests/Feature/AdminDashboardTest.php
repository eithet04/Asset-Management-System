<?php

namespace Tests\Feature;

use App\Models\Asset;
use App\Models\AssetActivity;
use App\Models\Category;
use App\Models\Location;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
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
        $response->assertDontSee('name="brand"', false);
        $response->assertDontSee('name="model"', false);
        $response->assertDontSee('name="description"', false);
        $response->assertSeeText('Location');
        $response->assertSeeText('Purchase date');
        $response->assertSeeText('Purchase cost');
        $response->assertSeeText('Warranty expiry');
        $response->assertSeeText('Condition');
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
            'description' => 'Legacy description',
        ]);

        $this->actingAs($admin)
            ->put(route('admin.assets.update', $asset), [
                'asset_id' => $asset->id,
                'asset_code' => 'AST-UPDATE-1',
                'name' => 'Updated laptop name',
                'category_id' => $category->id,
                'condition' => 'fair',
            ])
            ->assertRedirect(route('admin.dashboard'))
            ->assertSessionHas('status', 'Asset details updated.');

        $this->assertDatabaseHas('assets', [
            'id' => $asset->id,
            'name' => 'Updated laptop name',
            'condition' => 'fair',
            'description' => 'Legacy description',
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

    public function test_admin_can_import_assets_from_csv_and_records_registration_history(): void
    {
        $admin = $this->createUserWithRole(User::ROLE_ADMIN);
        $category = Category::query()->create([
            'code' => 'LAPTOP',
            'name' => 'Laptop',
        ]);
        $location = Location::query()->create([
            'code' => 'HQ',
            'name' => 'Head Office',
        ]);
        $file = UploadedFile::fake()->createWithContent('assets.csv', implode("\r\n", [
            'Asset,Asset code,Category,Serial number,Location,Purchase Date,Purchase Cost,Warranty expiry,Condition,Status',
            'Lenovo IDEAPAD Slim 1,AST-1001,Laptop,SN-111111,Head Office,2025-03-05,500.50,2027-03-05,Good,Available',
            'Office laptop,AST-1002,LAPTOP,,,,650,2028-04-06,Fair,Under maintenance',
        ]));

        $response = $this->actingAs($admin)->post(route('admin.assets.import'), ['csv_file' => $file]);

        $response->assertRedirect(route('admin.dashboard'))
            ->assertSessionHas('status', 'Imported 2 assets from CSV.');
        $this->assertDatabaseHas('assets', [
            'asset_code' => 'AST-1001',
            'name' => 'Lenovo IDEAPAD Slim 1',
            'category_id' => $category->id,
            'location_id' => $location->id,
            'serial_number' => 'SN-111111',
            'condition' => 'good',
            'status' => 'available',
            'created_by' => $admin->id,
        ]);
        $this->assertDatabaseHas('assets', [
            'asset_code' => 'AST-1002',
            'category_id' => $category->id,
            'location_id' => null,
            'serial_number' => null,
            'condition' => 'fair',
            'status' => 'under_maintenance',
        ]);
        $firstImportedAsset = Asset::query()->where('asset_code', 'AST-1001')->firstOrFail();
        $secondImportedAsset = Asset::query()->where('asset_code', 'AST-1002')->firstOrFail();
        $this->assertSame('2025-03-05', $firstImportedAsset->purchase_date->format('Y-m-d'));
        $this->assertSame('500.50', $firstImportedAsset->purchase_cost);
        $this->assertSame('2027-03-05', $firstImportedAsset->warranty_expiry_date->format('Y-m-d'));
        $this->assertNull($secondImportedAsset->location_id);
        $this->assertNull($secondImportedAsset->purchase_date);
        $this->assertSame('650.00', $secondImportedAsset->purchase_cost);
        $this->assertSame('2028-04-06', $secondImportedAsset->warranty_expiry_date->format('Y-m-d'));
        $this->assertDatabaseCount('asset_activities', 2);
        $this->assertDatabaseCount('asset_status_histories', 2);

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSeeText('Head Office')
            ->assertSeeText('Mar 5, 2025')
            ->assertSeeText('500.50')
            ->assertSeeText('Mar 5, 2027')
            ->assertSeeText('Good');
    }

    public function test_csv_import_rejects_duplicate_codes_and_assigned_assets_without_partial_import(): void
    {
        $admin = $this->createUserWithRole(User::ROLE_ADMIN);
        $category = Category::query()->create([
            'code' => 'LAPTOP',
            'name' => 'Laptop',
        ]);
        Asset::query()->create([
            'category_id' => $category->id,
            'asset_code' => 'AST-EXISTING',
            'name' => 'Existing laptop',
            'condition' => 'good',
            'status' => 'available',
        ]);
        $file = UploadedFile::fake()->createWithContent('assets.csv', implode("\r\n", [
            'Asset,Asset code,Category,Serial number,Location,Purchase Date,Purchase Cost,Warranty expiry,Condition,Status',
            'New laptop,AST-NEW,Laptop,SN-NEW,,2025-01-01,200,2026-01-01,Good,Available',
            'Existing laptop,AST-EXISTING,Laptop,SN-EXISTING,,2025-01-01,200,2026-01-01,Good,Assigned',
        ]));

        $response = $this->followingRedirects()
            ->actingAs($admin)
            ->from(route('admin.dashboard'))
            ->post(route('admin.assets.import'), ['csv_file' => $file]);

        $response->assertOk()
            ->assertSeeText('Row 3: Assigned assets must be registered through the assignment workflow.')
            ->assertSeeText("Row 3: Asset code 'AST-EXISTING' already exists.");
        $this->assertDatabaseMissing('assets', ['asset_code' => 'AST-NEW']);
        $this->assertDatabaseCount('assets', 1);
        $this->assertDatabaseCount('asset_activities', 0);
    }

    public function test_csv_import_rejects_invalid_purchase_cost_and_condition(): void
    {
        $admin = $this->createUserWithRole(User::ROLE_ADMIN);
        Category::query()->create([
            'code' => 'LAPTOP',
            'name' => 'Laptop',
        ]);
        $file = UploadedFile::fake()->createWithContent('assets.csv', implode("\r\n", [
            'Asset,Asset code,Category,Serial number,Location,Purchase Date,Purchase Cost,Warranty expiry,Condition,Status',
            'Faulty laptop,AST-BAD-DATA,Laptop,SN-BAD-DATA,,not-a-date,-20,not-a-date,Excellent,Available',
        ]));

        $response = $this->followingRedirects()
            ->actingAs($admin)
            ->from(route('admin.dashboard'))
            ->post(route('admin.assets.import'), ['csv_file' => $file]);

        $response->assertOk()
            ->assertSeeText('Row 2: The Purchase date field must be a valid date.')
            ->assertSeeText('Row 2: The Purchase cost field must be at least 0.')
            ->assertSeeText('Row 2: The Warranty expiry field must be a valid date.')
            ->assertSeeText('Row 2: The selected Condition is invalid.');
        $this->assertDatabaseMissing('assets', ['asset_code' => 'AST-BAD-DATA']);
        $this->assertDatabaseCount('asset_activities', 0);
    }

    public function test_csv_import_rejects_an_unknown_location(): void
    {
        $admin = $this->createUserWithRole(User::ROLE_ADMIN);
        Category::query()->create([
            'code' => 'LAPTOP',
            'name' => 'Laptop',
        ]);
        $file = UploadedFile::fake()->createWithContent('assets.csv', implode("\r\n", [
            'Asset,Asset code,Category,Serial number,Location,Purchase Date,Purchase Cost,Warranty expiry,Condition,Status',
            'Unknown site laptop,AST-UNKNOWN-LOCATION,Laptop,SN-UNKNOWN,Unknown Site,2025-03-05,500,2027-03-05,Good,Available',
        ]));

        $response = $this->followingRedirects()
            ->actingAs($admin)
            ->from(route('admin.dashboard'))
            ->post(route('admin.assets.import'), ['csv_file' => $file]);

        $response->assertOk()
            ->assertSeeText("Row 2: Location 'Unknown Site' was not found, is inactive, or is ambiguous.");
        $this->assertDatabaseMissing('assets', ['asset_code' => 'AST-UNKNOWN-LOCATION']);
        $this->assertDatabaseCount('asset_activities', 0);
    }

    public function test_non_admin_cannot_import_assets_from_csv(): void
    {
        $employee = $this->createUserWithRole(User::ROLE_EMPLOYEE);

        $this->actingAs($employee)
            ->post(route('admin.assets.import'))
            ->assertForbidden();
    }

    public function test_asset_export_downloads_all_rows_with_the_registry_csv_columns(): void
    {
        $admin = $this->createUserWithRole(User::ROLE_ADMIN);
        $category = Category::query()->create([
            'code' => 'LAPTOP',
            'name' => 'Laptop',
        ]);
        $location = Location::query()->create([
            'code' => 'HQ',
            'name' => 'Head Office',
        ]);

        for ($index = 1; $index <= 11; $index++) {
            Asset::query()->create([
                'category_id' => $category->id,
                'location_id' => $index === 1 ? $location->id : null,
                'asset_code' => 'AST-EXPORT-'.$index,
                'name' => $index === 1 ? '=HYPERLINK("https://example.test")' : 'Laptop '.$index,
                'serial_number' => 'SN-EXPORT-'.$index,
                'purchase_date' => '2025-01-02',
                'purchase_cost' => '1200.50',
                'warranty_expiry_date' => '2027-01-02',
                'condition' => 'fair',
                'status' => 'available',
            ]);
        }

        $response = $this->actingAs($admin)->get(route('admin.assets.export'));

        $response->assertOk()
            ->assertHeader('content-type', 'text/csv; charset=UTF-8')
            ->assertHeader('content-disposition', 'attachment; filename=asset-registry.csv');
        $csvLines = explode("\n", substr($response->streamedContent(), 3));
        $csvRows = array_map(static fn (string $line): array => str_getcsv($line), array_filter($csvLines));

        $this->assertSame([
            'Asset',
            'Asset code',
            'Category',
            'Serial number',
            'Location',
            'Purchase Date',
            'Purchase Cost',
            'Warranty expiry',
            'Condition',
            'Status',
        ], $csvRows[0]);
        $this->assertCount(12, $csvRows);
        $this->assertSame("'=HYPERLINK(\"https://example.test\")", $csvRows[1][0]);
        $this->assertSame('Head Office', $csvRows[1][4]);
        $this->assertSame('2025-01-02', $csvRows[1][5]);
        $this->assertSame('1200.50', $csvRows[1][6]);
        $this->assertSame('2027-01-02', $csvRows[1][7]);
        $this->assertSame('Fair', $csvRows[1][8]);
    }

    public function test_asset_export_applies_dashboard_search_and_status_filters(): void
    {
        $admin = $this->createUserWithRole(User::ROLE_ADMIN);
        $category = Category::query()->create([
            'code' => 'LAPTOP',
            'name' => 'Laptop',
        ]);
        Asset::query()->create([
            'category_id' => $category->id,
            'asset_code' => 'AST-MATCH',
            'name' => 'Filtered laptop',
            'condition' => 'good',
            'status' => 'available',
        ]);
        Asset::query()->create([
            'category_id' => $category->id,
            'asset_code' => 'AST-OTHER',
            'name' => 'Filtered laptop damaged',
            'condition' => 'good',
            'status' => 'damaged',
        ]);

        $response = $this->actingAs($admin)->get(route('admin.assets.export', [
            'q' => 'AST-MATCH',
            'status' => 'available',
        ]));

        $response->assertOk();
        $csvRows = array_map(
            static fn (string $line): array => str_getcsv($line),
            array_filter(explode("\n", substr($response->streamedContent(), 3))),
        );
        $this->assertCount(2, $csvRows);
        $this->assertSame('AST-MATCH', $csvRows[1][1]);
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
