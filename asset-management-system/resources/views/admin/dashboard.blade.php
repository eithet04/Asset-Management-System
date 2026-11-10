<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#111a2b">
    <title>Asset overview · Asset control</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <div class="admin-shell">
        <aside class="sidebar" id="sidebar">
            <a class="brand" href="/" aria-label="Asset control home">
                <span class="brand-mark" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none"><path d="M4.5 7.25 12 3l7.5 4.25v9.5L12 21l-7.5-4.25v-9.5Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/><path d="m4.8 7.4 7.2 4.1 7.2-4.1M12 11.6V21" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/></svg>
                </span>
                <span class="brand-copy"><strong>Asset<span>flow</span></strong><small>CONTROL CENTER</small></span>
            </a>

            <div class="sidebar-section-label">WORKSPACE</div>
            <nav class="main-nav" aria-label="Main navigation">
                <a class="nav-link active" href="#overview" aria-current="page">
                    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><rect x="3.5" y="3.5" width="7" height="7" rx="1.5" stroke="currentColor" stroke-width="1.7"/><rect x="13.5" y="3.5" width="7" height="7" rx="1.5" stroke="currentColor" stroke-width="1.7"/><rect x="3.5" y="13.5" width="7" height="7" rx="1.5" stroke="currentColor" stroke-width="1.7"/><rect x="13.5" y="13.5" width="7" height="7" rx="1.5" stroke="currentColor" stroke-width="1.7"/></svg>
                    <span>Dashboard</span>
                </a>
                <a class="nav-link" href="#assets">
                    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M4 7.5h16M6.5 4h11a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2h-11a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2Z" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/><path d="M8 11.5h8M8 15.5h5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg>
                    <span>Asset registry</span><span class="nav-count">1,250</span>
                </a>
                <a class="nav-link" href="#categories">
                    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="m12 3 9 5-9 5-9-5 9-5Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/><path d="m3 12 9 5 9-5M3 16l9 5 9-5" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/></svg>
                    <span>Categories</span>
                </a>
                <a class="nav-link" href="#locations">
                    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M19 10.1c0 5-7 10.4-7 10.4S5 15.1 5 10.1a7 7 0 1 1 14 0Z" stroke="currentColor" stroke-width="1.7"/><circle cx="12" cy="10" r="2.3" stroke="currentColor" stroke-width="1.7"/></svg>
                    <span>Locations</span>
                </a>

                <div class="sidebar-section-label sidebar-section-spaced">MANAGE</div>
                <a class="nav-link" href="#users">
                    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle cx="9" cy="8" r="3.2" stroke="currentColor" stroke-width="1.7"/><path d="M3.5 20v-1.6a5.5 5.5 0 0 1 11 0V20M16 5.2a3.2 3.2 0 0 1 0 6.2m1.3 3.7a5.3 5.3 0 0 1 3.2 4.9V20" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg>
                    <span>User roles</span>
                </a>
                <a class="nav-link" href="#maintenance">
                    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M14.2 6.1a5 5 0 0 0-6.3 6.3L3.7 16.6a2.1 2.1 0 0 0 3 3l4.2-4.2a5 5 0 0 0 6.3-6.3l-3 3-3.1-3.1 3.1-2.9Z" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    <span>Maintenance</span><span class="nav-dot"></span>
                </a>
                <a class="nav-link" href="#settings">
                    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="1.7"/><path d="m19.4 15 .1.1a1.8 1.8 0 1 1-2.5 2.5l-.1-.1a1.8 1.8 0 0 0-3 .9v.2a1.8 1.8 0 1 1-3.6 0v-.2a1.8 1.8 0 0 0-3-.9l-.1.1a1.8 1.8 0 1 1-2.5-2.5l.1-.1a1.8 1.8 0 0 0-.9-3h-.2a1.8 1.8 0 1 1 0-3.6h.2a1.8 1.8 0 0 0 .9-3l-.1-.1a1.8 1.8 0 1 1 2.5-2.5l.1.1a1.8 1.8 0 0 0 3-.9v-.2a1.8 1.8 0 1 1 3.6 0v.2a1.8 1.8 0 0 0 3 .9l.1-.1a1.8 1.8 0 1 1 2.5 2.5l-.1.1a1.8 1.8 0 0 0 .9 3h.2a1.8 1.8 0 1 1 0 3.6h-.2a1.8 1.8 0 0 0-.9 3Z" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    <span>Settings</span>
                </a>
            </nav>

            <div class="sidebar-bottom">
                <div class="help-card">
                    <span class="help-icon">?</span>
                    <strong>Need a hand?</strong>
                    <p>Visit the help center for tips and guides.</p>
                    <a href="mailto:support@example.com">Open help center <span aria-hidden="true">↗</span></a>
                </div>
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
                        <h1>Good morning, {{ auth()->user()->name }} <span class="wave" aria-hidden="true">✦</span></h1>
                        <p class="page-subtitle">Here’s what’s happening with your assets today.</p>
                    </div>
                    <button class="button button-primary" type="button" data-open-modal>
                        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="1.9" stroke-linecap="round"/></svg>
                        Add new asset
                    </button>
                </section>

                <section class="stats-grid" aria-label="Asset summary">
                    <article class="stat-card">
                        <div class="stat-top"><span class="stat-icon icon-blue"><svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><rect x="4" y="4" width="16" height="16" rx="2.5" stroke="currentColor" stroke-width="1.7"/><path d="M8 8h8v8H8zM9 2v4m6-4v4M9 18v4m6-4v4M2 9h4m-4 6h4m12-6h4m-4 6h4" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg></span><span class="stat-trend trend-blue">All time</span></div>
                        <p class="stat-label">Total registered</p><div class="stat-value">1,250</div><p class="stat-foot"><span class="trend-positive">↗ 8.2%</span> <span>from last month</span></p>
                    </article>
                    <article class="stat-card">
                        <div class="stat-top"><span class="stat-icon icon-green"><svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="m5 12.5 4.3 4.3L19.5 6.7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.7"/></svg></span><span class="stat-trend trend-green">78.4%</span></div>
                        <p class="stat-label">Active units</p><div class="stat-value">980</div><p class="stat-foot"><span class="stat-foot-muted">Currently in use</span></p>
                    </article>
                    <article class="stat-card">
                        <div class="stat-top"><span class="stat-icon icon-amber"><svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M12 8v4l2.5 2.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/><circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.7"/></svg></span><span class="stat-trend trend-amber">Needs attention</span></div>
                        <p class="stat-label">Under repair</p><div class="stat-value">45</div><p class="stat-foot"><span class="stat-foot-muted">12 due back this week</span></p>
                    </article>
                    <article class="stat-card">
                        <div class="stat-top"><span class="stat-icon icon-red"><svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M4 7h16M10 11v6m4-6v6M5.5 7l1 13h11l1-13M9 7V4h6v3" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg></span><span class="stat-trend trend-neutral">This year</span></div>
                        <p class="stat-label">Disposed</p><div class="stat-value">22</div><p class="stat-foot"><span class="stat-foot-muted">Safely decommissioned</span></p>
                    </article>
                </section>

                <section class="inventory-section" id="assets">
                    <div class="section-heading">
                        <div><h2>Asset registry</h2><p>A quick look at your recently updated assets.</p></div>
                        <a class="text-link" href="#assets">View all assets <span aria-hidden="true">→</span></a>
                    </div>
                    <div class="inventory-card">
                        <div class="table-toolbar">
                            <label class="search-box">
                                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle cx="10.8" cy="10.8" r="6.8" stroke="currentColor" stroke-width="1.7"/><path d="m16 16 4 4" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg>
                                <input id="asset-search" type="search" placeholder="Search assets..." aria-label="Search assets">
                                <kbd>⌘ K</kbd>
                            </label>
                            <div class="toolbar-actions">
                                <label class="filter-select">
                                    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M4 6h16M7 12h10m-7 6h4" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg>
                                    <select id="status-filter" aria-label="Filter by status">
                                        <option value="">All statuses</option>
                                        <option value="Assigned">Assigned</option>
                                        <option value="Available">Available</option>
                                        <option value="Under maintenance">Under maintenance</option>
                                        <option value="Damaged">Damaged</option>
                                    </select>
                                </label>
                                <button class="button button-secondary export-button" type="button" id="export-assets">
                                    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M12 3v12m0 0 4-4m-4 4-4-4M5 16v4h14v-4" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                    Export
                                </button>
                            </div>
                        </div>
                        <div class="table-scroll">
                            <table class="asset-table">
                                <thead><tr><th scope="col">Asset</th><th scope="col">Category</th><th scope="col">Serial number</th><th scope="col">Assigned to</th><th scope="col">Status</th><th scope="col">Actions</th></tr></thead>
                                <tbody id="asset-table-body">
                                    <tr data-status="Assigned"><td><div class="asset-name"><span class="device-icon device-laptop"><svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><rect x="5" y="4" width="14" height="11" rx="1.5" stroke="currentColor" stroke-width="1.7"/><path d="M3 18h18l-1.5 2h-15L3 18Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/></svg></span><span><strong>MacBook Pro 16"</strong><small>AST-1001</small></span></div></td><td>Laptop</td><td class="serial-cell">SN-8839201</td><td><span class="assignee"><span class="person-avatar avatar-purple">JD</span> Jordan Davis</span></td><td><span class="status-badge status-assigned"><span></span>Assigned</span></td><td><button class="row-menu" type="button" aria-label="More actions for MacBook Pro">···</button></td></tr>
                                    <tr data-status="Available"><td><div class="asset-name"><span class="device-icon device-laptop"><svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><rect x="5" y="4" width="14" height="11" rx="1.5" stroke="currentColor" stroke-width="1.7"/><path d="M3 18h18l-1.5 2h-15L3 18Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/></svg></span><span><strong>Dell XPS 15</strong><small>AST-1002</small></span></div></td><td>Laptop</td><td class="serial-cell">SN-9940122</td><td><span class="unassigned">—</span></td><td><span class="status-badge status-available"><span></span>Available</span></td><td><button class="row-menu" type="button" aria-label="More actions for Dell XPS">···</button></td></tr>
                                    <tr data-status="Under maintenance"><td><div class="asset-name"><span class="device-icon device-printer"><svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M7 8V4h10v4M6 17H4V9h16v8h-2M7 14h10v6H7v-6Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/><path d="M17 11h.01" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"/></svg></span><span><strong>Canon imageRUNNER</strong><small>AST-1003</small></span></div></td><td>Printer</td><td class="serial-cell">SN-1102933</td><td><span class="unassigned">—</span></td><td><span class="status-badge status-maintenance"><span></span>Under maintenance</span></td><td><button class="row-menu" type="button" aria-label="More actions for Canon imageRUNNER">···</button></td></tr>
                                    <tr data-status="Damaged"><td><div class="asset-name"><span class="device-icon device-desktop"><svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><rect x="3" y="4" width="18" height="13" rx="1.5" stroke="currentColor" stroke-width="1.7"/><path d="M12 17v3m-4 0h8M9 8l2 2-2 2m4 0h2" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg></span><span><strong>HP ZBook 17</strong><small>AST-1004</small></span></div></td><td>Desktop</td><td class="serial-cell">SN-5540192</td><td><span class="assignee"><span class="person-avatar avatar-orange">MC</span> Morgan Chen</span></td><td><span class="status-badge status-damaged"><span></span>Damaged</span></td><td><button class="row-menu" type="button" aria-label="More actions for HP ZBook">···</button></td></tr>
                                    <tr data-status="Assigned"><td><div class="asset-name"><span class="device-icon device-mobile"><svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><rect x="7" y="2.5" width="10" height="19" rx="2" stroke="currentColor" stroke-width="1.7"/><path d="M10 5h4m-2 13.5h.01" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg></span><span><strong>iPhone 14 Pro</strong><small>AST-1005</small></span></div></td><td>Mobile</td><td class="serial-cell">SN-7729104</td><td><span class="assignee"><span class="person-avatar avatar-green">AT</span> Alex Taylor</span></td><td><span class="status-badge status-assigned"><span></span>Assigned</span></td><td><button class="row-menu" type="button" aria-label="More actions for iPhone 14 Pro">···</button></td></tr>
                                </tbody>
                            </table>
                            <div class="empty-state" id="empty-state" hidden>No assets match your search.</div>
                        </div>
                        <footer class="table-footer"><span>Showing <strong id="visible-count">5</strong> of <strong id="total-count">1,250</strong> assets</span><div class="pagination"><button type="button" disabled aria-label="Previous page">‹</button><button type="button" class="page-current" aria-current="page">1</button><button type="button">2</button><button type="button">3</button><span>…</span><button type="button">25</button><button type="button" aria-label="Next page">›</button></div></footer>
                    </div>
                </section>
                <footer class="page-footer"><span>© 2026 Assetflow</span><span>Asset control made simple.</span></footer>
            </div>
        </main>
    </div>

    <div class="modal-backdrop" id="asset-modal" hidden>
        <section class="asset-modal" role="dialog" aria-modal="true" aria-labelledby="modal-title">
            <div class="modal-heading"><div><p class="eyebrow">ASSET REGISTRY</p><h2 id="modal-title">Add a new asset</h2></div><button class="icon-button modal-close" type="button" aria-label="Close dialog" data-close-modal>×</button></div>
            <p class="modal-description">Enter the details below to add an item to your registry.</p>
            <form id="asset-form">
                <label class="form-field">Asset name<input name="name" required placeholder="e.g. MacBook Pro 16"></label>
                <div class="form-row"><label class="form-field">Category<select name="category" required><option value="">Select category</option><option>Laptop</option><option>Desktop</option><option>Printer</option><option>Mobile</option><option>Other</option></select></label><label class="form-field">Serial number<input name="serial" required placeholder="e.g. SN-1234567"></label></div>
                <div class="form-row"><label class="form-field">Asset code<input name="code" required placeholder="e.g. AST-1006"></label><label class="form-field">Status<select name="status"><option>Available</option><option>Assigned</option><option>Under maintenance</option><option>Damaged</option></select></label></div>
                <div class="modal-actions"><button class="button button-secondary" type="button" data-close-modal>Cancel</button><button class="button button-primary" type="submit">Add asset</button></div>
            </form>
        </section>
    </div>
    <div class="toast" id="toast" role="status" aria-live="polite"></div>
</body>
</html>
