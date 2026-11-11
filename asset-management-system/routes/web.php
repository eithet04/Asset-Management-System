<?php

use App\Http\Controllers\Admin\AssetController;
use App\Http\Controllers\Admin\ManagementController;
use App\Http\Controllers\AssetOperationsController;
use App\Http\Controllers\Auth\AuthenticationController;
use Illuminate\Support\Facades\Route;

Route::get('/', [AuthenticationController::class, 'dashboard'])->name('home');

Route::middleware('guest')->group(function (): void {
    Route::get('/login', [AuthenticationController::class, 'createLogin'])->name('login');
    Route::post('/login', [AuthenticationController::class, 'storeLogin'])
        ->middleware('throttle:5,1')
        ->name('login.store');
    Route::get('/register', [AuthenticationController::class, 'createRegistration'])->name('register');
    Route::post('/register', [AuthenticationController::class, 'storeRegistration'])
        ->middleware('throttle:5,1')
        ->name('register.store');
});

Route::post('/logout', [AuthenticationController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

Route::middleware(['auth', 'role:admin'])->group(function (): void {
    Route::get('/admin', [AssetController::class, 'index'])->name('admin.dashboard');
    Route::post('/admin/assets', [AssetController::class, 'store'])->name('admin.assets.store');
    Route::get('/admin/management', fn () => to_route('admin.categories'))->name('admin.management');
    Route::get('/admin/categories', [ManagementController::class, 'index'])
        ->defaults('section', 'categories')
        ->name('admin.categories');
    Route::get('/admin/locations', [ManagementController::class, 'index'])
        ->defaults('section', 'locations')
        ->name('admin.locations');
    Route::get('/admin/branches', [ManagementController::class, 'index'])
        ->defaults('section', 'branches')
        ->name('admin.branches');
    Route::get('/admin/departments', [ManagementController::class, 'index'])
        ->defaults('section', 'departments')
        ->name('admin.departments');
    Route::get('/admin/users', [ManagementController::class, 'index'])
        ->defaults('section', 'users')
        ->name('admin.users');
    Route::get('/admin/maintenance', [ManagementController::class, 'index'])
        ->defaults('section', 'maintenance')
        ->name('admin.maintenance');
    Route::get('/admin/settings', [ManagementController::class, 'index'])
        ->defaults('section', 'settings')
        ->name('admin.settings');
    Route::post('/admin/categories', [ManagementController::class, 'storeCategory'])->name('admin.categories.store');
    Route::put('/admin/categories/{category}', [ManagementController::class, 'updateCategory'])->name('admin.categories.update');
    Route::delete('/admin/categories/{category}', [ManagementController::class, 'destroyCategory'])->name('admin.categories.destroy');
    Route::post('/admin/locations', [ManagementController::class, 'storeLocation'])->name('admin.locations.store');
    Route::put('/admin/locations/{location}', [ManagementController::class, 'updateLocation'])->name('admin.locations.update');
    Route::delete('/admin/locations/{location}', [ManagementController::class, 'destroyLocation'])->name('admin.locations.destroy');
    Route::post('/admin/branches', [ManagementController::class, 'storeBranch'])->name('admin.branches.store');
    Route::put('/admin/branches/{branch}', [ManagementController::class, 'updateBranch'])->name('admin.branches.update');
    Route::delete('/admin/branches/{branch}', [ManagementController::class, 'destroyBranch'])->name('admin.branches.destroy');
    Route::post('/admin/departments', [ManagementController::class, 'storeDepartment'])->name('admin.departments.store');
    Route::put('/admin/departments/{department}', [ManagementController::class, 'updateDepartment'])->name('admin.departments.update');
    Route::delete('/admin/departments/{department}', [ManagementController::class, 'destroyDepartment'])->name('admin.departments.destroy');
    Route::put('/admin/users/{user}', [ManagementController::class, 'updateUser'])->name('admin.users.update');
    Route::delete('/admin/users/{user}', [ManagementController::class, 'destroyUser'])->name('admin.users.destroy');
    Route::post('/admin/maintenance', [ManagementController::class, 'storeMaintenance'])->name('admin.maintenance.store');
    Route::put('/admin/maintenance/{maintenanceRecord}', [ManagementController::class, 'updateMaintenance'])->name('admin.maintenance.update');
    Route::delete('/admin/maintenance/{maintenanceRecord}', [ManagementController::class, 'destroyMaintenance'])->name('admin.maintenance.destroy');
    Route::put('/admin/settings', [ManagementController::class, 'updateSettings'])->name('admin.settings.update');
});

Route::middleware(['auth', 'role:manager,asset_controller,department_manager'])->group(function (): void {
    Route::get('/manager', [AssetOperationsController::class, 'index'])
        ->defaults('section', 'assignments')->name('manager.dashboard');
});

Route::middleware(['auth', 'role:employee'])->group(function (): void {
    Route::get('/employee', [AssetOperationsController::class, 'employeeDashboard'])->name('employee.dashboard');
    Route::get('/employee/asset-requests', [AssetOperationsController::class, 'index'])
        ->defaults('section', 'my-requests')->name('employee.requests');
    Route::post('/employee/asset-requests', [AssetOperationsController::class, 'storeRequest'])
        ->name('employee.requests.store');
    Route::put('/employee/asset-requests/{assetRequest}', [AssetOperationsController::class, 'updateOwnRequest'])
        ->name('employee.requests.update');
    Route::delete('/employee/asset-requests/{assetRequest}', [AssetOperationsController::class, 'destroyOwnRequest'])
        ->name('employee.requests.destroy');
    Route::get('/employee/notifications', [AssetOperationsController::class, 'index'])
        ->defaults('section', 'notifications')->name('employee.notifications');
    Route::post('/employee/notifications/{notification}/read', [AssetOperationsController::class, 'markNotificationRead'])
        ->name('employee.notifications.read');
    Route::delete('/employee/notifications/{notification}', [AssetOperationsController::class, 'destroyOwnNotification'])
        ->name('employee.notifications.destroy');
    Route::post('/employee/notifications/read-all', [AssetOperationsController::class, 'markAllNotificationsRead'])
        ->name('employee.notifications.read-all');
});

Route::middleware(['auth', 'role:admin,manager,asset_controller,department_manager,it_technician,hr_admin_staff,management_viewer'])->prefix('operations')->name('operations.')->group(function (): void {
    foreach (['assignments', 'returns', 'transfers', 'status', 'history', 'reports', 'requests', 'purchases', 'warranties', 'verification', 'disposals', 'notifications', 'maintenance', 'people'] as $section) {
        Route::get("/{$section}", [AssetOperationsController::class, 'index'])
            ->defaults('section', $section)->name($section);
    }
    Route::post('/assignments', [AssetOperationsController::class, 'storeAssignment'])->name('assignments.store');
    Route::post('/returns/{assignment}', [AssetOperationsController::class, 'storeReturn'])->name('returns.store');
    Route::post('/transfers', [AssetOperationsController::class, 'storeTransfer'])->name('transfers.store');
    Route::put('/assets/{asset}/status', [AssetOperationsController::class, 'updateStatus'])->name('status.update');
    Route::put('/requests/{assetRequest}', [AssetOperationsController::class, 'reviewRequest'])->name('requests.review');
    Route::post('/requests/{assetRequest}/fulfill', [AssetOperationsController::class, 'fulfillRequest'])->name('requests.fulfill');
    Route::post('/purchases', [AssetOperationsController::class, 'storePurchase'])->name('purchases.store');
    Route::post('/warranties', [AssetOperationsController::class, 'storeWarranty'])->name('warranties.store');
    Route::post('/warranties/{warranty}/claims', [AssetOperationsController::class, 'storeWarrantyClaim'])->name('warranties.claims.store');
    Route::put('/warranty-claims/{claim}', [AssetOperationsController::class, 'reviewWarrantyClaim'])->name('warranties.claims.update');
    Route::post('/verification', [AssetOperationsController::class, 'storeVerification'])->name('verification.store');
    Route::post('/disposals', [AssetOperationsController::class, 'storeDisposal'])->name('disposals.store');
    Route::put('/disposals/{disposal}', [AssetOperationsController::class, 'reviewDisposal'])->name('disposals.review');
    Route::post('/notifications/{notification}/read', [AssetOperationsController::class, 'markNotificationRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [AssetOperationsController::class, 'markAllNotificationsRead'])->name('notifications.read-all');
    Route::put('/records/{type}/{record}', [AssetOperationsController::class, 'updateRecord'])
        ->whereIn('type', ['assignment', 'return', 'transfer', 'purchase', 'warranty', 'verification'])
        ->whereNumber('record')->name('records.update');
    Route::delete('/records/{type}/{record}', [AssetOperationsController::class, 'destroyRecord'])
        ->whereIn('type', ['assignment', 'return', 'transfer', 'request', 'purchase', 'warranty', 'claim', 'verification', 'disposal', 'maintenance', 'notification'])
        ->where('record', '[A-Za-z0-9-]+')->name('records.destroy');
    Route::post('/maintenance', [ManagementController::class, 'storeMaintenance'])->name('maintenance.store');
    Route::put('/maintenance/{maintenanceRecord}', [ManagementController::class, 'updateMaintenance'])->name('maintenance.update');
    Route::get('/attachments/{type}/{id}', [AssetOperationsController::class, 'downloadAttachment'])
        ->whereIn('type', ['invoice', 'warranty', 'maintenance'])->name('attachments.download');
});
