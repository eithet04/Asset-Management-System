<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#111a2b">
    <title>{{ $pageTitle }} · Assetflow</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <div class="admin-shell">
        <aside class="sidebar" id="sidebar">
            <a class="brand" href="{{ route('admin.dashboard') }}">
                <span class="brand-mark" aria-hidden="true">▣</span>
                <span class="brand-copy"><strong>Asset<span>flow</span></strong><small>CONTROL CENTER</small></span>
            </a>
            <div class="sidebar-section-label">WORKSPACE</div>
            <nav class="main-nav" aria-label="Administration">
                <a class="nav-link" href="{{ route('admin.dashboard') }}">Dashboard</a>
                <a class="nav-link" href="{{ route('admin.dashboard') }}#assets">Asset registry</a>
                <div class="sidebar-section-label sidebar-section-spaced">ASSET OPERATIONS</div>
                <a class="nav-link" href="{{ route('operations.assignments') }}">Assignments</a>
                <a class="nav-link" href="{{ route('operations.returns') }}">Returns</a>
                <a class="nav-link" href="{{ route('operations.transfers') }}">Transfers</a>
                <a class="nav-link" href="{{ route('operations.requests') }}">Request approvals</a>
                <a class="nav-link" href="{{ route('operations.reports') }}">Reports &amp; history</a>
                <a class="nav-link" href="{{ route('operations.purchases') }}">Purchases</a>
                <a class="nav-link" href="{{ route('operations.warranties') }}">Warranties</a>
                <a class="nav-link" href="{{ route('operations.verification') }}">Verification</a>
                <a class="nav-link" href="{{ route('operations.disposals') }}">Disposals</a>
                <a class="nav-link" href="{{ route('operations.notifications') }}">Notifications</a>
                <div class="sidebar-section-label sidebar-section-spaced">MANAGE</div>
                <a class="nav-link {{ $section === 'categories' ? 'active' : '' }}" @if ($section === 'categories') aria-current="page" @endif href="{{ route('admin.categories') }}">Categories</a>
                <a class="nav-link {{ $section === 'locations' ? 'active' : '' }}" @if ($section === 'locations') aria-current="page" @endif href="{{ route('admin.locations') }}">Locations</a>
                <a class="nav-link {{ $section === 'branches' ? 'active' : '' }}" @if ($section === 'branches') aria-current="page" @endif href="{{ route('admin.branches') }}">Branches</a>
                <a class="nav-link {{ $section === 'departments' ? 'active' : '' }}" @if ($section === 'departments') aria-current="page" @endif href="{{ route('admin.departments') }}">Departments</a>
                <a class="nav-link {{ $section === 'users' ? 'active' : '' }}" @if ($section === 'users') aria-current="page" @endif href="{{ route('admin.users') }}">User roles</a>
                <a class="nav-link {{ $section === 'maintenance' ? 'active' : '' }}" @if ($section === 'maintenance') aria-current="page" @endif href="{{ route('admin.maintenance') }}">Maintenance</a>
                <a class="nav-link {{ $section === 'settings' ? 'active' : '' }}" @if ($section === 'settings') aria-current="page" @endif href="{{ route('admin.settings') }}">Settings</a>
            </nav>
            <div class="sidebar-bottom">
                <div class="sidebar-user">
                    <span class="user-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                    <span class="user-details"><strong>{{ auth()->user()->name }}</strong><small>Administrator</small></span>
                    <form method="POST" action="{{ route('logout') }}">@csrf<button class="view-switch" type="submit">Sign out</button></form>
                </div>
            </div>
        </aside>

        <main class="main-panel">
            <header class="topbar">
                <button class="icon-button menu-toggle" type="button" aria-label="Toggle navigation" aria-expanded="false" aria-controls="sidebar">☰</button>
                <div class="breadcrumbs"><a href="{{ route('admin.dashboard') }}">Workspace</a><span class="breadcrumb-slash">/</span><strong>{{ $pageTitle }}</strong></div>
                <div class="topbar-actions"><span class="topbar-user">{{ auth()->user()->name }}</span></div>
            </header>

            <div class="page-content management-page">
                <section class="page-heading">
                    <div>
                        <p class="eyebrow">ADMINISTRATION</p>
                        <h1>Manage {{ $pageTitle }}</h1>
                        <p class="page-subtitle">Update {{ strtolower($pageTitle) }} for your workspace.</p>
                    </div>
                    <a class="button button-secondary" href="{{ route('admin.dashboard') }}">Back to dashboard</a>
                </section>

                @if (session('status'))
                    <div class="management-alert" role="status">{{ session('status') }}</div>
                @endif
                @if ($errors->any())
                    <div class="management-alert management-alert-error" role="alert">
                        <strong>Please correct the following:</strong>
                        <ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                    </div>
                @endif

                <nav class="management-tabs" aria-label="Management sections">
                    <a @if ($section === 'categories') aria-current="page" @endif href="{{ route('admin.categories') }}">Categories</a>
                    <a @if ($section === 'locations') aria-current="page" @endif href="{{ route('admin.locations') }}">Locations</a>
                    <a @if ($section === 'branches') aria-current="page" @endif href="{{ route('admin.branches') }}">Branches</a>
                    <a @if ($section === 'departments') aria-current="page" @endif href="{{ route('admin.departments') }}">Departments</a>
                    <a @if ($section === 'users') aria-current="page" @endif href="{{ route('admin.users') }}">User roles</a>
                    <a @if ($section === 'maintenance') aria-current="page" @endif href="{{ route('admin.maintenance') }}">Maintenance</a>
                    <a @if ($section === 'settings') aria-current="page" @endif href="{{ route('admin.settings') }}">Settings</a>
                </nav>

                @if ($section === 'categories')
                <section class="management-section" id="categories">
                    <div class="management-section-heading"><div><p class="eyebrow">ASSET TYPES</p><h2>Categories</h2><p>Keep existing asset history by deactivating categories instead of deleting them.</p></div></div>
                    <form class="management-create-form" method="POST" action="{{ route('admin.categories.store') }}">
                        @csrf
                        <label class="form-field">Category name<input name="name" value="{{ old('name') }}" required maxlength="255" placeholder="e.g. Tablet"></label>
                        <button class="button button-primary" type="submit">Add category</button>
                    </form>
                    <div class="management-table-wrap">
                        <table class="management-table">
                            <thead><tr><th>Name</th><th>Assets</th><th>Status</th><th>Actions</th></tr></thead>
                            <tbody>
                                @forelse ($categories as $category)
                                    <tr>
                                        <td><form id="category-{{ $category->id }}" method="POST" action="{{ route('admin.categories.update', $category) }}">@csrf @method('PUT')</form><input form="category-{{ $category->id }}" name="name" value="{{ $category->name }}" required maxlength="255" aria-label="Category name"></td>
                                        <td>{{ $category->assets_count }}</td>
                                        <td><select form="category-{{ $category->id }}" name="is_active" aria-label="Category status"><option value="1" @selected($category->is_active)>Active</option><option value="0" @selected(! $category->is_active)>Archived</option></select></td>
                                        <td>
                                            <button class="button button-secondary" form="category-{{ $category->id }}" type="submit">Update</button>
                                            <form method="POST" action="{{ route('admin.categories.destroy', $category) }}" onsubmit="return confirm('Delete this category from active use? Existing asset records will be retained.');">
                                                @csrf @method('DELETE')
                                                <button class="button button-secondary" type="submit">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4">No categories yet. Add one above.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </section>
                @endif

                @if ($section === 'locations')
                <section class="management-section" id="locations">
                    <div class="management-section-heading"><div><p class="eyebrow">PLACES</p><h2>Locations</h2><p>Organize assets by branch, office, room or storage area.</p></div></div>
                    <form class="management-create-form" method="POST" action="{{ route('admin.locations.store') }}">
                        @csrf
                        <label class="form-field">Name<input name="name" value="{{ old('name') }}" required maxlength="255" placeholder="e.g. Main office"></label>
                        <label class="form-field">Type<select name="location_type" required><option value="branch">Branch</option><option value="office" selected>Office</option><option value="room">Room</option><option value="storage">Storage</option><option value="other">Other</option></select></label>
                        <label class="form-field">Branch<select name="branch_id"><option value="">No branch</option>@foreach ($branches as $branch)<option value="{{ $branch->id }}">{{ $branch->name }}</option>@endforeach</select></label>
                        <button class="button button-primary" type="submit">Add location</button>
                        <label class="form-field management-description-field">Description<input name="description" value="{{ old('description') }}" maxlength="5000"></label>
                    </form>
                    <div class="management-table-wrap">
                        <table class="management-table">
                            <thead><tr><th>Name</th><th>Type</th><th>Branch</th><th>Description</th><th>Assets</th><th>Status</th><th>Actions</th></tr></thead>
                            <tbody>
                                @forelse ($locations as $location)
                                    <tr>
                                        <td><form id="location-{{ $location->id }}" method="POST" action="{{ route('admin.locations.update', $location) }}">@csrf @method('PUT')</form><input form="location-{{ $location->id }}" name="name" value="{{ $location->name }}" required maxlength="255" aria-label="Location name"></td>
                                        <td><select form="location-{{ $location->id }}" name="location_type" aria-label="Location type">@foreach (['branch', 'office', 'room', 'storage', 'other'] as $type)<option value="{{ $type }}" @selected($location->location_type === $type)>{{ ucfirst($type) }}</option>@endforeach</select></td>
                                        <td><select form="location-{{ $location->id }}" name="branch_id" aria-label="Branch"><option value="">None</option>@foreach ($branches as $branch)<option value="{{ $branch->id }}" @selected($location->branch_id === $branch->id)>{{ $branch->name }}</option>@endforeach</select></td>
                                        <td><input form="location-{{ $location->id }}" name="description" value="{{ $location->description }}" maxlength="5000" aria-label="Description"></td>
                                        <td>{{ $location->assets_count }}</td>
                                        <td><select form="location-{{ $location->id }}" name="is_active" aria-label="Location status"><option value="1" @selected($location->is_active)>Active</option><option value="0" @selected(! $location->is_active)>Archived</option></select></td>
                                        <td>
                                            <button class="button button-secondary" form="location-{{ $location->id }}" type="submit">Update</button>
                                            <form method="POST" action="{{ route('admin.locations.destroy', $location) }}" onsubmit="return confirm('Delete this location from active use? Existing asset records will be retained.');">
                                                @csrf @method('DELETE')
                                                <button class="button button-secondary" type="submit">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="7">No locations yet. Add one above.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </section>
                @endif

                @if ($section === 'departments')
                <section class="management-section" id="departments">
                    <div class="management-section-heading"><div><p class="eyebrow">ORGANIZATION</p><h2>Departments</h2><p>Add or edit departments. Existing employees and assignment history remain linked if a department is deactivated.</p></div></div>
                    <form class="management-create-form" method="POST" action="{{ route('admin.departments.store') }}">
                        @csrf
                        <label class="form-field">Department name<input name="name" value="{{ old('name') }}" required maxlength="255" placeholder="e.g. Finance"></label>
                        <label class="form-field">Description<input name="description" value="{{ old('description') }}" maxlength="5000"></label>
                        <button class="button button-primary" type="submit">Add department</button>
                    </form>
                    <div class="management-table-wrap">
                        <table class="management-table">
                            <thead><tr><th>Name</th><th>Description</th><th>Employees</th><th>Assignments</th><th>Status</th><th>Actions</th></tr></thead>
                            <tbody>
                                @forelse ($departments as $department)
                                    <tr>
                                        <td><form id="department-{{ $department->id }}" method="POST" action="{{ route('admin.departments.update', $department) }}">@csrf @method('PUT')</form><input form="department-{{ $department->id }}" name="name" value="{{ $department->name }}" required maxlength="255" aria-label="Department name"></td>
                                        <td><input form="department-{{ $department->id }}" name="description" value="{{ $department->description }}" maxlength="5000" aria-label="Department description"></td>
                                        <td>{{ $department->employees_count }}</td>
                                        <td>{{ $department->asset_assignments_count }}</td>
                                        <td><select form="department-{{ $department->id }}" name="is_active" aria-label="Department status"><option value="1" @selected($department->is_active)>Active</option><option value="0" @selected(! $department->is_active)>Inactive</option></select></td>
                                        <td>
                                            <button class="button button-secondary" form="department-{{ $department->id }}" type="submit">Update</button>
                                            <form method="POST" action="{{ route('admin.departments.destroy', $department) }}" onsubmit="return confirm('Delete this department from active use? Existing employee and assignment records will be retained.');">
                                                @csrf @method('DELETE')
                                                <button class="button button-secondary" type="submit">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="6">No departments yet. Add one above.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </section>
                @endif

                @if ($section === 'branches')
                <section class="management-section" id="branches">
                    <div class="management-section-heading"><div><p class="eyebrow">ORGANIZATION</p><h2>Branches</h2><p>Add or edit company branches. Existing location and employee records are retained when a branch is inactive.</p></div></div>
                    <form class="management-create-form" method="POST" action="{{ route('admin.branches.store') }}">
                        @csrf
                        <label class="form-field">Branch name<input name="name" value="{{ old('name') }}" required maxlength="255" placeholder="e.g. Yangon Office"></label>
                        <label class="form-field">Address<input name="address" value="{{ old('address') }}" maxlength="255"></label>
                        <label class="form-field">Phone<input name="phone" value="{{ old('phone') }}" maxlength="255"></label>
                        <button class="button button-primary" type="submit">Add branch</button>
                    </form>
                    <div class="management-table-wrap">
                        <table class="management-table">
                            <thead><tr><th>Name</th><th>Address</th><th>Phone</th><th>Locations</th><th>Employees</th><th>Status</th><th>Actions</th></tr></thead>
                            <tbody>
                                @forelse ($branches as $branch)
                                    <tr>
                                        <td><form id="branch-{{ $branch->id }}" method="POST" action="{{ route('admin.branches.update', $branch) }}">@csrf @method('PUT')</form><input form="branch-{{ $branch->id }}" name="name" value="{{ $branch->name }}" required maxlength="255" aria-label="Branch name"></td>
                                        <td><input form="branch-{{ $branch->id }}" name="address" value="{{ $branch->address }}" maxlength="255" aria-label="Branch address"></td>
                                        <td><input form="branch-{{ $branch->id }}" name="phone" value="{{ $branch->phone }}" maxlength="255" aria-label="Branch phone"></td>
                                        <td>{{ $branch->locations_count }}</td>
                                        <td>{{ $branch->employees_count }}</td>
                                        <td><select form="branch-{{ $branch->id }}" name="is_active" aria-label="Branch status"><option value="1" @selected($branch->is_active)>Active</option><option value="0" @selected(! $branch->is_active)>Inactive</option></select></td>
                                        <td>
                                            <button class="button button-secondary" form="branch-{{ $branch->id }}" type="submit">Update</button>
                                            <form method="POST" action="{{ route('admin.branches.destroy', $branch) }}" onsubmit="return confirm('Delete this branch from active use? Existing locations and employee records will be retained.');">
                                                @csrf @method('DELETE')
                                                <button class="button button-secondary" type="submit">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="7">No branches yet. Add one above.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </section>
                @endif

                @if ($section === 'users')
                <section class="management-section" id="users">
                    <div class="management-section-heading"><div><p class="eyebrow">ACCESS CONTROL</p><h2>User roles</h2><p>Assign a role and department, then activate or deactivate each account. At least one active administrator must remain.</p></div></div>
                    <div class="management-table-wrap">
                        <table class="management-table">
                            <thead><tr><th>Name</th><th>Email</th><th>Role</th><th>Department</th><th>Account</th><th>Actions</th></tr></thead>
                            <tbody>
                                @forelse ($users as $user)
                                    <tr>
                                        <td><form id="user-{{ $user->id }}" method="POST" action="{{ route('admin.users.update', $user) }}">@csrf @method('PUT')</form>{{ $user->name }} @if ($user->is(auth()->user()))<span class="management-note">(you)</span>@endif</td>
                                        <td>{{ $user->email }}</td>
                                        <td><select form="user-{{ $user->id }}" name="role_id" aria-label="Role for {{ $user->name }}">@foreach ($roles as $role)<option value="{{ $role->id }}" @selected($user->role_id === $role->id)>{{ $role->name }}</option>@endforeach</select></td>
                                        <td><select form="user-{{ $user->id }}" name="department_id" aria-label="Department for {{ $user->name }}"><option value="">Not assigned</option>@foreach ($departments as $department)<option value="{{ $department->id }}" @selected($user->employee?->department_id === $department->id)>{{ $department->name }}</option>@endforeach</select></td>
                                        <td><select form="user-{{ $user->id }}" name="is_active" aria-label="Account status for {{ $user->name }}"><option value="1" @selected($user->is_active)>Active</option><option value="0" @selected(! $user->is_active)>Inactive</option></select></td>
                                        <td>
                                            <button class="button button-secondary" form="user-{{ $user->id }}" type="submit">Update</button>
                                            <form method="POST" action="{{ route('admin.users.destroy', $user) }}" onsubmit="return confirm('Deactivate this user account? Their records will be retained.');">
                                                @csrf @method('DELETE')
                                                <button class="button button-secondary" type="submit" @disabled($user->is(auth()->user()))>Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="6">No user accounts found.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </section>
                @endif

                @if ($section === 'maintenance')
                <section class="management-section" id="maintenance">
                    <div class="management-section-heading"><div><p class="eyebrow">REPAIRS</p><h2>Maintenance</h2><p>Record a repair request, assign a technician, and track progress. In-progress work updates the asset status and history.</p></div></div>
                    <form class="management-create-form maintenance-create-form" method="POST" enctype="multipart/form-data" action="{{ route('admin.maintenance.store') }}">
                        @csrf
                        <label class="form-field">Asset<select name="asset_id" required><option value="">Select asset</option>@foreach ($assets->whereNotIn('status', ['disposed', 'under_maintenance']) as $asset)<option value="{{ $asset->id }}">{{ $asset->asset_code }} · {{ $asset->name }}</option>@endforeach</select></label>
                        <label class="form-field">Assign technician<select name="assigned_to_user_id"><option value="">Unassigned</option>@foreach ($technicians as $technician)<option value="{{ $technician->id }}">{{ $technician->name }}</option>@endforeach</select></label>
                        <label class="form-field">Problem description<input name="description" required maxlength="5000" placeholder="Describe the issue"></label>
                        <label class="form-field">Photo attachment<input type="file" name="photo" accept="image/jpeg,image/png,image/webp"></label>
                        <button class="button button-primary" type="submit">Create request</button>
                    </form>
                    <div class="management-table-wrap">
                        <table class="management-table">
                            <thead><tr><th>Reported</th><th>Asset</th><th>Issue / photo</th><th>Technician</th><th>Status</th><th>Cost</th><th>Completed</th><th>Actions</th></tr></thead>
                            <tbody>
                                @forelse ($maintenanceRecords as $record)
                                    <tr>
                                        <td><form id="maintenance-{{ $record->id }}" method="POST" action="{{ route('admin.maintenance.update', $record) }}">@csrf @method('PUT')</form>{{ $record->reported_at->format('Y-m-d') }}</td>
                                        <td>{{ $record->asset->asset_code }} · {{ $record->asset->name }}</td>
                                        <td>{{ $record->description }} @if ($record->attachment_path)<br><a href="{{ route('operations.attachments.download', ['type' => 'maintenance', 'id' => $record->id]) }}">View photo</a>@endif</td>
                                        <td><select form="maintenance-{{ $record->id }}" name="assigned_to_user_id" aria-label="Technician"><option value="">Unassigned</option>@foreach ($technicians as $technician)<option value="{{ $technician->id }}" @selected($record->assigned_to_user_id === $technician->id)>{{ $technician->name }}</option>@endforeach</select></td>
                                        <td><select form="maintenance-{{ $record->id }}" name="status" aria-label="Maintenance status">@foreach (['requested', 'in_progress', 'completed'] as $status)<option value="{{ $status }}" @selected($record->status === $status)>{{ str($status)->replace('_', ' ')->title() }}</option>@endforeach</select></td>
                                        <td><input form="maintenance-{{ $record->id }}" type="number" min="0" step="0.01" name="cost" value="{{ $record->cost }}" placeholder="—" aria-label="Cost"></td>
                                        <td><input form="maintenance-{{ $record->id }}" type="datetime-local" name="completed_at" value="{{ $record->completed_at?->format('Y-m-d\TH:i') ?? now()->format('Y-m-d\TH:i') }}" aria-label="Completion date"></td>
                                        <td>
                                            <button class="button button-secondary" form="maintenance-{{ $record->id }}" type="submit">Update</button>
                                            <form method="POST" action="{{ route('admin.maintenance.destroy', $record) }}" onsubmit="return confirm('Delete this completed maintenance record from active lists? Its history will remain stored.');">
                                                @csrf @method('DELETE')
                                                <button class="button button-secondary" type="submit" @disabled($record->status !== 'completed')>Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="8">No maintenance requests yet.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </section>
                @endif

                @if ($section === 'settings')
                <section class="management-section" id="settings">
                    <div class="management-section-heading"><div><p class="eyebrow">ORGANIZATION</p><h2>Company settings</h2><p>These details are stored in the application database, not in Git or environment credentials.</p></div></div>
                    <form class="management-settings-form" method="POST" action="{{ route('admin.settings.update') }}">
                        @csrf
                        @method('PUT')
                        <label class="form-field">Company name<input name="company_name" value="{{ old('company_name', $settings->company_name) }}" required maxlength="255"></label>
                        <label class="form-field">Contact email<input name="contact_email" type="email" value="{{ old('contact_email', $settings->contact_email) }}" maxlength="255"></label>
                        <label class="form-field">Phone<input name="phone" value="{{ old('phone', $settings->phone) }}" maxlength="50"></label>
                        <label class="form-field">Timezone<input name="timezone" value="{{ old('timezone', $settings->timezone) }}" required list="timezones" placeholder="e.g. Asia/Yangon"><datalist id="timezones">@foreach (timezone_identifiers_list() as $timezone)<option value="{{ $timezone }}">@endforeach</datalist></label>
                        <label class="form-field">Currency code<input name="currency" value="{{ old('currency', $settings->currency) }}" required minlength="3" maxlength="3" pattern="[A-Za-z]{3}" placeholder="USD"></label>
                        <button class="button button-primary" type="submit">Update settings</button>
                    </form>
                </section>
                @endif

                <footer class="page-footer"><span>© {{ now()->year }} Assetflow</span><span>Administration</span></footer>
            </div>
        </main>
    </div>
</body>
</html>
