<?php

namespace Tests\Feature;

use App\Models\Asset;
use App\Models\AssetDisposal;
use App\Models\AssetPurchaseRecord;
use App\Models\AssetVerificationItem;
use App\Models\AssetWarranty;
use App\Models\Category;
use App\Models\MaintenanceRecord;
use App\Models\Role;
use App\Models\User;
use App\Notifications\AssetOperationNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AssetManagementModulesTest extends TestCase
{
    use RefreshDatabase;

    public function test_manager_can_record_purchases_warranties_and_claims(): void
    {
        Storage::fake('local');
        $manager = $this->userWithRole(User::ROLE_MANAGER);
        $asset = $this->createAsset('MODULE-001');

        $this->actingAs($manager)->post(route('operations.purchases.store'), [
            'asset_id' => $asset->id,
            'supplier' => 'Acme Supply',
            'purchase_order_reference' => 'PO-2026-01',
            'invoice' => UploadedFile::fake()->create('invoice.pdf', 100, 'application/pdf'),
            'purchase_cost' => '899.50',
            'received_at' => today()->toDateString(),
        ])->assertRedirect(route('operations.purchases'));

        $purchase = AssetPurchaseRecord::query()->firstOrFail();
        $this->actingAs($manager)->get(route('operations.purchases'))
            ->assertOk()->assertSeeText('Update')->assertSeeText('Delete');
        Storage::disk('local')->assertExists($purchase->invoice_path);
        $this->assertDatabaseHas('assets', ['id' => $asset->id, 'purchase_cost' => '899.50']);
        $this->actingAs($manager)->get(route('operations.attachments.download', ['type' => 'invoice', 'id' => $purchase->id]))->assertOk();
        $this->actingAs($manager)->put(route('operations.records.update', [
            'type' => 'purchase',
            'record' => $purchase->id,
        ]), [
            'supplier' => 'Updated supplier',
            'purchase_order_reference' => 'PO-2026-02',
            'purchase_cost' => '950.00',
            'received_at' => today()->toDateString(),
        ])->assertRedirect(route('operations.purchases'));
        $this->assertDatabaseHas('asset_purchase_records', ['id' => $purchase->id, 'supplier' => 'Updated supplier']);
        $this->assertDatabaseHas('assets', ['id' => $asset->id, 'purchase_cost' => '950.00']);

        $this->actingAs($manager)->post(route('operations.warranties.store'), [
            'asset_id' => $asset->id,
            'provider' => 'Acme Care',
            'starts_at' => today()->toDateString(),
            'expires_at' => today()->addYear()->toDateString(),
            'document' => UploadedFile::fake()->create('warranty.pdf', 100, 'application/pdf'),
        ])->assertRedirect(route('operations.warranties'));
        $warranty = AssetWarranty::query()->firstOrFail();
        $this->actingAs($manager)->get(route('operations.warranties'))
            ->assertOk()->assertSeeText('Update')->assertSeeText('Delete');
        Storage::disk('local')->assertExists($warranty->document_path);

        $this->actingAs($manager)->post(route('operations.warranties.claims.store', $warranty), [
            'claim_reference' => 'CL-100',
            'description' => 'Battery has stopped charging.',
            'submitted_at' => today()->toDateString(),
        ])->assertRedirect(route('operations.warranties'));
        $claim = $warranty->claims()->firstOrFail();
        $this->actingAs($manager)->put(route('operations.warranties.claims.update', $claim), [
            'status' => 'in_review',
            'resolution' => 'Provider is inspecting the device.',
        ])->assertRedirect(route('operations.warranties'));
        $this->assertDatabaseHas('warranty_claims', ['id' => $claim->id, 'status' => 'in_review']);
        $this->assertDatabaseHas('asset_activities', ['asset_id' => $asset->id, 'action_type' => 'purchase_recorded']);

        $this->actingAs($manager)->delete(route('operations.records.destroy', [
            'type' => 'warranty',
            'record' => $warranty->id,
        ]))->assertRedirect(route('operations.warranties'));
        $this->assertDatabaseMissing('asset_warranties', ['id' => $warranty->id]);
        $this->assertDatabaseMissing('warranty_claims', ['id' => $claim->id]);
        Storage::disk('local')->assertMissing($warranty->document_path);

        $this->actingAs($manager)->delete(route('operations.records.destroy', [
            'type' => 'purchase',
            'record' => $purchase->id,
        ]))->assertRedirect(route('operations.purchases'));
        $this->assertDatabaseMissing('asset_purchase_records', ['id' => $purchase->id]);
        $this->assertDatabaseHas('assets', ['id' => $asset->id, 'purchase_cost' => null]);
        Storage::disk('local')->assertMissing($purchase->invoice_path);
    }

    public function test_warranty_reminders_are_persisted_once_per_threshold(): void
    {
        $admin = $this->userWithRole(User::ROLE_ADMIN);
        $asset = $this->createAsset('MODULE-002');
        $warranty = AssetWarranty::query()->create([
            'asset_id' => $asset->id,
            'recorded_by_user_id' => $admin->id,
            'provider' => 'Example Warranty Co',
            'starts_at' => today()->subYear(),
            'expires_at' => today()->addDays(7),
        ]);

        Artisan::call('assets:send-warranty-reminders');
        Artisan::call('assets:send-warranty-reminders');

        $this->assertDatabaseHas('notifications', [
            'notifiable_id' => $admin->id,
            'type' => AssetOperationNotification::class,
        ]);
        $this->assertSame([7], $warranty->fresh()->reminder_days_sent);
        $this->assertSame(1, $admin->notifications()->where('data->kind', 'warranty_expiry')->count());
    }

    public function test_verification_and_approved_disposal_create_auditable_history(): void
    {
        $admin = $this->userWithRole(User::ROLE_ADMIN);
        $manager = $this->userWithRole(User::ROLE_MANAGER);
        $asset = $this->createAsset('MODULE-003');

        $this->actingAs($manager)->post(route('operations.verification.store'), [
            'asset_id' => $asset->id,
            'finding' => 'mismatch',
            'observed_asset_code' => 'UNEXPECTED-CODE',
            'verified_at' => now()->format('Y-m-d\TH:i'),
            'notes' => 'Observed label differs from the register.',
        ])->assertRedirect(route('operations.verification'));
        $item = AssetVerificationItem::query()->firstOrFail();
        $session = $item->session;
        $this->assertDatabaseHas('asset_verification_items', [
            'asset_id' => $asset->id,
            'finding' => 'mismatch',
            'observed_asset_code' => 'UNEXPECTED-CODE',
        ]);
        $this->actingAs($manager)->put(route('operations.records.update', [
            'type' => 'verification',
            'record' => $item->id,
        ]), [
            'finding' => 'verified',
            'observed_asset_code' => 'MODULE-003',
            'notes' => 'Label verified.',
            'verified_at' => now()->format('Y-m-d\TH:i'),
        ])->assertRedirect(route('operations.verification'));
        $this->assertDatabaseHas('asset_verification_items', ['id' => $item->id, 'finding' => 'verified']);
        $this->actingAs($manager)->delete(route('operations.records.destroy', [
            'type' => 'verification',
            'record' => $item->id,
        ]))->assertRedirect(route('operations.verification'));
        $this->assertDatabaseMissing('asset_verification_sessions', ['id' => $session->id]);

        $this->actingAs($manager)->post(route('operations.disposals.store'), [
            'asset_id' => $asset->id,
            'reason' => 'The asset is beyond economical repair.',
        ])->assertRedirect(route('operations.disposals'));
        $disposal = AssetDisposal::query()->firstOrFail();
        $this->actingAs($admin)->put(route('operations.disposals.review', $disposal), [
            'decision' => 'approve',
            'disposed_at' => now()->format('Y-m-d\TH:i'),
            'reviewer_remarks' => 'Approved after verification.',
        ])->assertRedirect(route('operations.disposals'));

        $this->assertDatabaseHas('assets', ['id' => $asset->id, 'status' => 'disposed']);
        $this->assertDatabaseHas('asset_disposals', ['id' => $disposal->id, 'status' => 'approved']);
        $this->assertDatabaseHas('asset_status_histories', ['asset_id' => $asset->id, 'new_status' => 'disposed']);
        $this->assertDatabaseHas('asset_activities', ['asset_id' => $asset->id, 'action_type' => 'disposed']);
        $this->assertSame(1, $manager->notifications()->where('data->kind', 'disposal_review')->count());
    }

    public function test_operations_module_pages_and_notifications_are_role_scoped(): void
    {
        $admin = $this->userWithRole(User::ROLE_ADMIN);
        $employee = $this->userWithRole(User::ROLE_EMPLOYEE);

        foreach (['purchases', 'warranties', 'verification', 'disposals', 'maintenance', 'notifications'] as $page) {
            $this->actingAs($admin)->get(route("operations.{$page}"))->assertOk();
        }
        $this->actingAs($employee)->get(route('employee.notifications'))->assertOk()
            ->assertSeeText('Your notifications')
            ->assertDontSeeText('Asset Operations pages');
        $this->actingAs($employee)->get(route('operations.purchases'))->assertForbidden();

        $employee->notify(new AssetOperationNotification(
            'test',
            'Unread test notification',
            'This is a test notification.',
        ));
        $notification = $employee->unreadNotifications()->firstOrFail();
        $this->actingAs($employee)->post(route('employee.notifications.read', $notification->id))
            ->assertRedirect();
        $this->assertNotNull($notification->fresh()->read_at);

        $employee->notify(new AssetOperationNotification(
            'test',
            'Second unread notification',
            'This is another test notification.',
        ));
        $this->actingAs($employee)->post(route('employee.notifications.read-all'))->assertRedirect();
        $this->assertSame(0, $employee->unreadNotifications()->count());
        $employee->notify(new AssetOperationNotification(
            'test',
            'Notification to delete',
            'This notification can be deleted.',
        ));
        $notification = $employee->notifications()->latest()->firstOrFail();
        $this->actingAs($employee)->get(route('employee.notifications'))
            ->assertOk()->assertSeeText('Delete');
        $this->actingAs($employee)->delete(route('employee.notifications.destroy', $notification->id))
            ->assertRedirect();
        $this->assertDatabaseMissing('notifications', ['id' => $notification->id]);
    }

    public function test_maintenance_photo_and_completion_date_are_stored(): void
    {
        Storage::fake('local');
        $admin = $this->userWithRole(User::ROLE_ADMIN);
        $asset = $this->createAsset('MODULE-004');

        $this->actingAs($admin)->post(route('admin.maintenance.store'), [
            'asset_id' => $asset->id,
            'description' => 'Cracked screen needs repair.',
            'photo' => UploadedFile::fake()->image('screen.png'),
        ])->assertRedirect(route('admin.maintenance'));

        $record = MaintenanceRecord::query()->firstOrFail();
        Storage::disk('local')->assertExists($record->attachment_path);
        $this->actingAs($admin)->put(route('admin.maintenance.update', $record), [
            'status' => 'completed',
            'assigned_to_user_id' => $admin->id,
            'cost' => '125.00',
            'completed_at' => now()->format('Y-m-d\TH:i'),
        ])->assertRedirect(route('admin.maintenance'));
        $this->assertNotNull($record->fresh()->completed_at);
        $this->assertSame(2, $admin->notifications()->where('data->kind', 'maintenance_update')->count());
        $this->actingAs($admin)->get(route('operations.maintenance'))
            ->assertOk()->assertSeeText('Delete');
        $this->actingAs($admin)->delete(route('operations.records.destroy', [
            'type' => 'maintenance',
            'record' => $record->id,
        ]))->assertRedirect(route('operations.maintenance'));
        $this->assertDatabaseMissing('maintenance_records', ['id' => $record->id]);
        Storage::disk('local')->assertMissing($record->attachment_path);
    }

    private function createAsset(string $code): Asset
    {
        $category = Category::query()->create([
            'code' => 'CAT-'.$code,
            'name' => 'Category '.$code,
        ]);

        return Asset::query()->create([
            'category_id' => $category->id,
            'asset_code' => $code,
            'name' => 'Test asset '.$code,
            'status' => 'available',
        ]);
    }

    private function userWithRole(string $slug): User
    {
        $role = Role::query()->firstOrCreate(['slug' => $slug], ['name' => ucfirst($slug)]);

        return User::factory()->create(['role_id' => $role->id, 'is_active' => true]);
    }
}
