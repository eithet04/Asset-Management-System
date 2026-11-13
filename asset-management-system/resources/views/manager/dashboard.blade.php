<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#111a2b">
    <title>Manager workspace · Axora</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <div class="admin-shell manager-dashboard">
        <aside class="sidebar" id="sidebar">
            <a class="brand" href="/" aria-label="Axora home">
                <span class="brand-mark" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none"><path d="M4.5 7.25 12 3l7.5 4.25v9.5L12 21l-7.5-4.25v-9.5Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/><path d="m4.8 7.4 7.2 4.1 7.2-4.1M12 11.6V21" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/></svg>
                </span>
                <span class="brand-copy"><strong>Ax<span>ora</span></strong><small>CONTROL CENTER</small></span>
            </a>

            <nav class="main-nav" aria-label="Manager navigation">
                <div class="sidebar-section-label">WORKSPACE</div>
                <a class="nav-link active" href="{{ route('manager.dashboard') }}" aria-current="page">
                    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><rect x="3.5" y="3.5" width="7" height="7" rx="1.5" stroke="currentColor" stroke-width="1.7"/><rect x="13.5" y="3.5" width="7" height="7" rx="1.5" stroke="currentColor" stroke-width="1.7"/><rect x="3.5" y="13.5" width="7" height="7" rx="1.5" stroke="currentColor" stroke-width="1.7"/><rect x="13.5" y="13.5" width="7" height="7" rx="1.5" stroke="currentColor" stroke-width="1.7"/></svg>
                    <span>Dashboard</span>
                </a>
                <div class="sidebar-section-label sidebar-section-spaced">ASSET OPERATIONS</div>
                <a class="nav-link" href="{{ route('operations.requests') }}">
                    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    <span>Request approvals</span>
                </a>
                <a class="nav-link" href="{{ route('operations.purchases') }}">
                    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    <span>Purchases</span>
                </a>
                <a class="nav-link" href="{{ route('operations.warranties') }}">
                    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    <span>Warranties</span>
                </a>
                <a class="nav-link" href="{{ route('operations.verification') }}">
                    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    <span>Verification</span>
                </a>
                <a class="nav-link" href="{{ route('operations.disposals') }}">
                    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    <span>Disposals</span>
                </a>
                <a class="nav-link" href="{{ route('operations.maintenance') }}">
                    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    <span>Maintenance</span>
                </a>
                <a class="nav-link" href="{{ route('operations.reports') }}">
                    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M4 19V5m0 14h17M8 15l4-4 3 2 5-6" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    <span>Reports</span>
                </a>
                <a class="nav-link" href="{{ route('operations.history') }}">
                    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M4 5h16v15H4zM8 9h8m-8 4h8m-8 4h5M8 2v3m8-3v3" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    <span>Audit logs</span>
                </a>
            </nav>

            <div class="sidebar-bottom">
                <div class="sidebar-user">
                    <span class="user-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                    <div class="user-details">
                        <strong>{{ auth()->user()->name }}</strong>
                        <small>Manager · HR / IT</small>
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
                        <input type="text" placeholder="Search assets, requests... (⌘K)" aria-label="Global search">
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
                                <span class="user-role">Manager</span>
                            </div>
                            <span class="user-avatar topbar-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                        </div>
                    </div>
                </div>
            </header>

            <div class="page-content manager-page">
                <section class="page-heading">
                    <div>
                        <p class="eyebrow">MANAGER WORKSPACE</p>
                        <h1>Asset assignments</h1>
                        <p class="page-subtitle">Manage team assignments, transfers, and asset returns.</p>
                    </div>
                    <span class="manager-date"><span class="online-dot"></span> Updated just now</span>
                </section>

                <section class="stats-grid manager-stats" aria-label="Assignment summary">
                    <article class="manager-stat manager-stat-blue">
                        <p class="stat-label">Total assignments</p>
                        <div class="manager-stat-bottom"><strong>980</strong><span>Across all departments</span></div>
                    </article>
                    <article class="manager-stat manager-stat-orange">
                        <p class="stat-label">Pending transfers</p>
                        <div class="manager-stat-bottom"><strong>12</strong><span>Awaiting approval</span></div>
                    </article>
                    <article class="manager-stat manager-stat-cyan">
                        <p class="stat-label">Pending returns</p>
                        <div class="manager-stat-bottom"><strong>5</strong><span>Action required</span></div>
                    </article>
                    <article class="manager-stat manager-stat-green">
                        <p class="stat-label">Dept verification</p>
                        <div class="manager-stat-bottom"><strong>98%</strong><span>Verified this month</span></div>
                    </article>
                </section>

                <section class="inventory-section" id="assignments">
                    <div class="section-heading">
                        <div><h2>Assignments &amp; requests</h2><p>Review asset ownership and take action on pending requests.</p></div>
                        <span class="request-count"><span></span> 17 requests need attention</span>
                    </div>
                    <div class="inventory-card">
                        <div class="table-toolbar">
                            <label class="search-box">
                                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle cx="10.8" cy="10.8" r="6.8" stroke="currentColor" stroke-width="1.7"/><path d="m16 16 4 4" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg>
                                <input id="assignment-search" type="search" placeholder="Search assets, people, or departments..." aria-label="Search assignments">
                                <kbd>⌘ K</kbd>
                            </label>
                            <div class="toolbar-actions">
                                <label class="filter-select">
                                    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M4 6h16M7 12h10m-7 6h4" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg>
                                    <select id="assignment-filter" aria-label="Filter assignments by status">
                                        <option value="">All statuses</option>
                                        <option value="Assigned">Assigned</option>
                                        <option value="Pending Approval">Pending approval</option>
                                        <option value="Return Requested">Return requested</option>
                                        <option value="Stationed">Stationed</option>
                                    </select>
                                </label>
                                <button class="button button-secondary export-button" type="button" id="export-assignments">
                                    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M12 3v12m0 0 4-4m-4 4-4-4M5 16v4h14v-4" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                    Export
                                </button>
                            </div>
                        </div>
                        <div class="table-scroll">
                            <table class="asset-table manager-table">
                                <thead>
                                    <tr>
                                        <th scope="col">Asset</th>
                                        <th scope="col">Assigned to</th>
                                        <th scope="col">Department</th>
                                        <th scope="col">Transfer / return status</th>
                                        <th scope="col">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="assignment-table-body">
                                    <tr data-status="Assigned" data-assignee="Mg Mg" data-department="IT Development" data-asset="MacBook Pro 16&quot;">
                                        <td><div class="asset-name"><span class="device-icon device-laptop"><svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><rect x="5" y="4" width="14" height="11" rx="1.5" stroke="currentColor" stroke-width="1.7"/><path d="M3 18h18l-1.5 2h-15L3 18Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/></svg></span><span><strong>MacBook Pro 16"</strong><small>AST-1001 · Laptop</small></span></div></td>
                                        <td><span class="assignee"><span class="person-avatar avatar-purple">MM</span><span data-assignee-label>Mg Mg</span></span></td>
                                        <td data-department-label>IT Development</td>
                                        <td><span class="status-badge status-assigned" data-manager-status><span></span>Assigned</span></td>
                                        <td><div class="row-actions"><button class="row-action" type="button" data-manager-action="transfer">Transfer</button><button class="row-action row-action-muted" type="button" data-manager-action="return">Return</button></div></td>
                                    </tr>
                                    <tr data-status="Pending Approval" data-assignee="Aung Aung" data-department="HR Team" data-asset="Dell XPS 15">
                                        <td><div class="asset-name"><span class="device-icon device-laptop"><svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><rect x="5" y="4" width="14" height="11" rx="1.5" stroke="currentColor" stroke-width="1.7"/><path d="M3 18h18l-1.5 2h-15L3 18Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/></svg></span><span><strong>Dell XPS 15</strong><small>AST-1002 · Laptop</small></span></div></td>
                                        <td><span class="assignee"><span class="person-avatar avatar-orange">AA</span><span data-assignee-label>Aung Aung</span></span></td>
                                        <td data-department-label>HR Team</td>
                                        <td><span class="status-badge status-pending" data-manager-status><span></span>Pending approval</span></td>
                                        <td><div class="row-actions"><button class="row-action row-action-approve" type="button" data-manager-action="approve">Approve transfer</button></div></td>
                                    </tr>
                                    <tr data-status="Stationed" data-assignee="" data-department="Admin" data-asset="Canon Printer">
                                        <td><div class="asset-name"><span class="device-icon device-printer"><svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M7 8V4h10v4M6 17H4V9h16v8h-2M7 14h10v6H7v-6Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/><path d="M17 11h.01" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"/></svg></span><span><strong>Canon Printer</strong><small>AST-1003 · Printer</small></span></div></td>
                                        <td><span class="unassigned" data-assignee-label>Office Floor 2</span></td>
                                        <td data-department-label>Admin</td>
                                        <td><span class="status-badge status-stationed" data-manager-status><span></span>Stationed</span></td>
                                        <td><div class="row-actions"><button class="row-action" type="button" data-manager-action="reassign">Reassign</button></div></td>
                                    </tr>
                                    <tr data-status="Return Requested" data-assignee="Su Su" data-department="Marketing" data-asset="HP Pavilion">
                                        <td><div class="asset-name"><span class="device-icon device-desktop"><svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><rect x="3" y="4" width="18" height="13" rx="1.5" stroke="currentColor" stroke-width="1.7"/><path d="M12 17v3m-4 0h8M9 8l2 2-2 2m4 0h2" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg></span><span><strong>HP Pavilion</strong><small>AST-1004 · Desktop</small></span></div></td>
                                        <td><span class="assignee"><span class="person-avatar avatar-green">SS</span><span data-assignee-label>Su Su</span></span></td>
                                        <td data-department-label>Marketing</td>
                                        <td><span class="status-badge status-return" data-manager-status><span></span>Return requested</span></td>
                                        <td><div class="row-actions"><button class="row-action row-action-approve" type="button" data-manager-action="accept-return">Accept return</button></div></td>
                                    </tr>
                                    <tr data-status="Assigned" data-assignee="Htun Htun" data-department="Sales" data-asset="ThinkPad T14">
                                        <td><div class="asset-name"><span class="device-icon device-laptop"><svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><rect x="5" y="4" width="14" height="11" rx="1.5" stroke="currentColor" stroke-width="1.7"/><path d="M3 18h18l-1.5 2h-15L3 18Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/></svg></span><span><strong>ThinkPad T14</strong><small>AST-1005 · Laptop</small></span></div></td>
                                        <td><span class="assignee"><span class="person-avatar avatar-purple">HT</span><span data-assignee-label>Htun Htun</span></span></td>
                                        <td data-department-label>Sales</td>
                                        <td><span class="status-badge status-assigned" data-manager-status><span></span>Assigned</span></td>
                                        <td><div class="row-actions"><button class="row-action" type="button" data-manager-action="transfer">Transfer</button><button class="row-action row-action-muted" type="button" data-manager-action="return">Return</button></div></td>
                                    </tr>
                                </tbody>
                            </table>
                            <div class="empty-state" id="assignment-empty-state" hidden>No assignments match your search.</div>
                        </div>
                        <footer class="table-footer">
                            <span>Showing <strong id="assignment-visible-count">5</strong> of <strong>980</strong> assignments</span>
                            <div class="pagination"><button type="button" disabled aria-label="Previous page">‹</button><button type="button" class="page-current" aria-current="page">1</button><button type="button">2</button><button type="button">3</button><span>…</span><button type="button">25</button><button type="button" aria-label="Next page">›</button></div>
                        </footer>
                    </div>
                </section>

                <footer class="page-footer"><span>© {{ now()->year }} Axora</span><span>Manager workspace · Demo data</span></footer>
            </div>
        </main>
    </div>

    <div class="modal-backdrop" id="manager-action-modal" hidden>
        <section class="asset-modal manager-modal" role="dialog" aria-modal="true" aria-labelledby="manager-modal-title">
            <div class="modal-heading">
                <div><p class="eyebrow">ASSIGNMENT WORKFLOW</p><h2 id="manager-modal-title">Request transfer</h2></div>
                <button class="icon-button modal-close" type="button" aria-label="Close dialog" data-close-manager-modal>×</button>
            </div>
            <p class="modal-description" id="manager-modal-description">Choose the new owner and department for this asset.</p>
            <form id="manager-action-form">
                <input type="hidden" name="asset">
                <input type="hidden" name="action">
                <div class="manager-transfer-fields">
                    <label class="form-field">New assignee<input name="assignee" required placeholder="Enter employee name"></label>
                    <label class="form-field">Destination department<select name="department" required><option value="">Select department</option><option>IT Development</option><option>HR Team</option><option>Admin</option><option>Marketing</option><option>Sales</option></select></label>
                </div>
                <label class="form-field manager-return-field" hidden>Return note<textarea name="return_note" rows="3" placeholder="Add a note about the asset return"></textarea></label>
                <div class="modal-actions"><button class="button button-secondary" type="button" data-close-manager-modal>Cancel</button><button class="button button-primary" type="submit" id="manager-modal-submit">Submit request</button></div>
            </form>
        </section>
    </div>
    <div class="toast" id="toast" role="status" aria-live="polite"></div>
</body>
</html>
