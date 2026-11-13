<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#111a2b">
    <title>{{ $pageTitle }} · Axora</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <div class="admin-shell">
        <aside class="sidebar" id="sidebar">
            <a class="brand" href="{{ route('admin.dashboard') }}">
                <span class="brand-mark" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none"><path d="M4.5 7.25 12 3l7.5 4.25v9.5L12 21l-7.5-4.25v-9.5Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/><path d="m4.8 7.4 7.2 4.1 7.2-4.1M12 11.6V21" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/></svg>
                </span>
                <span class="brand-copy"><strong>Ax<span>ora</span></strong><small>CONTROL CENTER</small></span>
            </a>
            <nav class="main-nav" aria-label="Administration">
                <div class="sidebar-section-label">WORKSPACE</div>
                <a class="nav-link" href="{{ route('admin.dashboard') }}">
                    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><rect x="3.5" y="3.5" width="7" height="7" rx="1.5" stroke="currentColor" stroke-width="1.7"/><rect x="13.5" y="3.5" width="7" height="7" rx="1.5" stroke="currentColor" stroke-width="1.7"/><rect x="3.5" y="13.5" width="7" height="7" rx="1.5" stroke="currentColor" stroke-width="1.7"/><rect x="13.5" y="13.5" width="7" height="7" rx="1.5" stroke="currentColor" stroke-width="1.7"/></svg>
                    <span>Dashboard</span>
                </a>
                <a class="nav-link" href="{{ route('admin.dashboard') }}#assets">
                    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M4 7.5h16M6.5 4h11a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2h-11a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2Z" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/><path d="M8 11.5h8M8 15.5h5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg>
                    <span>Asset registry</span>
                </a>
                <div class="sidebar-section-label sidebar-section-spaced">ASSET OPERATIONS</div>
                <a class="nav-link" href="{{ route('operations.assignments') }}">
                    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M8 7h12m0 0l-4-4m4 4l-4 4m-8 6H4m0 0l4 4m-4-4l4-4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    <span>Assignments &amp; returns</span>
                </a>
                <a class="nav-link" href="{{ route('operations.transfers') }}">
                    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M8 7h12m0 0l-4-4m4 4l-4 4m-8 6H4m0 0l4 4m-4-4l4-4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    <span>Asset transfers</span>
                </a>
                <a class="nav-link" href="{{ route('operations.requests') }}">
                    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    <span>Request approvals</span>
                </a>
                <a class="nav-link" href="{{ route('operations.reports') }}">
                    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    <span>Reports &amp; history</span>
                </a>
                <a class="nav-link" href="{{ route('operations.purchases') }}">
                    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    <span>Purchases &amp; warranties</span>
                </a>
                <a class="nav-link" href="{{ route('operations.verification') }}">
                    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    <span>Verification &amp; disposal</span>
                </a>
                <a class="nav-link" href="{{ route('operations.maintenance') }}">
                    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    <span>Maintenance requests</span>
                </a>
                <a class="nav-link" href="{{ route('operations.notifications') }}">
                    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    <span>Notifications</span>
                </a>
                <div class="sidebar-section-label sidebar-section-spaced">MANAGE</div>
                <a class="nav-link {{ $section === 'categories' ? 'active' : '' }}" @if ($section === 'categories') aria-current="page" @endif href="{{ route('admin.categories') }}">
                    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="m12 3 9 5-9 5-9-5 9-5Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/><path d="m3 12 9 5 9-5M3 16l9 5 9-5" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/></svg>
                    <span>Categories</span>
                </a>
                <a class="nav-link {{ $section === 'locations' ? 'active' : '' }}" @if ($section === 'locations') aria-current="page" @endif href="{{ route('admin.locations') }}">
                    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M19 10.1c0 5-7 10.4-7 10.4S5 15.1 5 10.1a7 7 0 1 1 14 0Z" stroke="currentColor" stroke-width="1.7"/><circle cx="12" cy="10" r="2.3" stroke="currentColor" stroke-width="1.7"/></svg>
                    <span>Locations</span>
                </a>
                <a class="nav-link {{ $section === 'branches' ? 'active' : '' }}" @if ($section === 'branches') aria-current="page" @endif href="{{ route('admin.branches') }}">
                    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m-1 4h1m3-8h1m-1 4h1m-1 4h1m-5 11v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    <span>Branches</span>
                </a>
                <a class="nav-link {{ $section === 'departments' ? 'active' : '' }}" @if ($section === 'departments') aria-current="page" @endif href="{{ route('admin.departments') }}">
                    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    <span>Departments</span>
                </a>
                <a class="nav-link {{ $section === 'users' ? 'active' : '' }}" @if ($section === 'users') aria-current="page" @endif href="{{ route('admin.users') }}">
                    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle cx="9" cy="8" r="3.2" stroke="currentColor" stroke-width="1.7"/><path d="M3.5 20v-1.6a5.5 5.5 0 0 1 11 0V20M16 5.2a3.2 3.2 0 0 1 0 6.2m1.3 3.7a5.3 5.3 0 0 1 3.2 4.9V20" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg>
                    <span>User roles</span>
                </a>
                <a class="nav-link {{ $section === 'maintenance' ? 'active' : '' }}" @if ($section === 'maintenance') aria-current="page" @endif href="{{ route('admin.maintenance') }}">
                    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M14.2 6.1a5 5 0 0 0-6.3 6.3L3.7 16.6a2.1 2.1 0 0 0 3 3l4.2-4.2a5 5 0 0 0 6.3-6.3l-3 3-3.1-3.1 3.1-2.9Z" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    <span>Maintenance</span>
                </a>
                <a class="nav-link {{ $section === 'settings' ? 'active' : '' }}" @if ($section === 'settings') aria-current="page" @endif href="{{ route('admin.settings') }}">
                    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="1.7"/><path d="m19.4 15 .1.1a1.8 1.8 0 1 1-2.5 2.5l-.1-.1a1.8 1.8 0 0 0-3 .9v.2a1.8 1.8 0 1 1-3.6 0v-.2a1.8 1.8 0 0 0-3-.9l-.1.1a1.8 1.8 0 1 1-2.5-2.5l.1-.1a1.8 1.8 0 0 0-.9-3h-.2a1.8 1.8 0 1 1 0-3.6h.2a1.8 1.8 0 0 0 .9-3l-.1-.1a1.8 1.8 0 1 1 2.5-2.5l.1.1a1.8 1.8 0 0 0 3-.9v-.2a1.8 1.8 0 1 1 3.6 0v.2a1.8 1.8 0 0 0 3 .9l.1-.1a1.8 1.8 0 1 1 2.5 2.5l-.1.1a1.8 1.8 0 0 0 .9 3h.2a1.8 1.8 0 1 1 0 3.6h-.2a1.8 1.8 0 0 0-.9 3Z" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    <span>Settings</span>
                </a>
            </nav>
            <div class="sidebar-bottom">
                <div class="sidebar-user">
                    <span class="user-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                    <div class="user-details">
                        <strong>{{ auth()->user()->name }}</strong>
                        <small>Administrator</small>
                    </div>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="logout-button" type="submit">
                        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        Sign out
                    </button>
                </form>
            </div>
        </aside>

        <main class="main-panel">
            <header class="topbar">
                <div class="topbar-left">
                    <button class="icon-button menu-toggle" type="button" aria-label="Toggle navigation" aria-expanded="false" aria-controls="sidebar">
                        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M4 6h16M4 12h16M4 18h16" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                    </button>
                    <div class="breadcrumbs">
                        <a href="{{ route('admin.dashboard') }}">Workspace</a>
                        <span class="breadcrumb-slash">/</span>
                        <strong>{{ $pageTitle }}</strong>
                    </div>
                </div>

                <div class="topbar-center">
                    <div class="global-search">
                        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle cx="11" cy="11" r="8" stroke="currentColor" stroke-width="2"/><path d="m21 21-4.35-4.35" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                        <input type="text" placeholder="Search {{ strtolower($pageTitle) }}... (⌘K)" aria-label="Global search">
                    </div>
                </div>

                <div class="topbar-right">
                    <div class="topbar-actions">
                        <button class="icon-button help-button" type="button" aria-label="Help">
                            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2"/><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3m0 4h.01" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                        </button>
                        <button class="icon-button notification-button" type="button" aria-label="Notifications">
                            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M18 9a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9ZM10 21h4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            <span class="notification-dot"></span>
                        </button>
                        <div class="topbar-divider"></div>
                        <div class="user-profile-trigger">
                            <div class="user-info">
                                <span class="user-name">{{ auth()->user()->name }}</span>
                                <span class="user-role">Administrator</span>
                            </div>
                            <span class="user-avatar topbar-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                        </div>
                    </div>
                </div>
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
                    <div class="management-section-heading locations-heading">
                        <div><p class="eyebrow">PLACES</p><h2>Locations</h2><p>Organize assets by branch, office, room or storage area.</p></div>
                        <a class="button button-secondary" href="{{ route('admin.branches') }}">Manage branches</a>
                    </div>
                    <form class="management-create-form" method="POST" action="{{ route('admin.locations.store') }}">
                        @csrf
                        <label class="form-field">Name<input name="name" value="{{ old('name') }}" required maxlength="255" placeholder="e.g. Main office"></label>
                        <label class="form-field">Type<select name="location_type" required><option value="branch">Branch</option><option value="office" selected>Office</option><option value="room">Room</option><option value="storage">Storage</option><option value="other">Other</option></select></label>
                        <label class="form-field">Branch<select name="branch_id"><option value="">No branch</option>@foreach ($branches->where('is_active', true) as $branch)<option value="{{ $branch->id }}">{{ $branch->name }}</option>@endforeach</select></label>
                        <button class="button button-primary" type="submit">Add location</button>
                    </form>
                    <div class="management-table-wrap">
                        <table class="management-table">
                            <thead><tr><th>Name</th><th>Type</th><th>Branch</th><th>Assets</th><th>Status</th><th>Actions</th></tr></thead>
                            <tbody>
                                @forelse ($locations as $location)
                                    <tr>
                                        <td>{{ $location->name }}</td>
                                        <td>{{ ucfirst($location->location_type) }}</td>
                                        <td>{{ $location->branch?->name ?? 'None' }}</td>
                                        <td>{{ $location->assets_count }}</td>
                                        <td>{{ $location->is_active ? 'Active' : 'Archived' }}</td>
                                        <td>
                                            <button class="button button-secondary" type="button" data-location-edit aria-expanded="false" aria-controls="location-edit-{{ $location->id }}">Update</button>
                                            <form method="POST" action="{{ route('admin.locations.destroy', $location) }}" onsubmit="return confirm('Delete this location from active use? Existing asset records will be retained.');">
                                                @csrf @method('DELETE')
                                                <button class="button button-secondary" type="submit">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                    <tr id="location-edit-{{ $location->id }}" data-location-edit-row hidden>
                                        <td colspan="6">
                                            <form class="management-create-form location-edit-form" method="POST" action="{{ route('admin.locations.update', $location) }}">
                                                @csrf @method('PUT')
                                                <label class="form-field">Name<input name="name" value="{{ $location->name }}" required maxlength="255"></label>
                                                <label class="form-field">Type<select name="location_type" required>@foreach (['branch', 'office', 'room', 'storage', 'other'] as $type)<option value="{{ $type }}" @selected($location->location_type === $type)>{{ ucfirst($type) }}</option>@endforeach</select></label>
                                                <label class="form-field">Branch<select name="branch_id"><option value="">No branch</option>@foreach ($branches as $branch)@if ($branch->is_active || $location->branch_id === $branch->id)<option value="{{ $branch->id }}" @selected($location->branch_id === $branch->id)>{{ $branch->name }}{{ $branch->is_active ? '' : ' (inactive)' }}</option>@endif @endforeach</select></label>
                                                <label class="form-field">Status<select name="is_active" required><option value="1" @selected($location->is_active)>Active</option><option value="0" @selected(! $location->is_active)>Archived</option></select></label>
                                                <div class="modal-actions">
                                                    <button class="button button-secondary" type="button" data-location-edit-cancel>Cancel</button>
                                                    <button class="button button-primary" type="submit">Save changes</button>
                                                </div>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="6">No locations yet. Add one above.</td></tr>
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
                            <thead><tr><th>Name</th><th>Email</th><th>Phone</th><th>Role</th><th>Department</th><th>Account</th><th>Actions</th></tr></thead>
                            <tbody>
                                @forelse ($users as $user)
                                    <tr>
                                        <td><form id="user-{{ $user->id }}" method="POST" action="{{ route('admin.users.update', $user) }}">@csrf @method('PUT')</form>{{ $user->name }} @if ($user->is(auth()->user()))<span class="management-note">(you)</span>@endif</td>
                                        <td>{{ $user->email }}</td>
                                        <td><input form="user-{{ $user->id }}" name="phone" type="tel" value="{{ $user->phone }}" placeholder="+959..." aria-label="Myanmar phone number for {{ $user->name }}"></td>
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
                                    <tr><td colspan="7">No user accounts found.</td></tr>
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

                <footer class="page-footer"><span>© {{ now()->year }} Axora</span><span>Administration</span></footer>
            </div>
        </main>
    </div>
</body>
</html>
