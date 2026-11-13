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

            <nav class="main-nav" aria-label="Main navigation">
                <div class="sidebar-section-label">WORKSPACE</div>
                <a class="nav-link active" href="#overview" aria-current="page">
                    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><rect x="3.5" y="3.5" width="7" height="7" rx="1.5" stroke="currentColor" stroke-width="1.7"/><rect x="13.5" y="3.5" width="7" height="7" rx="1.5" stroke="currentColor" stroke-width="1.7"/><rect x="3.5" y="13.5" width="7" height="7" rx="1.5" stroke="currentColor" stroke-width="1.7"/><rect x="13.5" y="13.5" width="7" height="7" rx="1.5" stroke="currentColor" stroke-width="1.7"/></svg>
                    <span>Dashboard</span>
                </a>
                <a class="nav-link" href="#assets">
                    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M4 7.5h16M6.5 4h11a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2h-11a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2Z" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/><path d="M8 11.5h8M8 15.5h5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg>
                    <span>Asset registry</span><span class="nav-count">{{ number_format($totalAssets) }}</span>
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
                <div class="sidebar-section-label sidebar-section-spaced">MANAGE</div>
                <a class="nav-link" href="{{ route('admin.categories') }}">
                    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="m12 3 9 5-9 5-9-5 9-5Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/><path d="m3 12 9 5 9-5M3 16l9 5 9-5" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/></svg>
                    <span>Categories</span>
                </a>
                <a class="nav-link" href="{{ route('admin.locations') }}">
                    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M19 10.1c0 5-7 10.4-7 10.4S5 15.1 5 10.1a7 7 0 1 1 14 0Z" stroke="currentColor" stroke-width="1.7"/><circle cx="12" cy="10" r="2.3" stroke="currentColor" stroke-width="1.7"/></svg>
                    <span>Locations</span>
                </a>
                <a class="nav-link" href="{{ route('admin.branches') }}">
                    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m-1 4h1m3-8h1m-1 4h1m-1 4h1m-5 11v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    <span>Branches</span>
                </a>
                <a class="nav-link" href="{{ route('admin.departments') }}">
                    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    <span>Departments</span>
                </a>
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

        <main class="main-panel" id="overview">
            <header class="topbar">
                <div class="topbar-left">
                    <button class="icon-button menu-toggle" type="button" aria-label="Toggle navigation" aria-expanded="false" aria-controls="sidebar">
                        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M4 6h16M4 12h16M4 18h16" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                    </button>
                    <div class="breadcrumbs">
                        <span>Workspace</span>
                        <span class="breadcrumb-slash">/</span>
                        <strong>Dashboard</strong>
                    </div>
                </div>

                <div class="topbar-center">
                    <div class="global-search">
                        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle cx="11" cy="11" r="8" stroke="currentColor" stroke-width="2"/><path d="m21 21-4.35-4.35" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                        <input type="text" placeholder="Search anything... (⌘K)" aria-label="Global search">
                    </div>
                </div>

                <div class="topbar-right">
                    <div class="topbar-actions">
                        <button class="icon-button help-button" type="button" aria-label="Help">
                            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2"/><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3m0 4h.01" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                        </button>
                        @include('components.theme-toggle')
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
                                <a class="button button-secondary export-button" id="export-assets" href="{{ route('admin.assets.export', $filters) }}">
                                    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M12 3v12m0 0 4-4m-4 4-4-4M5 16v4h14v-4" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                    Export
                                </a>
                            </div>
                        </form>
                        <form class="asset-import-form" method="POST" action="{{ route('admin.assets.import') }}" enctype="multipart/form-data">
                            @csrf
                            <label class="form-field">Import asset CSV
                                <input name="csv_file" type="file" accept=".csv,text/csv" required>
                            </label>
                            <p class="asset-import-hint">Use columns: Asset, Asset code, Category, Serial number, Location, Purchase Date, Purchase Cost, Warranty expiry, Condition, Status. Dates: YYYY-MM-DD. Leave optional values blank. Assigned assets must use the assignment workflow.</p>
                            <button class="button button-secondary" type="submit">Import CSV</button>
                        </form>
                        @if ($errors->import->any())
                            <div class="management-alert management-alert-error" role="alert">
                                <ul>
                                    @foreach ($errors->import->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        <div class="table-scroll">
                            <table class="asset-table">
                                <thead><tr><th scope="col">Asset</th><th scope="col">Category</th><th scope="col">Serial number</th><th scope="col">Location</th><th scope="col">Purchase date</th><th scope="col">Purchase cost</th><th scope="col">Warranty expiry</th><th scope="col">Condition</th><th scope="col">Assigned to</th><th scope="col">Status</th><th scope="col">Actions</th></tr></thead>
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
                                            <td>{{ $asset->location?->name ?? '—' }}</td>
                                            <td>{{ $asset->purchase_date?->format('M j, Y') ?? '—' }}</td>
                                            <td>{{ $asset->purchase_cost !== null ? number_format((float) $asset->purchase_cost, 2) : '—' }}</td>
                                            <td>{{ $asset->warranty_expiry_date?->format('M j, Y') ?? '—' }}</td>
                                            <td>{{ str($asset->condition)->title() }}</td>
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
                                                data-purchase-date="{{ $asset->purchase_date?->format('Y-m-d') }}"
                                                data-purchase-cost="{{ $asset->purchase_cost }}"
                                                data-warranty-expiry-date="{{ $asset->warranty_expiry_date?->format('Y-m-d') }}"
                                                data-condition="{{ $asset->condition }}"
                                                data-status="{{ $asset->status }}">Edit</button></td>
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

    <div class="modal-backdrop" id="asset-modal" data-update-url="{{ route('admin.assets.update', ['asset' => '__ASSET__']) }}" @if ($errors->any() || session('asset_form_success')) @else hidden @endif>
        <section class="asset-modal" role="dialog" aria-modal="true" aria-labelledby="modal-title">
            <div class="modal-heading"><div><p class="eyebrow">ASSET REGISTRY</p><h2 id="modal-title">Add a new asset</h2></div><button class="icon-button modal-close" type="button" aria-label="Close dialog" data-close-modal>×</button></div>
            <p class="modal-description" data-modal-description>Enter the details below to add an item to your registry.</p>
            @if (session('asset_form_success'))
                <p class="management-alert" role="status" aria-live="polite" data-asset-form-success>{{ session('asset_form_success') }}</p>
            @endif
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
                <div class="form-row"><label class="form-field">Purchase date<input name="purchase_date" type="date" value="{{ old('purchase_date') }}"></label><label class="form-field">Purchase cost<input name="purchase_cost" type="number" min="0" step="0.01" value="{{ old('purchase_cost') }}"></label></div>
                <div class="form-row">
                    <label class="form-field">Warranty expiry<input name="warranty_expiry_date" type="date" value="{{ old('warranty_expiry_date') }}"></label>
                    <label class="form-field">Condition<select name="condition" required><option value="good" @selected(old('condition', 'good') === 'good')>Good</option><option value="fair" @selected(old('condition') === 'fair')>Fair</option><option value="poor" @selected(old('condition') === 'poor')>Poor</option></select></label>
                </div>
                <div class="form-row">
                    <label class="form-field" data-status-field>Status<select name="status" required><option value="available" @selected(old('status', 'available') === 'available')>Available</option><option value="assigned" disabled>Assigned</option><option value="under_maintenance" @selected(old('status') === 'under_maintenance')>Under maintenance</option><option value="damaged" @selected(old('status') === 'damaged')>Damaged</option><option value="disposed" @selected(old('status') === 'disposed')>Disposed</option></select></label>
                </div>
                <p class="modal-description" data-status-note hidden>Asset status is managed in the Status &amp; condition section to preserve assignment and maintenance records.</p>
                @foreach (['purchase_date', 'purchase_cost', 'warranty_expiry_date', 'condition', 'status'] as $field)
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
