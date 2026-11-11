<?php

namespace Tests\Feature;

use App\Models\Asset;
use App\Models\AssetAssignment;
use App\Models\AssetRequest;
use App\Models\AssetReturn;
use App\Models\AssetStatusHistory;
use App\Models\Category;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Location;
use App\Models\MaintenanceRecord;
use App\Models\Role;
use App\Models\User;
use App\Notifications\AssetOperationNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AssetOperationsTest extends TestCase
{
    use RefreshDatabase;

    public function test_deleting_an_open_assignment_releases_the_asset(): void
    {
        $manager = $this->userWithRole(User::ROLE_MANAGER);
        $employeeUser = $this->userWithRole(User::ROLE_EMPLOYEE);
        $category = Category::query()->create(['code' => 'DELETE-ASG', 'name' => 'Delete assignment']);
        $employee = Employee::query()->create([
            'user_id' => $employeeUser->id,
            'employee_code' => 'DELETE-ASG-EMP',
            'first_name' => 'Taylor',
            'last_name' => 'Employee',
            'email' => $employeeUser->email,
        ]);
        $asset = Asset::query()->create([
            'category_id' => $category->id,
            'asset_code' => 'DELETE-ASG-ASSET',
            'name' => 'Assigned laptop',
            'status' => 'assigned',
            'current_employee_id' => $employee->id,
        ]);
        $assignedAt = now();
        $assignment = AssetAssignment::query()->create([
            'asset_id' => $asset->id,
            'employee_id' => $employee->id,
            'assigned_by_user_id' => $manager->id,
            'assigned_at' => $assignedAt,
        ]);
        AssetStatusHistory::query()->create([
            'asset_id' => $asset->id,
            'changed_by_user_id' => $manager->id,
            'old_status' => 'available',
            'new_status' => 'assigned',
            'old_condition' => 'good',
            'new_condition' => 'good',
            'changed_at' => $assignedAt,
        ]);

        $this->actingAs($manager)->delete(route('operations.records.destroy', [
            'type' => 'assignment',
            'record' => $assignment->id,
        ]))->assertRedirect(route('operations.assignments'));

        $this->assertDatabaseMissing('asset_assignments', ['id' => $assignment->id]);
        $this->assertDatabaseHas('assets', [
            'id' => $asset->id,
            'status' => 'available',
            'current_employee_id' => null,
        ]);
    }

    public function test_deleting_a_return_restores_the_prior_assignment_state(): void
    {
        $manager = $this->userWithRole(User::ROLE_MANAGER);
        $employeeUser = $this->userWithRole(User::ROLE_EMPLOYEE);
        $category = Category::query()->create(['code' => 'DELETE-RET', 'name' => 'Delete return']);
        $employee = Employee::query()->create([
            'user_id' => $employeeUser->id,
            'employee_code' => 'DELETE-RET-EMP',
            'first_name' => 'Alex',
            'last_name' => 'Employee',
            'email' => $employeeUser->email,
        ]);
        $asset = Asset::query()->create([
            'category_id' => $category->id,
            'asset_code' => 'DELETE-RET-ASSET',
            'name' => 'Returned laptop',
            'status' => 'damaged',
            'condition' => 'fair',
        ]);
        $assignedAt = now()->subDay();
        $returnedAt = now();
        $assignment = AssetAssignment::query()->create([
            'asset_id' => $asset->id,
            'employee_id' => $employee->id,
            'assigned_by_user_id' => $manager->id,
            'assigned_at' => $assignedAt,
            'closed_at' => $returnedAt,
        ]);
        $return = AssetReturn::query()->create([
            'asset_assignment_id' => $assignment->id,
            'asset_id' => $asset->id,
            'received_by_user_id' => $manager->id,
            'returned_at' => $returnedAt,
            'returned_condition' => 'fair',
            'next_status' => 'damaged',
        ]);
        AssetStatusHistory::query()->create([
            'asset_id' => $asset->id,
            'changed_by_user_id' => $manager->id,
            'old_status' => 'assigned',
            'new_status' => 'damaged',
            'old_condition' => 'good',
            'new_condition' => 'fair',
            'changed_at' => $returnedAt,
        ]);

        $updatedReturnAt = now()->addMinute();
        $this->actingAs($manager)->put(route('operations.records.update', [
            'type' => 'return',
            'record' => $return->id,
        ]), [
            'returned_at' => $updatedReturnAt->format('Y-m-d\TH:i'),
            'returned_condition' => 'poor',
            'next_status' => 'available',
            'remarks' => 'Updated return details',
        ])->assertRedirect(route('operations.returns'));
        $this->assertDatabaseHas('assets', [
            'id' => $asset->id,
            'status' => 'available',
            'condition' => 'poor',
        ]);
        $this->assertDatabaseHas('asset_status_histories', [
            'asset_id' => $asset->id,
            'new_status' => 'available',
            'new_condition' => 'poor',
            'changed_at' => $updatedReturnAt->format('Y-m-d H:i:00'),
        ]);

        $this->actingAs($manager)->delete(route('operations.records.destroy', [
            'type' => 'return',
            'record' => $return->id,
        ]))->assertRedirect(route('operations.returns'));

        $this->assertDatabaseMissing('asset_returns', ['id' => $return->id]);
        $this->assertDatabaseHas('asset_assignments', ['id' => $assignment->id, 'closed_at' => null]);
        $this->assertDatabaseHas('assets', [
            'id' => $asset->id,
            'status' => 'assigned',
            'condition' => 'good',
            'current_employee_id' => $employee->id,
        ]);
    }

    public function test_manager_can_assign_and_record_an_asset_return(): void
    {
        $manager = $this->userWithRole(User::ROLE_MANAGER);
        $employeeUser = $this->userWithRole(User::ROLE_EMPLOYEE);
        $category = Category::query()->create(['code' => 'LAPTOP', 'name' => 'Laptop']);
        $department = Department::query()->create(['code' => 'IT', 'name' => 'IT']);
        $employee = Employee::query()->create([
            'user_id' => $employeeUser->id,
            'department_id' => $department->id,
            'employee_code' => 'EMP-001',
            'first_name' => 'Taylor',
            'last_name' => 'Employee',
            'email' => $employeeUser->email,
        ]);
        $asset = Asset::query()->create([
            'category_id' => $category->id,
            'asset_code' => 'ASSET-001',
            'name' => 'Work laptop',
            'status' => 'available',
            'condition' => 'good',
        ]);

        $this->actingAs($manager)->post(route('operations.assignments.store'), [
            'asset_id' => $asset->id,
            'employee_id' => $employee->id,
            'assigned_at' => now()->format('Y-m-d\TH:i'),
            'remarks' => 'New starter equipment',
        ])->assertRedirect(route('operations.assignments'));

        $assignment = AssetAssignment::query()->where('asset_id', $asset->id)->firstOrFail();
        $this->actingAs($manager)->get(route('operations.assignments'))
            ->assertOk()->assertSeeText('Update')->assertSeeText('Delete');
        $this->assertDatabaseHas('assets', [
            'id' => $asset->id,
            'status' => 'assigned',
            'current_employee_id' => $employee->id,
        ]);
        $this->assertSame(1, $employeeUser->notifications()->where('data->kind', 'asset_assignment')->count());

        $this->actingAs($employeeUser)->post(route('employee.requests.store'), [
            'category_id' => $category->id,
            'business_reason' => 'A second monitor is needed for review work.',
        ])->assertRedirect(route('employee.requests'));
        $secondRequest = AssetRequest::query()->latest('id')->firstOrFail();
        $this->actingAs($manager)->put(route('operations.requests.review', $secondRequest), [
            'decision' => 'approve',
            'reviewer_remarks' => 'Approved; waiting for stock.',
        ])->assertRedirect(route('operations.requests'));

        $secondAsset = Asset::query()->create([
            'category_id' => $category->id,
            'asset_code' => 'ASSET-004',
            'name' => 'Second external monitor',
            'status' => 'available',
        ]);
        $this->actingAs($manager)->post(route('operations.requests.fulfill', $secondRequest), [
            'asset_id' => $secondAsset->id,
        ])->assertRedirect(route('operations.requests'));
        $this->assertDatabaseHas('asset_requests', [
            'id' => $secondRequest->id,
            'status' => 'fulfilled',
            'fulfilled_asset_id' => $secondAsset->id,
        ]);
        $this->assertDatabaseHas('asset_status_histories', ['asset_id' => $asset->id, 'new_status' => 'assigned']);
        $this->assertDatabaseHas('notifications', [
            'notifiable_id' => $employeeUser->id,
            'type' => AssetOperationNotification::class,
        ]);

        $this->actingAs($manager)->post(route('operations.returns.store', $assignment), [
            'returned_at' => now()->format('Y-m-d\TH:i'),
            'returned_condition' => 'fair',
            'next_status' => 'damaged',
            'remarks' => 'Screen is cracked',
        ])->assertRedirect(route('operations.returns'));

        $this->assertDatabaseHas('asset_returns', [
            'asset_assignment_id' => $assignment->id,
            'returned_condition' => 'fair',
            'next_status' => 'damaged',
        ]);
        $this->assertDatabaseHas('assets', [
            'id' => $asset->id,
            'status' => 'damaged',
            'condition' => 'fair',
            'current_employee_id' => null,
        ]);
        $this->actingAs($manager)->get(route('operations.returns'))
            ->assertOk()->assertSeeText('Update')->assertSeeText('Delete');
    }

    public function test_employee_request_can_be_approved_and_fulfilled_in_one_step(): void
    {
        $manager = $this->userWithRole(User::ROLE_MANAGER);
        $employeeUser = $this->userWithRole(User::ROLE_EMPLOYEE);
        $category = Category::query()->create(['code' => 'MONITOR', 'name' => 'Monitor']);
        $employee = Employee::query()->create([
            'user_id' => $employeeUser->id,
            'employee_code' => 'EMP-002',
            'first_name' => 'Alex',
            'last_name' => 'Worker',
            'email' => $employeeUser->email,
        ]);
        $asset = Asset::query()->create([
            'category_id' => $category->id,
            'asset_code' => 'ASSET-002',
            'name' => 'External monitor',
            'status' => 'available',
        ]);

        $this->actingAs($employeeUser)->post(route('employee.requests.store'), [
            'category_id' => $category->id,
            'business_reason' => 'I need a monitor for my daily work.',
        ])->assertRedirect(route('employee.requests'));

        $request = AssetRequest::query()->firstOrFail();
        $this->actingAs($manager)->put(route('operations.requests.review', $request), [
            'decision' => 'approve',
            'asset_id' => $asset->id,
            'reviewer_remarks' => 'Approved and assigned.',
        ])->assertRedirect(route('operations.requests'));

        $this->assertDatabaseHas('asset_requests', [
            'id' => $request->id,
            'status' => 'fulfilled',
            'fulfilled_asset_id' => $asset->id,
        ]);
        $this->assertDatabaseHas('assets', [
            'id' => $asset->id,
            'status' => 'assigned',
            'current_employee_id' => $employee->id,
        ]);
        $this->assertSame(1, $employeeUser->notifications()->where('data->kind', 'asset_request_review')->count());
    }

    public function test_employees_cannot_access_manager_operations_and_reports_export_csv(): void
    {
        $employee = $this->userWithRole(User::ROLE_EMPLOYEE);
        $manager = $this->userWithRole(User::ROLE_MANAGER);

        $this->actingAs($employee)->get(route('operations.assignments'))->assertForbidden();
        $response = $this->actingAs($manager)->get(route('operations.reports', ['type' => 'assets', 'download' => 1]))
            ->assertOk()
            ->assertHeader('content-type', 'text/csv; charset=UTF-8');
        $this->assertStringContainsString(
            '"Asset code",Asset,Category,Location,Holder,Status,Condition',
            $response->streamedContent(),
        );
    }

    public function test_operations_pages_render_and_manager_can_transfer_and_update_status(): void
    {
        $manager = $this->userWithRole(User::ROLE_MANAGER);
        $employeeUser = $this->userWithRole(User::ROLE_EMPLOYEE);
        $employee = Employee::query()->create([
            'user_id' => $employeeUser->id,
            'employee_code' => 'EMP-003',
            'first_name' => 'Sam',
            'last_name' => 'Staff',
            'email' => $employeeUser->email,
        ]);
        $category = Category::query()->create(['code' => 'PHONE', 'name' => 'Phone']);
        $location = Location::query()->create(['code' => 'OFFICE-1', 'name' => 'Main office']);
        $asset = Asset::query()->create([
            'category_id' => $category->id,
            'asset_code' => 'ASSET-003',
            'name' => 'Company phone',
            'status' => 'available',
        ]);

        foreach (['assignments', 'returns', 'transfers', 'status', 'requests', 'reports', 'history'] as $page) {
            $this->actingAs($manager)->get(route("operations.{$page}"))
                ->assertOk()
                ->assertSeeText('← Back');
        }
        $this->actingAs($employeeUser)->get(route('employee.dashboard'))->assertOk()
            ->assertSeeText('0 units');
        $this->actingAs($employeeUser)->get(route('employee.requests'))->assertOk()
            ->assertSeeText('← Back');

        $this->actingAs($manager)->post(route('operations.transfers.store'), [
            'asset_id' => $asset->id,
            'to_employee_id' => $employee->id,
            'to_location_id' => $location->id,
            'transferred_at' => now()->format('Y-m-d\TH:i'),
            'reason' => 'Issued for field work',
        ])->assertRedirect(route('operations.transfers'));

        $this->assertDatabaseHas('asset_transfers', [
            'asset_id' => $asset->id,
            'to_employee_id' => $employee->id,
            'to_location_id' => $location->id,
        ]);
        $this->actingAs($manager)->put(route('operations.status.update', $asset), [
            'status' => 'damaged',
            'condition' => 'poor',
            'remarks' => 'Device casing is damaged',
        ])->assertRedirect(route('operations.status'));
        $this->assertDatabaseHas('assets', [
            'id' => $asset->id,
            'status' => 'damaged',
            'condition' => 'poor',
        ]);
    }

    public function test_operations_sidebar_keeps_workspace_manage_and_role_navigation_visible(): void
    {
        $admin = $this->userWithRole(User::ROLE_ADMIN);
        $manager = $this->userWithRole(User::ROLE_MANAGER);
        $employee = $this->userWithRole(User::ROLE_EMPLOYEE);

        $this->actingAs($admin)->get(route('operations.assignments'))
            ->assertOk()
            ->assertSeeText('WORKSPACE')
            ->assertSeeText('ASSET OPERATIONS')
            ->assertSeeText('MANAGE')
            ->assertSee(route('admin.categories'), false)
            ->assertSee(route('admin.settings'), false);

        $this->actingAs($manager)->get(route('operations.assignments'))
            ->assertOk()
            ->assertSeeText('WORKSPACE')
            ->assertSeeText('ASSET OPERATIONS')
            ->assertDontSeeText('MANAGE');

        $this->actingAs($employee)->get(route('employee.requests'))
            ->assertOk()
            ->assertSeeText('MY WORKSPACE')
            ->assertSee(route('employee.dashboard'), false);
    }

    public function test_employee_registration_creates_a_linked_employee_profile(): void
    {
        $this->post(route('register.store'), [
            'name' => 'New Employee',
            'email' => 'new.employee@example.com',
            'password' => 'a-strong-password',
            'password_confirmation' => 'a-strong-password',
        ])->assertRedirect(route('employee.dashboard'));

        $user = User::query()->where('email', 'new.employee@example.com')->firstOrFail();
        $this->assertDatabaseHas('employees', [
            'user_id' => $user->id,
            'employee_code' => 'EMP-'.str_pad((string) $user->id, 6, '0', STR_PAD_LEFT),
            'first_name' => 'New',
            'last_name' => 'Employee',
        ]);
    }

    public function test_management_viewer_can_read_reports_but_cannot_change_assets(): void
    {
        $viewer = $this->userWithRole(User::ROLE_MANAGEMENT_VIEWER);
        $category = Category::query()->create(['code' => 'VIEW', 'name' => 'Viewer assets']);
        $asset = Asset::query()->create([
            'category_id' => $category->id,
            'asset_code' => 'VIEW-001',
            'name' => 'Viewer test asset',
            'status' => 'available',
            'condition' => 'good',
        ]);

        $this->actingAs($viewer)->get(route('operations.reports'))
            ->assertOk()->assertSeeText('Viewer test asset');
        $this->actingAs($viewer)->get(route('operations.assignments'))->assertForbidden();
        $this->actingAs($viewer)->put(route('operations.status.update', $asset), [
            'status' => 'damaged',
            'condition' => 'poor',
        ])->assertForbidden();
    }

    public function test_hr_staff_can_view_employee_assets_without_access_to_editing_pages(): void
    {
        $hrStaff = $this->userWithRole(User::ROLE_HR_ADMIN_STAFF);
        $employeeUser = $this->userWithRole(User::ROLE_EMPLOYEE);
        $category = Category::query()->create(['code' => 'HR', 'name' => 'HR assets']);
        $employee = Employee::query()->create([
            'user_id' => $employeeUser->id,
            'employee_code' => 'HR-EMP-001',
            'first_name' => 'Taylor',
            'last_name' => 'Employee',
            'email' => $employeeUser->email,
        ]);
        Asset::query()->create([
            'category_id' => $category->id,
            'asset_code' => 'HR-ASSET-001',
            'name' => 'HR visible laptop',
            'current_employee_id' => $employee->id,
            'status' => 'assigned',
            'condition' => 'good',
        ]);

        $this->actingAs($hrStaff)->get(route('operations.people'))
            ->assertOk()->assertSeeText('Taylor Employee')->assertSeeText('HR visible laptop');
        $this->actingAs($hrStaff)->get(route('operations.reports'))->assertForbidden();
        $this->actingAs($hrStaff)->get(route('operations.assignments'))->assertForbidden();
    }

    public function test_it_technician_can_open_maintenance_but_not_asset_assignment_pages(): void
    {
        $technician = $this->userWithRole(User::ROLE_IT_TECHNICIAN);
        $otherTechnician = $this->userWithRole(User::ROLE_IT_TECHNICIAN);
        $category = Category::query()->create(['code' => 'IT-TECH', 'name' => 'IT equipment']);
        $asset = Asset::query()->create([
            'category_id' => $category->id,
            'asset_code' => 'TECH-001',
            'name' => 'Technician laptop',
            'status' => 'under_maintenance',
            'condition' => 'fair',
        ]);
        $maintenanceRecord = MaintenanceRecord::query()->create([
            'asset_id' => $asset->id,
            'assigned_to_user_id' => $otherTechnician->id,
            'created_by_user_id' => $otherTechnician->id,
            'description' => 'Assigned to another technician',
            'status' => 'in_progress',
            'reported_at' => now(),
        ]);

        $this->actingAs($technician)->get(route('operations.maintenance'))->assertOk();
        $this->actingAs($technician)->get(route('operations.assignments'))->assertForbidden();
        $this->actingAs($technician)->get(route('operations.purchases'))->assertForbidden();
        $this->actingAs($technician)->put(route('operations.maintenance.update', $maintenanceRecord), [
            'status' => 'completed',
            'assigned_to_user_id' => $technician->id,
        ])->assertForbidden();
    }

    public function test_department_manager_is_scoped_to_their_department_and_cannot_override_assignment_department(): void
    {
        $manager = $this->userWithRole(User::ROLE_DEPARTMENT_MANAGER);
        $category = Category::query()->create(['code' => 'DEPT', 'name' => 'Department assets']);
        $ownDepartment = Department::query()->create(['code' => 'DEPT-A', 'name' => 'Department A']);
        $otherDepartment = Department::query()->create(['code' => 'DEPT-B', 'name' => 'Department B']);
        Employee::query()->create([
            'user_id' => $manager->id,
            'department_id' => $ownDepartment->id,
            'employee_code' => 'MGR-DEPT-A',
            'first_name' => 'Department',
            'last_name' => 'Manager',
            'email' => $manager->email,
        ]);
        $ownEmployeeUser = $this->userWithRole(User::ROLE_EMPLOYEE);
        $ownEmployee = Employee::query()->create([
            'user_id' => $ownEmployeeUser->id,
            'department_id' => $ownDepartment->id,
            'employee_code' => 'EMP-DEPT-A',
            'first_name' => 'Own',
            'last_name' => 'Department',
            'email' => $ownEmployeeUser->email,
        ]);
        $otherEmployeeUser = $this->userWithRole(User::ROLE_EMPLOYEE);
        $otherEmployee = Employee::query()->create([
            'user_id' => $otherEmployeeUser->id,
            'department_id' => $otherDepartment->id,
            'employee_code' => 'EMP-DEPT-B',
            'first_name' => 'Other',
            'last_name' => 'Department',
            'email' => $otherEmployeeUser->email,
        ]);
        $ownAsset = Asset::query()->create([
            'category_id' => $category->id,
            'asset_code' => 'DEPT-A-ASSET',
            'name' => 'Department A asset',
            'current_employee_id' => $ownEmployee->id,
            'status' => 'assigned',
            'condition' => 'good',
        ]);
        $availableForOwnEmployee = Asset::query()->create([
            'category_id' => $category->id,
            'asset_code' => 'DEPT-A-AVAILABLE',
            'name' => 'Available asset',
            'status' => 'available',
            'condition' => 'good',
        ]);
        $availableForOtherEmployee = Asset::query()->create([
            'category_id' => $category->id,
            'asset_code' => 'DEPT-B-AVAILABLE',
            'name' => 'Other available asset',
            'status' => 'available',
            'condition' => 'good',
        ]);

        $this->actingAs($manager)->get(route('operations.status'))
            ->assertOk()->assertSeeText('Department A asset')->assertDontSeeText('Other available asset');
        $this->actingAs($manager)->put(route('operations.status.update', $ownAsset), [
            'status' => 'damaged',
            'condition' => 'poor',
        ])->assertForbidden();
        $this->actingAs($manager)->post(route('operations.assignments.store'), [
            'asset_id' => $availableForOtherEmployee->id,
            'employee_id' => $otherEmployee->id,
            'assigned_at' => now()->format('Y-m-d\TH:i'),
        ])->assertForbidden();
        $this->actingAs($manager)->post(route('operations.assignments.store'), [
            'asset_id' => $availableForOwnEmployee->id,
            'employee_id' => $ownEmployee->id,
            'department_id' => $otherDepartment->id,
            'assigned_at' => now()->format('Y-m-d\TH:i'),
        ])->assertRedirect(route('operations.assignments'));

        $this->assertDatabaseHas('asset_assignments', [
            'asset_id' => $availableForOwnEmployee->id,
            'department_id' => $ownDepartment->id,
        ]);
    }

    private function userWithRole(string $slug): User
    {
        $role = Role::query()->firstOrCreate(['slug' => $slug], ['name' => ucfirst($slug)]);

        return User::factory()->create(['role_id' => $role->id, 'is_active' => true]);
    }
}
