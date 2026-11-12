<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#111a2b">
    <title>Asset overview · Axora</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <div class="admin-shell admin-dashboard">
        <aside class="sidebar" id="sidebar">
            <a class="brand" href="/" aria-label="Axora home">
                <span class="brand-mark" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none"><path d="M4.5 7.25 12 3l7.5 4.25v9.5L12 21l-7.5-4.25v-9.5Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/><path d="m4.8 7.4 7.2 4.1 7.2-4.1M12 11.6V21" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/></svg>
                </span>
                <span class="brand-copy"><strong>Ax<span>ora</span></strong><small>CONTROL CENTER</small></span>
            </a>

            <div class="sidebar-section-label">WORKSPACE</div>
            <nav class="main-nav" aria-label="Main navigation">
                <a class="nav-link active" href="#overview" aria-current="page">
                    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><rect x="3.5" y="3.5" width="7" height="7" rx="1.5" stroke="currentColor" stroke-width="1.7"/><rect x="13.5" y="3.5" width="7" height="7" rx="1.5" stroke="currentColor" stroke-width="1.7"/><rect x="3.5" y="13.5" width="7" height="7" rx="1.5" stroke="currentColor" stroke-width="1.7"/><rect x="13.5" y="13.5" width="7" height="7" rx="1.5" stroke="currentColor" stroke-width="1.7"/></svg>
                    <span>Dashboard</span>
                </a>
                <a class="nav-link" href="#assets">
                    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M4 7.5h16M6.5 4h11a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2h-11a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2Z" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/><path d="M8 11.5h8M8 15.5h5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg>
                    <span>Asset registry</span><span class="nav-count">{{ number_format($totalAssets) }}</span>
                </a>
                <div class="sidebar-section-label sidebar-section-spaced">ASSET OPERATIONS</div>
                <a class="nav-link" href="{{ route('operations.assignments') }}"><span>Assignments &amp; returns</span></a>
                <a class="nav-link" href="{{ route('operations.transfers') }}"><span>Asset transfers</span></a>
                <a class="nav-link" href="{{ route('operations.requests') }}"><span>Request approvals</span></a>
                <a class="nav-link" href="{{ route('operations.reports') }}"><span>Reports &amp; history</span></a>
                <a class="nav-link" href="{{ route('operations.purchases') }}"><span>Purchases &amp; warranties</span></a>
                <a class="nav-link" href="{{ route('operations.verification') }}"><span>Verification &amp; disposal</span></a>
                <a class="nav-link" href="{{ route('operations.maintenance') }}"><span>Maintenance requests</span></a>
                <a class="nav-link" href="{{ route('operations.notifications') }}"><span>Notifications</span><span class="nav-count">{{ auth()->user()->unreadNotifications()->count() }}</span></a>
                <div class="sidebar-section-label sidebar-section-spaced">MANAGE</div>
                <a class="nav-link" href="{{ route('admin.categories') }}">
                    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="m12 3 9 5-9 5-9-5 9-5Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/><path d="m3 12 9 5 9-5M3 16l9 5 9-5" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/></svg>
                    <span>Categories</span>
                </a>
                <a class="nav-link" href="{{ route('admin.locations') }}">
                    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M19 10.1c0 5-7 10.4-7 10.4S5 15.1 5 10.1a7 7 0 1 1 14 0Z" stroke="currentColor" stroke-width="1.7"/><circle cx="12" cy="10" r="2.3" stroke="currentColor" stroke-width="1.7"/></svg>
                    <span>Locations</span>
                </a>
                <a class="nav-link" href="{{ route('admin.branches') }}"><span>Branches</span></a>
                <a class="nav-link" href="{{ route('admin.departments') }}"><span>Departments</span></a>
                <a class="nav-link" href="{{ route('admin.users') }}">
                    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle cx="9" cy="8" r="3.2" stroke="currentColor" stroke-width="1.7"/><path d="M3.5 20v-1.6a5.5 5.5 0 0 1 11 0V20M16 5.2a3.2 3.2 0 0 1 0 6.2m1.3 3.7a5.3 5.3 0 0 1 3.2 4.9V20" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg>
                    <span>User roles</span>
                </a>
                <a class="nav-link" href="{{ route('admin.maintenance') }}">
                    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M14.2 6.1a5 5 0 0 0-6.3 6.3L3.7 16.6a2.1 2.1 0 0 0 3 3l4.2-4.2a5 5 0 0 0 6.3-6.3l-3 3-3.1-3.1 3.1-2.9Z" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    <span>Maintenance</span><span class="nav-dot"></span>
                </a>
                <a class="nav-link" href="{{ route('admin.settings') }}">
                    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="1.7"/><path d="m19.4 15 .1.1a1.8 1.8 0 1 1-2.5 2.5l-.1-.1a1.8 1.8 0 0 0-3 .9v.2a1.8 1.8 0 1 1-3.6 0v-.2a1.8 1.8 0 0 0-3-.9l-.1.1a1.8 1.8 0 1 1-2.5-2.5l.1-.1a1.8 1.8 0 0 0-.9-3h-.2a1.8 1.8 0 1 1 0-3.6h.2a1.8 1.8 0 0 0 .9-3l-.1-.1a1.8 1.8 0 1 1 2.5-2.5l.1.1a1.8 1.8 0 0 0 3-.9v-.2a1.8 1.8 0 1 1 3.6 0v.2a1.8 1.8 0 0 0 3 .9l.1-.1a1.8 1.8 0 1 1 2.5 2.5l-.1.1a1.8 1.8 0 0 0 .9 3h.2a1.8 1.8 0 1 1 0 3.6h-.2a1.8 1.8 0 0 0-.9 3Z" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    <span>Settings</span>
                </a>
            </nav>

            <div class="sidebar-bottom">
                <div class="sidebar-user">
                    <span class="user-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                    <span class="user-details"><strong>{{ auth()->user()->name }}</strong><small>Administrator</small></span>
                    <button class="icon-button sidebar-more" type="button" aria-label="Account options">···</button>
                </div>
            </div>
        </aside>

        <main class="main-panel" id="overview">
            <header class="topbar">
                <button class="icon-button menu-toggle" type="button" aria-label="Toggle navigation" aria-expanded="false" aria-controls="sidebar">
                    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M4 6h16M4 12h16M4 18h16" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                </button>
                <div class="breadcrumbs"><span>Workspace</span><span class="breadcrumb-slash">/</span><strong>Dashboard</strong></div>
                <div class="topbar-actions">
                    <span class="topbar-date"><span class="online-dot"></span> System operational</span>
                    <button class="icon-button notification-button" type="button" aria-label="Notifications">
                        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M18 9a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9ZM10 21h4" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        <span class="notification-dot"></span>
                    </button>
                    <span class="topbar-divider"></span>
                    <span class="topbar-user">{{ auth()->user()->name }}</span>
                    <span class="user-avatar topbar-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="view-switch" type="submit">Sign out</button>
                    </form>
                </div>
            </header>

            <div class="page-content">
                <section class="page-heading">
                    <div>
                        <p class="eyebrow">OVERVIEW</p>
                        @php
                            $hour = now('Asia/Yangon')->hour;
                        @endphp
                        <h1>Good {{ $hour < 12 ? 'morning' : ($hour < 18 ? 'afternoon' : 'evening') }}, {{ auth()->user()->name }}</h1>
                        <p class="page-subtitle">Here’s what’s happening with your assets today.</p>
                    </div>
                </section>

                <section class="stats-grid" aria-label="Asset summary">
                    <article class="stat-card">
                        <div class="stat-top"><span class="stat-icon icon-blue"><svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><rect x="4" y="4" width="16" height="16" rx="2.5" stroke="currentColor" stroke-width="1.7"/><path d="M8 8h8v8H8zM9 2v4m6-4v4M9 18v4m6-4v4M2 9h4m-4 6h4m12-6h4m-4 6h4" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg></span><span class="stat-trend trend-blue">All assets</span></div>
                        <p class="stat-label">Total Assets</p><div class="stat-value">{{ number_format($totalAssets) }}</div><p class="stat-foot"><span class="stat-foot-muted">Registered in the system</span></p>
                    </article>
                    <article class="stat-card">
                        <div class="stat-top"><span class="stat-icon icon-green"><svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="m5 12.5 4.3 4.3L19.5 6.7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.7"/></svg></span><span class="stat-trend trend-green">Ready</span></div>
                        <p class="stat-label">Available Assets</p><div class="stat-value">{{ number_format($availableAssets) }}</div><p class="stat-foot"><span class="stat-foot-muted">Ready for assignment</span></p>
                    </article>
                    <article class="stat-card">
                        <div class="stat-top"><span class="stat-icon icon-blue"><svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle cx="9" cy="8" r="3.2" stroke="currentColor" stroke-width="1.7"/><path d="M3.5 20v-1.6a5.5 5.5 0 0 1 11 0V20M16 5.2a3.2 3.2 0 0 1 0 6.2m1.3 3.7a5.3 5.3 0 0 1 3.2 4.9V20" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg></span><span class="stat-trend trend-blue">In use</span></div>
                        <p class="stat-label">Assigned Assets</p><div class="stat-value">{{ number_format($assignedAssets) }}</div><p class="stat-foot"><span class="stat-foot-muted">Assigned to employees or departments</span></p>
                    </article>
                    <article class="stat-card">
                        <div class="stat-top"><span class="stat-icon icon-amber"><svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M12 8v4l2.5 2.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/><circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.7"/></svg></span><span class="stat-trend trend-amber">In repair</span></div>
                        <p class="stat-label">Under Maintenance</p><div class="stat-value">{{ number_format($maintenanceAssets) }}</div><p class="stat-foot"><span class="stat-foot-muted">Currently being repaired or maintained</span></p>
                    </article>
                    <article class="stat-card">
                        <div class="stat-top"><span class="stat-icon icon-red"><svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M12 8v4m0 4h.01M10.3 3.9 2.8 17a2 2 0 0 0 1.7 3h15a2 2 0 0 0 1.7-3l-7.5-13.1a2 2 0 0 0-3.4 0Z" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg></span><span class="stat-trend trend-amber">Needs attention</span></div>
                        <p class="stat-label">Damaged Assets</p><div class="stat-value">{{ number_format($damagedAssets) }}</div><p class="stat-foot"><span class="stat-foot-muted">Marked as damaged</span></p>
                    </article>
                    <article class="stat-card">
                        <div class="stat-top"><span class="stat-icon icon-red"><svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M4 7h16M10 11v6m4-6v6M5.5 7l1 13h11l1-13M9 7V4h6v3" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg></span><span class="stat-trend trend-neutral">Inactive</span></div>
                        <p class="stat-label">Disposed Assets</p><div class="stat-value">{{ number_format($disposedAssets) }}</div><p class="stat-foot"><span class="stat-foot-muted">Marked as disposed</span></p>
                    </article>
                </section>

                <section class="inventory-section activity-section" aria-labelledby="activity-heading">
                    <div class="section-heading">
                        <div><h2 id="activity-heading">Recent Activities</h2><p>Latest registrations, assignments, returns, transfers and status changes.</p></div>
                        <a class="text-link" href="{{ route('operations.history') }}">View activity history <span aria-hidden="true">→</span></a>
                    </div>
                    <div class="inventory-card activity-card">
                        @forelse ($recentActivities as $activity)
                            <article class="activity-row">
                                <span class="activity-marker" aria-hidden="true"></span>
                                <div class="activity-copy">
                                    <p>{{ $activity->description ?: str($activity->action_type)->replace('_', ' ')->title() }}</p>
                                    <span>{{ $activity->asset?->asset_code ?? 'Asset activity' }} · {{ $activity->user?->name ?? 'System' }}</span>
                                </div>
                                <time datetime="{{ $activity->occurred_at?->toIso8601String() }}">{{ $activity->occurred_at?->diffForHumans() ?? 'Unknown time' }}</time>
                            </article>
                        @empty
                            <p class="activity-empty">No recent activities yet.</p>
                        @endforelse
                    </div>
                </section>

                <section class="inventory-section" id="assets">
                    <div class="section-heading">
                        <div><h2>Asset registry</h2><p>A quick look at your recently updated assets.</p></div>
                        <button class="button button-primary" type="button" data-open-modal>
                            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="1.9" stroke-linecap="round"/></svg>
                            Add new asset
                        </button>
                    </div>
                    <div class="inventory-card">
                        <form class="table-toolbar" method="GET" action="{{ route('admin.dashboard') }}">
                            <label class="search-box">
                                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle cx="10.8" cy="10.8" r="6.8" stroke="currentColor" stroke-width="1.7"/><path d="m16 16 4 4" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg>
                                <input id="asset-search" name="q" type="search" value="{{ $filters['q'] ?? '' }}" placeholder="Search assets..." aria-label="Search assets">
                                <kbd>⌘ K</kbd>
                            </label>
                            <div class="toolbar-actions">
                                <label class="filter-select">
                                    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M4 6h16M7 12h10m-7 6h4" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg>
                                    <select id="status-filter" name="status" aria-label="Filter by status">
                                        <option value="">All statuses</option>
                                        <option value="assigned" @selected(($filters['status'] ?? '') === 'assigned')>Assigned</option>
                                        <option value="available" @selected(($filters['status'] ?? '') === 'available')>Available</option>
                                        <option value="under_maintenance" @selected(($filters['status'] ?? '') === 'under_maintenance')>Under maintenance</option>
                                        <option value="damaged" @selected(($filters['status'] ?? '') === 'damaged')>Damaged</option>
                                        <option value="disposed" @selected(($filters['status'] ?? '') === 'disposed')>Disposed</option>
                                    </select>
                                </label>
                                <button class="button button-secondary" type="submit">Search</button>
                                <button class="button button-secondary export-button" type="button" id="export-assets">
                                    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M12 3v12m0 0 4-4m-4 4-4-4M5 16v4h14v-4" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                    Export
                                </button>
                            </div>
                        </form>
                        <div class="table-scroll">
                            <table class="asset-table">
                                <thead><tr><th scope="col">Asset</th><th scope="col">Category</th><th scope="col">Serial number</th><th scope="col">Assigned to</th><th scope="col">Status</th><th scope="col">Actions</th></tr></thead>
                                <tbody id="asset-table-body">
                                    @foreach ($assets as $asset)
                                        @php
                                            $statusClass = match ($asset->status) {
                                                'assigned' => 'status-assigned',
                                                'under_maintenance' => 'status-maintenance',
                                                'damaged' => 'status-damaged',
                                                'disposed' => 'status-disposed',
                                                default => 'status-available',
                                            };
                                            $statusLabel = str($asset->status)->replace('_', ' ')->title();
                                        @endphp
                                        <tr data-status="{{ $asset->status }}">
                                            <td><div class="asset-name"><span class="device-icon device-laptop">▣</span><span><strong>{{ $asset->name }}</strong><small>{{ $asset->asset_code }}</small></span></div></td>
                                            <td>{{ $asset->category->name }}</td>
                                            <td class="serial-cell">{{ $asset->serial_number ?: '—' }}</td>
                                            <td>
                                                @if ($asset->currentEmployee)
                                                    {{ $asset->currentEmployee->first_name }} {{ $asset->currentEmployee->last_name }}
                                                @else
                                                    <span class="unassigned">—</span>
                                                @endif
                                            </td>
                                            <td><span class="status-badge {{ $statusClass }}"><span></span>{{ $statusLabel }}</span></td>
                                            <td><button class="button button-secondary asset-edit-button" type="button"
                                                data-edit-asset
                                                data-id="{{ $asset->id }}"
                                                data-name="{{ $asset->name }}"
                                                data-category-id="{{ $asset->category_id }}"
                                                data-category-name="{{ $asset->category->name }}"
                                                data-category-active="{{ $asset->category->is_active ? 'true' : 'false' }}"
                                                data-location-id="{{ $asset->location_id }}"
                                                data-location-name="{{ $asset->location?->name }}"
                                                data-location-active="{{ $asset->location?->is_active ? 'true' : 'false' }}"
                                                data-asset-code="{{ $asset->asset_code }}"
                                                data-serial-number="{{ $asset->serial_number }}"
                                                data-brand="{{ $asset->brand }}"
                                                data-model="{{ $asset->model }}"
                                                data-purchase-date="{{ $asset->purchase_date?->format('Y-m-d') }}"
                                                data-purchase-cost="{{ $asset->purchase_cost }}"
                                                data-warranty-expiry-date="{{ $asset->warranty_expiry_date?->format('Y-m-d') }}"
                                                data-condition="{{ $asset->condition }}"
                                                data-status="{{ $asset->status }}"
                                                data-description="{{ $asset->description }}">Edit</button></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <div class="empty-state" id="empty-state" @if ($assets->isNotEmpty()) hidden @endif>No assets match your search.</div>
                        </div>
                        <footer class="table-footer"><span>Showing <strong id="visible-count">{{ $assets->count() }}</strong> of <strong id="total-count">{{ $assets->total() }}</strong> matching assets</span>{{ $assets->links() }}</footer>
                    </div>
                </section>
                <footer class="page-footer"><span>© {{ now()->year }} Axora</span><span>Asset control made simple.</span></footer>
            </div>
        </main>
    </div>

    <div class="modal-backdrop" id="asset-modal" data-update-url="{{ route('admin.assets.update', ['asset' => '__ASSET__']) }}" @if ($errors->any()) @else hidden @endif>
        <section class="asset-modal" role="dialog" aria-modal="true" aria-labelledby="modal-title">
            <div class="modal-heading"><div><p class="eyebrow">ASSET REGISTRY</p><h2 id="modal-title">Add a new asset</h2></div><button class="icon-button modal-close" type="button" aria-label="Close dialog" data-close-modal>×</button></div>
            <p class="modal-description" data-modal-description>Enter the details below to add an item to your registry.</p>
            <form id="asset-form" method="POST" action="{{ route('admin.assets.store') }}" data-create-url="{{ route('admin.assets.store') }}">
                @csrf
                <input type="hidden" name="_method" value="PUT" data-edit-method @if (old('_method') !== 'PUT') disabled @endif>
                <input type="hidden" name="asset_id" value="{{ old('asset_id') }}" data-edit-id>
                <label class="form-field">Asset name<input name="name" value="{{ old('name') }}" required maxlength="255" placeholder="e.g. MacBook Pro 16"></label>
                @error('name')<p class="form-error">{{ $message }}</p>@enderror
                <div class="form-row">
                    <label class="form-field">Category<select name="category_id" required><option value="">Select category</option>@foreach ($categories as $category)<option value="{{ $category->id }}" @selected(old('category_id') == $category->id)>{{ $category->name }}</option>@endforeach</select></label>
                    <label class="form-field">Asset code<input name="asset_code" value="{{ old('asset_code') }}" required maxlength="255" placeholder="e.g. AST-1006"></label>
                </div>
                @error('category_id')<p class="form-error">{{ $message }}</p>@enderror
                @error('asset_code')<p class="form-error">{{ $message }}</p>@enderror
                <div class="form-row">
                    <label class="form-field">Serial number<input name="serial_number" value="{{ old('serial_number') }}" maxlength="255" placeholder="e.g. SN-1234567"></label>
                    <label class="form-field">Location<select name="location_id"><option value="">No location</option>@foreach ($locations as $location)<option value="{{ $location->id }}" @selected(old('location_id') == $location->id)>{{ $location->name }}</option>@endforeach</select></label>
                </div>
                @error('serial_number')<p class="form-error">{{ $message }}</p>@enderror
                @error('location_id')<p class="form-error">{{ $message }}</p>@enderror
                <div class="form-row"><label class="form-field">Brand<input name="brand" value="{{ old('brand') }}" maxlength="255"></label><label class="form-field">Model<input name="model" value="{{ old('model') }}" maxlength="255"></label></div>
                <div class="form-row"><label class="form-field">Purchase date<input name="purchase_date" type="date" value="{{ old('purchase_date') }}"></label><label class="form-field">Purchase cost<input name="purchase_cost" type="number" min="0" step="0.01" value="{{ old('purchase_cost') }}"></label></div>
                <div class="form-row">
                    <label class="form-field">Warranty expiry<input name="warranty_expiry_date" type="date" value="{{ old('warranty_expiry_date') }}"></label>
                    <label class="form-field">Condition<select name="condition" required><option value="good" @selected(old('condition', 'good') === 'good')>Good</option><option value="fair" @selected(old('condition') === 'fair')>Fair</option><option value="poor" @selected(old('condition') === 'poor')>Poor</option></select></label>
                </div>
                <div class="form-row">
                    <label class="form-field" data-status-field>Status<select name="status" required><option value="available" @selected(old('status', 'available') === 'available')>Available</option><option value="assigned" disabled>Assigned</option><option value="under_maintenance" @selected(old('status') === 'under_maintenance')>Under maintenance</option><option value="damaged" @selected(old('status') === 'damaged')>Damaged</option><option value="disposed" @selected(old('status') === 'disposed')>Disposed</option></select></label>
                    <label class="form-field">Description<input name="description" value="{{ old('description') }}" maxlength="5000"></label>
                </div>
                <p class="modal-description" data-status-note hidden>Asset status is managed in the Status &amp; condition section to preserve assignment and maintenance records.</p>
                @foreach (['purchase_date', 'purchase_cost', 'warranty_expiry_date', 'condition', 'status', 'description'] as $field)
                    @error($field)<p class="form-error">{{ $message }}</p>@enderror
                @endforeach
                <div class="modal-actions"><button class="button button-secondary" type="button" data-close-modal>Cancel</button><button class="button button-primary" type="submit" data-submit-label>Add asset</button></div>
            </form>
        </section>
    </div>
    @if (session('status'))
        <div class="toast is-visible" id="toast" role="status" aria-live="polite">{{ session('status') }}</div>
    @else
    <div class="toast" id="toast" role="status" aria-live="polite"></div>
    @endif
</body>
</html>
