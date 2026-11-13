<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#111a2b">
    <title>My workspace · Axora</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <div class="admin-shell employee-dashboard">
        <aside class="sidebar" id="sidebar">
            <a class="brand" href="/" aria-label="Axora home">
                <span class="brand-mark" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none"><path d="M4.5 7.25 12 3l7.5 4.25v9.5L12 21l-7.5-4.25v-9.5Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/><path d="m4.8 7.4 7.2 4.1 7.2-4.1M12 11.6V21" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/></svg>
                </span>
                <span class="brand-copy"><strong>Ax<span>ora</span></strong><small>CONTROL CENTER</small></span>
            </a>

            <nav class="main-nav" aria-label="Employee navigation">
                <div class="sidebar-section-label">WORKSPACE</div>
                <a class="nav-link active" href="{{ route('employee.dashboard') }}" aria-current="page">
                    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><rect x="3.5" y="3.5" width="7" height="7" rx="1.5" stroke="currentColor" stroke-width="1.7"/><rect x="13.5" y="3.5" width="7" height="7" rx="1.5" stroke="currentColor" stroke-width="1.7"/><rect x="3.5" y="13.5" width="7" height="7" rx="1.5" stroke="currentColor" stroke-width="1.7"/><rect x="13.5" y="13.5" width="7" height="7" rx="1.5" stroke="currentColor" stroke-width="1.7"/></svg>
                    <span>Dashboard</span>
                </a>
                <a class="nav-link" href="{{ route('employee.requests') }}">
                    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    <span>My requests</span>
                </a>
                <a class="nav-link" href="#report-issue" data-open-employee-modal="issue">
                    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="m12 3 9 16H3l9-16Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/><path d="M12 9v4m0 3h.01" stroke="currentColor" stroke-width="1.9" stroke-linecap="round"/></svg>
                    <span>Report issue</span>
                </a>
            </nav>

            <div class="sidebar-bottom">
                <div class="sidebar-user">
                    <span class="user-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                    <div class="user-details">
                        <strong>{{ auth()->user()->name }}</strong>
                        <small>Employee</small>
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
                        <input type="text" placeholder="Search my assets, requests... (⌘K)" aria-label="Global search">
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
                                <span class="user-role">Employee</span>
                            </div>
                            <span class="user-avatar topbar-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                        </div>
                    </div>
                </div>
            </header>

            <div class="page-content employee-page">
                <section class="page-heading">
                    <div>
                        <p class="eyebrow">EMPLOYEE SELF-SERVICE</p>
                        <h1>Welcome back, {{ auth()->user()->name }}</h1>
                        <p class="page-subtitle">Your assigned equipment and service requests at a glance.</p>
                    </div>
                    <a class="button button-primary" href="{{ route('employee.requests') }}">
                        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="1.9" stroke-linecap="round"/></svg>
                        Request an asset
                    </a>
                </section>

                <section class="employee-stats" aria-label="My workspace summary">
                    <article class="employee-stat employee-stat-blue">
                        <div class="employee-stat-icon">
                            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><rect x="4" y="4" width="16" height="16" rx="2.5" stroke="currentColor" stroke-width="1.7"/><path d="M8 8h8v8H8zM9 2v4m6-4v4M9 18v4m6-4v4M2 9h4m-4 6h4m12-6h4m-4 6h4" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg>
                        </div>
                        <div><p>Assigned assets</p><strong id="employee-asset-count">{{ $assignedAssets->count() }} <span>units</span></strong></div>
                    </article>
                    <article class="employee-stat employee-stat-orange">
                        <div class="employee-stat-icon">
                            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M12 8v4l2.5 2.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/><circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.7"/></svg>
                        </div>
                        <div><p>My requests</p><strong><span id="employee-request-count">{{ $pendingRequestCount }}</span> <span>pending</span></strong></div>
                    </article>
                    <article class="employee-stat employee-stat-green">
                        <div class="employee-stat-icon">
                            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="m5 12.5 4.3 4.3L19.5 6.7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.7"/></svg>
                        </div>
                        <div><p>System status</p><strong class="verified-status"><span></span>Verified</strong></div>
                    </article>
                </section>

                <section class="inventory-section employee-assets-section" id="my-assets">
                    <div class="section-heading">
                        <div><h2>My assigned assets</h2><p>Equipment currently assigned to your account.</p></div>
                        <span class="employee-assets-total"><span id="employee-table-count">{{ $assignedAssets->count() }}</span> items</span>
                    </div>
                    <div class="inventory-card">
                        <div class="table-toolbar">
                            <label class="search-box">
                                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle cx="10.8" cy="10.8" r="6.8" stroke="currentColor" stroke-width="1.7"/><path d="m16 16 4 4" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg>
                                <input id="employee-asset-search" type="search" placeholder="Search my assets..." aria-label="Search my assets">
                            </label>
                            <button class="button button-secondary export-button" type="button" id="export-employee-assets">
                                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M12 3v12m0 0 4-4m-4 4-4-4M5 16v4h14v-4" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                Export my list
                            </button>
                        </div>
                        <div class="table-scroll">
                            <table class="asset-table employee-table">
                                <thead>
                                    <tr><th scope="col">My item code</th><th scope="col">Item name</th><th scope="col">Category</th><th scope="col">Assigned date</th><th scope="col">Status</th></tr>
                                </thead>
                                <tbody id="employee-asset-table-body">
                                    @forelse ($assignedAssets as $asset)
                                        <tr data-employee-asset="{{ strtolower($asset->asset_code.' '.$asset->name.' '.$asset->category?->name.' '.$asset->status) }}">
                                            <td class="employee-code">{{ $asset->asset_code }}</td>
                                            <td><div class="employee-item-name"><strong>{{ $asset->name }}</strong></div></td>
                                            <td>{{ $asset->category?->name ?? '—' }}</td>
                                            <td class="serial-cell">{{ $asset->assignments->first()?->assigned_at?->format('M j, Y') ?? '—' }}</td>
                                            <td><span class="status-badge status-{{ str_replace('_', '-', $asset->status) }}"><span></span>{{ ucfirst(str_replace('_', ' ', $asset->status)) }}</span></td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="5">No assets are currently assigned to your account.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                            <div class="empty-state" id="employee-assets-empty" hidden>No assigned assets match your search.</div>
                        </div>
                        <footer class="table-footer employee-table-footer"><span>Showing <strong id="employee-visible-count">{{ $assignedAssets->count() }}</strong> of <strong id="employee-table-total">{{ $assignedAssets->count() }}</strong> assigned assets</span><span class="employee-private-note"><svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><rect x="5" y="10" width="14" height="11" rx="2" stroke="currentColor" stroke-width="1.7"/><path d="M8 10V7a4 4 0 1 1 8 0v3" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg> Only visible to you</span></footer>
                    </div>
                </section>

                <section class="employee-request-strip" aria-label="Request help">
                    <span class="employee-request-icon"><svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="m12 3 9 16H3l9-16Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/><path d="M12 9v4m0 3h.01" stroke="currentColor" stroke-width="1.9" stroke-linecap="round"/></svg></span>
                    <span><strong>Something not working?</strong><small>Report an issue with one of your assigned assets and your IT team will follow up.</small></span>
                    <button class="button button-secondary" type="button" data-open-employee-modal="issue">Report an issue</button>
                </section>
                <footer class="page-footer"><span>© {{ now()->year }} Axora</span><span>Your workspace · Private to your account</span></footer>
            </div>
        </main>
    </div>

    <div class="modal-backdrop" id="employee-asset-modal" hidden>
        <section class="asset-modal employee-modal" role="dialog" aria-modal="true" aria-labelledby="employee-asset-modal-title">
            <div class="modal-heading"><div><p class="eyebrow">SELF-SERVICE REQUEST</p><h2 id="employee-asset-modal-title">Request an asset</h2></div><button class="icon-button modal-close" type="button" aria-label="Close dialog" data-close-employee-modal>×</button></div>
            <p class="modal-description">Tell your IT team what you need. Your request will be sent for approval.</p>
            <form id="employee-asset-form">
                <label class="form-field">Asset type<select name="asset_type" required><option value="">Select an asset type</option><option>Laptop</option><option>Monitor</option><option>Mobile phone</option><option>Keyboard or mouse</option><option>Other equipment</option></select></label>
                <label class="form-field">Reason for request<textarea name="reason" rows="3" required maxlength="500" placeholder="Let us know why you need this asset"></textarea></label>
                <div class="modal-actions"><button class="button button-secondary" type="button" data-close-employee-modal>Cancel</button><button class="button button-primary" type="submit">Submit request</button></div>
            </form>
        </section>
    </div>

    <div class="modal-backdrop" id="employee-issue-modal" hidden>
        <section class="asset-modal employee-modal" role="dialog" aria-modal="true" aria-labelledby="employee-issue-modal-title">
            <div class="modal-heading"><div><p class="eyebrow">IT SUPPORT</p><h2 id="employee-issue-modal-title">Report an issue</h2></div><button class="icon-button modal-close" type="button" aria-label="Close dialog" data-close-employee-modal>×</button></div>
            <p class="modal-description">Describe the problem and your IT team will review your repair ticket.</p>
            <form id="employee-issue-form">
                <label class="form-field">Affected asset<select name="asset_code" required><option value="">Select an assigned asset</option><option value="AST-1001">AST-1001 · MacBook Pro 16"</option><option value="AST-9021">AST-9021 · Dell 27" Monitor</option></select></label>
                <label class="form-field">Issue details<textarea name="description" rows="4" required maxlength="1000" placeholder="What happened? Include any helpful details"></textarea></label>
                <label class="form-field">Priority<select name="priority" required><option>Normal</option><option>Urgent</option></select></label>
                <div class="modal-actions"><button class="button button-secondary" type="button" data-close-employee-modal>Cancel</button><button class="button button-primary" type="submit">Submit repair ticket</button></div>
            </form>
        </section>
    </div>
    <div class="toast" id="toast" role="status" aria-live="polite"></div>
</body>
</html>
