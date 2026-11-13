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
            <a class="brand" href="/" aria-label="Axora home">
                <span class="brand-mark" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none"><path d="M4.5 7.25 12 3l7.5 4.25v9.5L12 21l-7.5-4.25v-9.5Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/><path d="m4.8 7.4 7.2 4.1 7.2-4.1M12 11.6V21" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/></svg>
                </span>
                <span class="brand-copy"><strong>Ax<span>ora</span></strong><small>CONTROL CENTER</small></span>
            </a>
            <nav class="main-nav" aria-label="Main navigation">
                @if (auth()->user()->role?->slug === 'admin')
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
                    <a class="nav-link {{ $section === 'assignments' ? 'active' : '' }}" href="{{ route('operations.assignments') }}">
                        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M8 7h12m0 0l-4-4m4 4l-4 4m-8 6H4m0 0l4 4m-4-4l4-4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        <span>Assignments</span>
                    </a>
                    <a class="nav-link {{ $section === 'returns' ? 'active' : '' }}" href="{{ route('operations.returns') }}">
                        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M12 8v4l2.5 2.5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/><circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.7"/></svg>
                        <span>Returns</span>
                    </a>
                    <a class="nav-link {{ $section === 'transfers' ? 'active' : '' }}" href="{{ route('operations.transfers') }}">
                        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M8 7h12m0 0l-4-4m4 4l-4 4m-8 6H4m0 0l4 4m-4-4l4-4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        <span>Transfers</span>
                    </a>
                    <a class="nav-link {{ $section === 'status' ? 'active' : '' }}" href="{{ route('operations.status') }}">
                        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.7"/><path d="M12 8v4l2 2" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg>
                        <span>Status &amp; condition</span>
                    </a>
                    <a class="nav-link {{ $section === 'requests' ? 'active' : '' }}" href="{{ route('operations.requests') }}">
                        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        <span>Request approvals</span>
                    </a>
                    <a class="nav-link {{ $section === 'reports' ? 'active' : '' }}" href="{{ route('operations.reports') }}">
                        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        <span>Reports</span>
                    </a>
                    <a class="nav-link {{ $section === 'history' ? 'active' : '' }}" href="{{ route('operations.history') }}">
                        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M12 8v4l2.5 2.5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/><circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.7"/></svg>
                        <span>Asset history</span>
                    </a>
                    <a class="nav-link {{ $section === 'purchases' ? 'active' : '' }}" href="{{ route('operations.purchases') }}">
                        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        <span>Purchases</span>
                    </a>
                    <a class="nav-link {{ $section === 'warranties' ? 'active' : '' }}" href="{{ route('operations.warranties') }}">
                        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        <span>Warranties</span>
                    </a>
                    <a class="nav-link {{ $section === 'verification' ? 'active' : '' }}" href="{{ route('operations.verification') }}">
                        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        <span>Verification</span>
                    </a>
                    <a class="nav-link {{ $section === 'disposals' ? 'active' : '' }}" href="{{ route('operations.disposals') }}">
                        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        <span>Disposals</span>
                    </a>
                    <a class="nav-link {{ $section === 'maintenance' ? 'active' : '' }}" href="{{ route('operations.maintenance') }}">
                        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        <span>Maintenance</span>
                    </a>
                    <a class="nav-link {{ $section === 'people' ? 'active' : '' }}" href="{{ route('operations.people') }}">
                        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 1 1-4 0 2 2 0 014 0z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        <span>Employee assets</span>
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
                        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 1 1-4 0 2 2 0 014 0zM7 10a2 2 0 1 1-4 0 2 2 0 014 0z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        <span>Departments</span>
                    </a>
                    <a class="nav-link" href="{{ route('admin.users') }}">
                        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle cx="9" cy="8" r="3.2" stroke="currentColor" stroke-width="1.7"/><path d="M3.5 20v-1.6a5.5 5.5 0 0 1 11 0V20M16 5.2a3.2 3.2 0 0 1 0 6.2m1.3 3.7a5.3 5.3 0 0 1 3.2 4.9V20" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg>
                        <span>User roles</span>
                    </a>
                    <a class="nav-link" href="{{ route('admin.maintenance') }}">
                        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M14.2 6.1a5 5 0 0 0-6.3 6.3L3.7 16.6a2.1 2.1 0 0 0 3 3l4.2-4.2a5 5 0 0 0 6.3-6.3l-3 3-3.1-3.1 3.1-2.9Z" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        <span>Maintenance</span>
                    </a>
                    <a class="nav-link" href="{{ route('admin.settings') }}">
                        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="1.7"/><path d="m19.4 15 .1.1a1.8 1.8 0 1 1-2.5 2.5l-.1-.1a1.8 1.8 0 0 0-3 .9v.2a1.8 1.8 0 1 1-3.6 0v-.2a1.8 1.8 0 0 0-3-.9l-.1.1a1.8 1.8 0 1 1-2.5-2.5l.1-.1a1.8 1.8 0 0 0-.9-3h-.2a1.8 1.8 0 1 1 0-3.6h.2a1.8 1.8 0 0 0 .9-3l-.1-.1a1.8 1.8 0 1 1 2.5-2.5l.1.1a1.8 1.8 0 0 0 3-.9v-.2a1.8 1.8 0 1 1 3.6 0v.2a1.8 1.8 0 0 0 3 .9l.1-.1a1.8 1.8 0 1 1 2.5 2.5l-.1.1a1.8 1.8 0 0 0 .9 3h.2a1.8 1.8 0 1 1 0 3.6h-.2a1.8 1.8 0 0 0-.9 3Z" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        <span>Settings</span>
                    </a>
                @elseif (auth()->user()->role?->slug === 'employee')
                    <div class="sidebar-section-label">WORKSPACE</div>
                    <a class="nav-link {{ $section === 'my-requests' ? '' : 'active' }}" href="{{ route('employee.dashboard') }}">
                        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><rect x="3.5" y="3.5" width="7" height="7" rx="1.5" stroke="currentColor" stroke-width="1.7"/><rect x="13.5" y="3.5" width="7" height="7" rx="1.5" stroke="currentColor" stroke-width="1.7"/><rect x="3.5" y="13.5" width="7" height="7" rx="1.5" stroke="currentColor" stroke-width="1.7"/><rect x="13.5" y="13.5" width="7" height="7" rx="1.5" stroke="currentColor" stroke-width="1.7"/></svg>
                        <span>Dashboard</span>
                    </a>
                    <a class="nav-link {{ $section === 'my-requests' ? 'active' : '' }}" href="{{ route('employee.requests') }}">
                        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        <span>Asset requests</span>
                    </a>
                @elseif (auth()->user()->role?->slug === 'hr_admin_staff')
                    <div class="sidebar-section-label">WORKSPACE</div>
                    <a class="nav-link {{ $section === 'people' ? 'active' : '' }}" href="{{ route('operations.people') }}">
                        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 1 1-4 0 2 2 0 014 0z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        <span>Employee assets</span>
                    </a>
                @elseif (auth()->user()->role?->slug === 'management_viewer')
                    <div class="sidebar-section-label">WORKSPACE</div>
                    <a class="nav-link {{ $section === 'reports' ? 'active' : '' }}" href="{{ route('operations.reports') }}">
                        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        <span>Reports</span>
                    </a>
                @elseif (auth()->user()->role?->slug === 'it_technician')
                    <div class="sidebar-section-label">WORKSPACE</div>
                    <a class="nav-link {{ $section === 'maintenance' ? 'active' : '' }}" href="{{ route('operations.maintenance') }}">
                        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        <span>Maintenance</span>
                    </a>
                    <a class="nav-link {{ $section === 'status' ? 'active' : '' }}" href="{{ route('operations.status') }}">
                        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.7"/><path d="M12 8v4l2 2" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg>
                        <span>Status &amp; condition</span>
                    </a>
                    <a class="nav-link {{ $section === 'history' ? 'active' : '' }}" href="{{ route('operations.history') }}">
                        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M12 8v4l2.5 2.5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/><circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.7"/></svg>
                        <span>Asset history</span>
                    </a>
                @elseif (auth()->user()->role?->slug === 'department_manager')
                    <div class="sidebar-section-label">WORKSPACE</div>
                    <a class="nav-link" href="{{ route('manager.dashboard') }}">
                        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><rect x="3.5" y="3.5" width="7" height="7" rx="1.5" stroke="currentColor" stroke-width="1.7"/><rect x="13.5" y="3.5" width="7" height="7" rx="1.5" stroke="currentColor" stroke-width="1.7"/><rect x="3.5" y="13.5" width="7" height="7" rx="1.5" stroke="currentColor" stroke-width="1.7"/><rect x="13.5" y="13.5" width="7" height="7" rx="1.5" stroke="currentColor" stroke-width="1.7"/></svg>
                        <span>Dashboard</span>
                    </a>
                    <div class="sidebar-section-label sidebar-section-spaced">ASSET OPERATIONS</div>
                    <a class="nav-link {{ $section === 'assignments' ? 'active' : '' }}" href="{{ route('operations.assignments') }}">
                        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M8 7h12m0 0l-4-4m4 4l-4 4m-8 6H4m0 0l4 4m-4-4l4-4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        <span>Assignments</span>
                    </a>
                    <a class="nav-link {{ $section === 'returns' ? 'active' : '' }}" href="{{ route('operations.returns') }}">
                        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M12 8v4l2.5 2.5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/><circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.7"/></svg>
                        <span>Returns</span>
                    </a>
                    <a class="nav-link {{ $section === 'transfers' ? 'active' : '' }}" href="{{ route('operations.transfers') }}">
                        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M8 7h12m0 0l-4-4m4 4l-4 4m-8 6H4m0 0l4 4m-4-4l4-4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        <span>Transfers</span>
                    </a>
                    <a class="nav-link {{ $section === 'status' ? 'active' : '' }}" href="{{ route('operations.status') }}">
                        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.7"/><path d="M12 8v4l2 2" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg>
                        <span>Status</span>
                    </a>
                    <a class="nav-link {{ $section === 'requests' ? 'active' : '' }}" href="{{ route('operations.requests') }}">
                        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        <span>Requests</span>
                    </a>
                    <a class="nav-link {{ $section === 'reports' ? 'active' : '' }}" href="{{ route('operations.reports') }}">
                        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        <span>Reports</span>
                    </a>
                    <a class="nav-link {{ $section === 'history' ? 'active' : '' }}" href="{{ route('operations.history') }}">
                        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M12 8v4l2.5 2.5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/><circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.7"/></svg>
                        <span>History</span>
                    </a>
                @else
                    <div class="sidebar-section-label">WORKSPACE</div>
                    <a class="nav-link" href="{{ route('manager.dashboard') }}">
                        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><rect x="3.5" y="3.5" width="7" height="7" rx="1.5" stroke="currentColor" stroke-width="1.7"/><rect x="13.5" y="3.5" width="7" height="7" rx="1.5" stroke="currentColor" stroke-width="1.7"/><rect x="3.5" y="13.5" width="7" height="7" rx="1.5" stroke="currentColor" stroke-width="1.7"/><rect x="13.5" y="13.5" width="7" height="7" rx="1.5" stroke="currentColor" stroke-width="1.7"/></svg>
                        <span>Dashboard</span>
                    </a>
                    <div class="sidebar-section-label sidebar-section-spaced">ASSET OPERATIONS</div>
                    <a class="nav-link {{ $section === 'assignments' ? 'active' : '' }}" href="{{ route('operations.assignments') }}">
                        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M8 7h12m0 0l-4-4m4 4l-4 4m-8 6H4m0 0l4 4m-4-4l4-4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        <span>Assignments</span>
                    </a>
                    <a class="nav-link {{ $section === 'returns' ? 'active' : '' }}" href="{{ route('operations.returns') }}">
                        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M12 8v4l2.5 2.5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/><circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.7"/></svg>
                        <span>Returns</span>
                    </a>
                    <a class="nav-link {{ $section === 'transfers' ? 'active' : '' }}" href="{{ route('operations.transfers') }}">
                        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M8 7h12m0 0l-4-4m4 4l-4 4m-8 6H4m0 0l4 4m-4-4l4-4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        <span>Transfers</span>
                    </a>
                    <a class="nav-link {{ $section === 'status' ? 'active' : '' }}" href="{{ route('operations.status') }}">
                        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.7"/><path d="M12 8v4l2 2" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg>
                        <span>Status &amp; condition</span>
                    </a>
                    <a class="nav-link {{ $section === 'requests' ? 'active' : '' }}" href="{{ route('operations.requests') }}">
                        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        <span>Request approvals</span>
                    </a>
                    <a class="nav-link {{ $section === 'purchases' ? 'active' : '' }}" href="{{ route('operations.purchases') }}">
                        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        <span>Purchases</span>
                    </a>
                    <a class="nav-link {{ $section === 'warranties' ? 'active' : '' }}" href="{{ route('operations.warranties') }}">
                        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        <span>Warranties</span>
                    </a>
                    <a class="nav-link {{ $section === 'verification' ? 'active' : '' }}" href="{{ route('operations.verification') }}">
                        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        <span>Verification</span>
                    </a>
                    <a class="nav-link {{ $section === 'disposals' ? 'active' : '' }}" href="{{ route('operations.disposals') }}">
                        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        <span>Disposals</span>
                    </a>
                    <a class="nav-link {{ $section === 'maintenance' ? 'active' : '' }}" href="{{ route('operations.maintenance') }}">
                        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        <span>Maintenance</span>
                    </a>
                    <a class="nav-link {{ $section === 'reports' ? 'active' : '' }}" href="{{ route('operations.reports') }}">
                        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        <span>Reports</span>
                    </a>
                    <a class="nav-link {{ $section === 'history' ? 'active' : '' }}" href="{{ route('operations.history') }}">
                        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M12 8v4l2.5 2.5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/><circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.7"/></svg>
                        <span>Asset history</span>
                    </a>
                @endif
            </nav>
            <div class="sidebar-bottom">
                <div class="sidebar-user">
                    <span class="user-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                    <div class="user-details">
                        <strong>{{ auth()->user()->name }}</strong>
                        <small>{{ auth()->user()->role?->name }}</small>
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
                        <span>Asset operations</span>
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
                        @include('components.theme-toggle')
                        <div class="topbar-divider"></div>
                        <div class="user-profile-trigger">
                            <div class="user-info">
                                <span class="user-name">{{ auth()->user()->name }}</span>
                                <span class="user-role">{{ auth()->user()->role?->name }}</span>
                            </div>
                            <span class="user-avatar topbar-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                        </div>
                    </div>
                </div>
            </header>
            <div class="page-content management-page">
                <section class="page-heading">
                    <div>
                        <p class="eyebrow">ASSET LIFECYCLE</p>
                        <h1>{{ $pageTitle }}</h1>
                        <p class="page-subtitle">Record and review changes using the shared asset register.</p>
                    </div>
                    <a
                        class="button button-secondary"
                        href="{{ route(auth()->user()->dashboardRoute() ?? 'login') }}"
                        onclick="if (document.referrer && new URL(document.referrer).origin === window.location.origin) { window.history.back(); return false; }"
                    >← Back</a>
                </section>

                @if (session('status'))<div class="management-alert" role="status">{{ session('status') }}</div>@endif
                @if ($errors->any())
                    <div class="management-alert management-alert-error" role="alert">
                        <strong>Please correct the following:</strong>
                        <ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                    </div>
                @endif

                @if ($section === 'assignments')
                    <section class="management-section">
                        <div class="management-section-heading"><div><h2>Assign an available asset</h2><p>Only assets marked Available can be assigned.</p></div></div>
                        <form class="management-create-form" method="POST" action="{{ route('operations.assignments.store') }}">
                            @csrf
                            <label class="form-field">Asset
                                <select name="asset_id" required><option value="">Select an available asset</option>
                                    @foreach ($availableAssets as $asset)<option value="{{ $asset->id }}" @selected(old('asset_id') == $asset->id)>{{ $asset->asset_code }} — {{ $asset->name }}</option>@endforeach
                                </select>
                            </label>
                            <label class="form-field">Employee
                                <select name="employee_id" required><option value="">Select an employee</option>
                                    @foreach ($employees as $employee)<option value="{{ $employee->id }}" @selected(old('employee_id') == $employee->id)>{{ $employee->first_name }} {{ $employee->last_name }}{{ $employee->department ? ' — '.$employee->department->name : '' }}</option>@endforeach
                                </select>
                            </label>
                            @if (auth()->user()->role?->slug === 'department_manager')
                                <input type="hidden" name="department_id" value="{{ $departments->first()?->id }}">
                                <p class="form-field">Department: {{ $departments->first()?->name }}</p>
                            @else
                                <label class="form-field">Department
                                    <select name="department_id"><option value="">Use employee department</option>
                                        @foreach ($departments as $department)<option value="{{ $department->id }}" @selected(old('department_id') == $department->id)>{{ $department->name }}</option>@endforeach
                                    </select>
                                </label>
                            @endif
                            <label class="form-field">Assignment date<input type="datetime-local" name="assigned_at" value="{{ old('assigned_at', now()->format('Y-m-d\TH:i')) }}" required></label>
                            <label class="form-field">Remarks<input name="remarks" value="{{ old('remarks') }}" maxlength="5000"></label>
                            <button class="button button-primary" type="submit">Assign asset</button>
                        </form>
                        <div class="management-table-wrap"><table class="management-table">
                            <thead><tr><th>Asset</th><th>Employee</th><th>Department</th><th>Assigned by</th><th>Assigned</th><th>Returned</th><th>Actions</th></tr></thead>
                            <tbody>@forelse ($assignments as $assignment)
                                <tr><td>{{ $assignment->asset?->asset_code }} · {{ $assignment->asset?->name }}</td><td>{{ $assignment->employee?->first_name }} {{ $assignment->employee?->last_name }}</td><td>{{ $assignment->department?->name ?? '—' }}</td><td>{{ $assignment->assignedBy?->name ?? '—' }}</td><td>{{ $assignment->assigned_at?->format('M j, Y') }}</td><td>{{ $assignment->closed_at?->format('M j, Y') ?? 'Active' }}</td>
                                    <td><details><summary>Update</summary><form method="POST" action="{{ route('operations.records.update', ['type' => 'assignment', 'record' => $assignment->id]) }}" class="management-create-form">@csrf @method('PUT')
                                        <label class="form-field">Assigned date<input type="datetime-local" name="assigned_at" value="{{ $assignment->assigned_at?->format('Y-m-d\TH:i') }}" required></label>
                                        <label class="form-field">Remarks<input name="remarks" value="{{ $assignment->remarks }}" maxlength="5000"></label>
                                        <button class="button button-secondary" type="submit">Save update</button></form></details>
                                        @unless ($assignment->assetReturn)<form method="POST" action="{{ route('operations.records.destroy', ['type' => 'assignment', 'record' => $assignment->id]) }}" onsubmit="return confirm('Permanently delete this assignment?')">@csrf @method('DELETE')<button class="button button-secondary" type="submit">Delete</button></form>@endunless</td></tr>
                            @empty<tr><td colspan="7">No assignments recorded yet.</td></tr>@endforelse</tbody>
                        </table></div>
                        {{ $assignments->links() }}
                    </section>
                @endif

                @if (in_array($section, ['assignments', 'returns'], true))
                    <section class="management-section">
                        <div class="management-section-heading"><div><h2>Record an asset return</h2><p>Close an active assignment and set the asset’s next status.</p></div></div>
                        <div class="management-table-wrap"><table class="management-table">
                            <thead><tr><th>Asset / holder</th><th>Assigned</th><th>Return date</th><th>Condition</th><th>Next status</th><th>Remarks</th><th>Action</th></tr></thead>
                            <tbody>@forelse ($openAssignments as $assignment)
                                <tr>
                                    <td>{{ $assignment->asset?->asset_code }} · {{ $assignment->asset?->name }}<br>{{ $assignment->employee?->first_name }} {{ $assignment->employee?->last_name }}</td>
                                    <td>{{ $assignment->assigned_at?->format('M j, Y') }}</td>
                                    <td colspan="5"><form class="management-create-form" method="POST" action="{{ route('operations.returns.store', $assignment) }}">
                                        @csrf
                                        <label class="form-field">Returned<input type="datetime-local" name="returned_at" value="{{ now()->format('Y-m-d\TH:i') }}" required></label>
                                        <label class="form-field">Condition<select name="returned_condition" required>@foreach (['good', 'fair', 'poor'] as $condition)<option value="{{ $condition }}">{{ ucfirst($condition) }}</option>@endforeach</select></label>
                                        <label class="form-field">Next status<select name="next_status" required>@foreach (['available', 'under_maintenance', 'damaged'] as $status)<option value="{{ $status }}">{{ ucfirst(str_replace('_', ' ', $status)) }}</option>@endforeach</select></label>
                                        <label class="form-field">Remarks<input name="remarks" maxlength="5000"></label>
                                        <button class="button button-primary" type="submit">Record return</button>
                                    </form></td>
                                </tr>
                            @empty<tr><td colspan="7">There are no active assignments to return.</td></tr>@endforelse</tbody>
                        </table></div>
                        <h2>Recent returns</h2>
                        <div class="management-table-wrap"><table class="management-table">
                            <thead><tr><th>Asset</th><th>Previous holder</th><th>Received by</th><th>Returned</th><th>Condition</th><th>Next status</th><th>Actions</th></tr></thead>
                            <tbody>@forelse ($returns as $return)
                                <tr><td>{{ $return->asset?->asset_code }} · {{ $return->asset?->name }}</td><td>{{ $return->assetAssignment?->employee?->first_name }} {{ $return->assetAssignment?->employee?->last_name }}</td><td>{{ $return->receivedBy?->name ?? '—' }}</td><td>{{ $return->returned_at?->format('M j, Y') }}</td><td>{{ ucfirst($return->returned_condition) }}</td><td>{{ ucfirst(str_replace('_', ' ', $return->next_status)) }}</td>
                                    <td><details><summary>Update</summary><form method="POST" action="{{ route('operations.records.update', ['type' => 'return', 'record' => $return->id]) }}" class="management-create-form">@csrf @method('PUT')
                                        <label class="form-field">Returned<input type="datetime-local" name="returned_at" value="{{ $return->returned_at?->format('Y-m-d\TH:i') }}" required></label>
                                        <label class="form-field">Condition<select name="returned_condition">@foreach (['good', 'fair', 'poor'] as $condition)<option value="{{ $condition }}" @selected($return->returned_condition === $condition)>{{ ucfirst($condition) }}</option>@endforeach</select></label>
                                        <label class="form-field">Next status<select name="next_status">@foreach (['available', 'under_maintenance', 'damaged'] as $status)<option value="{{ $status }}" @selected($return->next_status === $status)>{{ ucfirst(str_replace('_', ' ', $status)) }}</option>@endforeach</select></label>
                                        <label class="form-field">Remarks<input name="remarks" value="{{ $return->remarks }}" maxlength="5000"></label>
                                        <button class="button button-secondary" type="submit">Save update</button></form></details>
                                        <form method="POST" action="{{ route('operations.records.destroy', ['type' => 'return', 'record' => $return->id]) }}" onsubmit="return confirm('Permanently delete this return?')">@csrf @method('DELETE')<button class="button button-secondary" type="submit">Delete</button></form></td></tr>
                            @empty<tr><td colspan="7">No asset returns have been recorded.</td></tr>@endforelse</tbody>
                        </table></div>
                        {{ $returns->links() }}
                    </section>
                @endif

                @if ($section === 'transfers')
                    <section class="management-section">
                        <div class="management-section-heading"><div><h2>Transfer an asset</h2><p>Choose a new employee, location, or both. Previous holder and location are saved automatically.</p></div></div>
                        <form class="management-create-form" method="POST" action="{{ route('operations.transfers.store') }}">
                            @csrf
                            <label class="form-field">Asset<select name="asset_id" required><option value="">Select asset</option>@foreach ($assets as $asset)<option value="{{ $asset->id }}">{{ $asset->asset_code }} — {{ $asset->name }}</option>@endforeach</select></label>
                            <label class="form-field">New holder<select name="to_employee_id"><option value="">No holder change</option>@foreach ($employees as $employee)<option value="{{ $employee->id }}">{{ $employee->first_name }} {{ $employee->last_name }}</option>@endforeach</select></label>
                            <label class="form-field">New location<select name="to_location_id"><option value="">Keep current location</option>@foreach ($locations as $location)<option value="{{ $location->id }}">{{ $location->name }}</option>@endforeach</select></label>
                            <label class="form-field">Transfer date<input type="datetime-local" name="transferred_at" value="{{ now()->format('Y-m-d\TH:i') }}" required></label>
                            <label class="form-field">Reason<input name="reason" maxlength="5000" required></label>
                            <button class="button button-primary" type="submit">Record transfer</button>
                        </form>
                        <div class="management-table-wrap"><table class="management-table">
                            <thead><tr><th>Asset</th><th>Previous holder / location</th><th>New holder / location</th><th>Authorized by</th><th>Date</th><th>Reason</th><th>Actions</th></tr></thead>
                            <tbody>@forelse ($transfers as $transfer)
                                <tr><td>{{ $transfer->asset?->asset_code }} · {{ $transfer->asset?->name }}</td><td>{{ $transfer->fromEmployee?->first_name }} {{ $transfer->fromEmployee?->last_name }}<br>{{ $transfer->fromLocation?->name ?? '—' }}</td><td>{{ $transfer->toEmployee?->first_name }} {{ $transfer->toEmployee?->last_name }}<br>{{ $transfer->toLocation?->name ?? '—' }}</td><td>{{ $transfer->authorizedBy?->name ?? '—' }}</td><td>{{ $transfer->transferred_at?->format('M j, Y') }}</td><td>{{ $transfer->reason }}</td>
                                    <td><details><summary>Update</summary><form method="POST" action="{{ route('operations.records.update', ['type' => 'transfer', 'record' => $transfer->id]) }}" class="management-create-form">@csrf @method('PUT')
                                        <label class="form-field">Transfer date<input type="datetime-local" name="transferred_at" value="{{ $transfer->transferred_at?->format('Y-m-d\TH:i') }}" required></label>
                                        <label class="form-field">Reason<input name="reason" value="{{ $transfer->reason }}" maxlength="5000" required></label>
                                        <button class="button button-secondary" type="submit">Save update</button></form></details>
                                        <form method="POST" action="{{ route('operations.records.destroy', ['type' => 'transfer', 'record' => $transfer->id]) }}" onsubmit="return confirm('Permanently delete this transfer record?')">@csrf @method('DELETE')<button class="button button-secondary" type="submit">Delete</button></form></td></tr>
                            @empty<tr><td colspan="7">No asset transfers have been recorded.</td></tr>@endforelse</tbody>
                        </table></div>
                        {{ $transfers->links() }}
                    </section>
                @endif

                @if ($section === 'status')
                    <section class="management-section">
                        <div class="management-section-heading"><div><h2>Current asset register</h2><p>Statuses: Available, Assigned, Under Maintenance, Damaged, Disposed. Conditions: Good, Fair, Poor.</p></div></div>
                        <div class="management-table-wrap"><table class="management-table">
                            <thead><tr><th>Asset</th><th>Category / location</th><th>Holder</th><th>Status</th><th>Condition</th><th>Save</th></tr></thead>
                            <tbody>@foreach ($assets as $asset)
                                <tr>
                                    <td>{{ $asset->asset_code }} · {{ $asset->name }}</td><td>{{ $asset->category?->name }}<br>{{ $asset->location?->name ?? '—' }}</td>
                                    <td>{{ $asset->currentEmployee?->first_name }} {{ $asset->currentEmployee?->last_name }}</td>
                                    <td colspan="3">
                                        @if (in_array(auth()->user()->role?->slug, ['admin', 'manager', 'asset_controller', 'it_technician'], true))
                                            <form class="management-create-form" method="POST" action="{{ route('operations.status.update', $asset) }}">@csrf @method('PUT')
                                                <label class="form-field">Status<select name="status" required>@foreach (['available', 'assigned', 'under_maintenance', 'damaged', 'disposed'] as $status)<option value="{{ $status }}" @selected($asset->status === $status)>{{ ucfirst(str_replace('_', ' ', $status)) }}</option>@endforeach</select></label>
                                                <label class="form-field">Condition<select name="condition" required>@foreach (['good', 'fair', 'poor'] as $condition)<option value="{{ $condition }}" @selected($asset->condition === $condition)>{{ ucfirst($condition) }}</option>@endforeach</select></label>
                                                <label class="form-field">Reason<input name="remarks" maxlength="5000"></label>
                                                <button class="button button-secondary" type="submit">Save status</button>
                                            </form>
                                        @else
                                            <strong>{{ ucfirst(str_replace('_', ' ', $asset->status)) }}</strong> · {{ ucfirst($asset->condition) }}
                                        @endif
                                    </td>
                                </tr>
                            @endforeach</tbody>
                        </table></div>
                        {{ $assets->links() }}
                    </section>
                @endif

                @if ($section === 'requests')
                    <section class="management-section"><div class="management-section-heading"><div><h2>Employee asset requests</h2><p>Approve or reject pending requests. Optionally select an available asset to approve and assign it immediately.</p></div></div>
                        <div class="management-table-wrap"><table class="management-table">
                            <thead><tr><th>Requester</th><th>Request</th><th>Department</th><th>Status</th><th>Review</th><th>Actions</th></tr></thead>
                            <tbody>@forelse ($requests as $assetRequest)
                                <tr><td>{{ $assetRequest->requester?->name }}<br>{{ $assetRequest->requester?->email }}</td>
                                    <td>{{ $assetRequest->category?->name ?? 'Any category' }}<br>{{ $assetRequest->business_reason }}</td>
                                    <td>{{ $assetRequest->department?->name ?? '—' }}</td>
                                    <td>{{ ucfirst($assetRequest->status) }}<br>{{ $assetRequest->fulfilledAsset?->asset_code }}</td>
                                    <td>
                                        @if ($assetRequest->status === 'pending')
                                            <form method="POST" action="{{ route('operations.requests.review', $assetRequest) }}" class="management-create-form">@csrf @method('PUT')
                                                <label class="form-field">Decision<select name="decision" required><option value="approve">Approve</option><option value="reject">Reject</option></select></label>
                                                <label class="form-field">Assign available asset (optional)<select name="asset_id"><option value="">Approve without assignment</option>@foreach ($availableAssets as $asset)<option value="{{ $asset->id }}" @disabled($assetRequest->category_id && $asset->category_id !== $assetRequest->category_id)>{{ $asset->asset_code }} — {{ $asset->name }}</option>@endforeach</select></label>
                                                <label class="form-field">Reviewer remarks<input name="reviewer_remarks" maxlength="5000"></label>
                                                <button class="button button-primary" type="submit">Save decision</button>
                                            </form>
                                        @elseif ($assetRequest->status === 'approved')
                                            <form method="POST" action="{{ route('operations.requests.fulfill', $assetRequest) }}" class="management-create-form">@csrf
                                                <label class="form-field">Assign available asset<select name="asset_id" required><option value="">Select asset</option>@foreach ($availableAssets as $asset)<option value="{{ $asset->id }}" @disabled($assetRequest->category_id && $asset->category_id !== $assetRequest->category_id)>{{ $asset->asset_code }} — {{ $asset->name }}</option>@endforeach</select></label>
                                                <button class="button button-primary" type="submit">Fulfill request</button>
                                            </form>
                                            <p>{{ $assetRequest->reviewer_remarks }}</p>
                                        @else
                                            <span>{{ $assetRequest->reviewedBy?->name ?? 'Reviewed' }} · {{ $assetRequest->reviewed_at?->format('M j, Y') }}</span>
                                            <p>{{ $assetRequest->reviewer_remarks }}</p>
                                        @endif
                                    </td><td><form method="POST" action="{{ route('operations.records.destroy', ['type' => 'request', 'record' => $assetRequest->id]) }}" onsubmit="return confirm('Permanently delete this asset request?')">@csrf @method('DELETE')<button class="button button-secondary" type="submit">Delete</button></form></td>
                                </tr>
                            @empty<tr><td colspan="6">No asset requests have been submitted.</td></tr>@endforelse</tbody>
                        </table></div>
                        {{ $requests->links() }}
                    </section>
                @endif

                @if ($section === 'my-requests')
                    <section class="management-section">
                        <div class="management-section-heading"><div><h2>Submit an asset request</h2><p>Explain the business reason. Your request will be sent to a manager for review.</p></div></div>
                        <form class="management-create-form" method="POST" action="{{ route('employee.requests.store') }}">@csrf
                            <label class="form-field">Category<select name="category_id"><option value="">No preference</option>@foreach ($categories as $category)<option value="{{ $category->id }}" @selected(old('category_id') == $category->id)>{{ $category->name }}</option>@endforeach</select></label>
                            <label class="form-field">Department<select name="department_id"><option value="">Not specified</option>@foreach ($departments as $department)<option value="{{ $department->id }}" @selected(old('department_id') == $department->id)>{{ $department->name }}</option>@endforeach</select></label>
                            <label class="form-field">Business reason<input name="business_reason" value="{{ old('business_reason') }}" minlength="10" maxlength="5000" required placeholder="Explain what equipment is needed and why"></label>
                            <button class="button button-primary" type="submit">Submit request</button>
                        </form>
                        <h2>My request history</h2>
                        <div class="management-table-wrap"><table class="management-table">
                            <thead><tr><th>Category</th><th>Department</th><th>Business reason</th><th>Status</th><th>Reviewed by</th><th>Asset / remarks</th><th>Submitted</th><th>Actions</th></tr></thead>
                            <tbody>@forelse ($requests as $assetRequest)
                                <tr><td>{{ $assetRequest->category?->name ?? 'Any category' }}</td><td>{{ $assetRequest->department?->name ?? '—' }}</td><td>{{ $assetRequest->business_reason }}</td><td>{{ ucfirst($assetRequest->status) }}</td><td>{{ $assetRequest->reviewedBy?->name ?? '—' }}</td><td>{{ $assetRequest->fulfilledAsset?->asset_code }}<br>{{ $assetRequest->reviewer_remarks }}</td><td>{{ $assetRequest->created_at?->format('M j, Y') }}</td>
                                    <td>@if ($assetRequest->status === 'pending')
                                        <details><summary>Update</summary><form method="POST" action="{{ route('employee.requests.update', $assetRequest) }}" class="management-create-form">@csrf @method('PUT')
                                            <label class="form-field">Business reason<input name="business_reason" value="{{ $assetRequest->business_reason }}" minlength="10" maxlength="5000" required></label>
                                            <label class="form-field">Category<select name="category_id"><option value="">No preference</option>@foreach ($categories as $category)<option value="{{ $category->id }}" @selected($assetRequest->category_id === $category->id)>{{ $category->name }}</option>@endforeach</select></label>
                                            <label class="form-field">Department<select name="department_id"><option value="">Not specified</option>@foreach ($departments as $department)<option value="{{ $department->id }}" @selected($assetRequest->department_id === $department->id)>{{ $department->name }}</option>@endforeach</select></label>
                                            <button class="button button-secondary" type="submit">Save update</button></form></details>
                                        <form method="POST" action="{{ route('employee.requests.destroy', $assetRequest) }}" onsubmit="return confirm('Permanently delete this request?')">@csrf @method('DELETE')<button class="button button-secondary" type="submit">Delete</button></form>
                                    @endif</td></tr>
                            @empty<tr><td colspan="8">You have not requested an asset yet.</td></tr>@endforelse</tbody>
                        </table></div>
                        {{ $requests->links() }}
                    </section>
                @endif

                @if ($section === 'history')
                    <section class="management-section"><div class="management-section-heading"><div><h2>Asset activity history</h2><p>Registration, assignment, return, transfer, maintenance, and status events with responsible users.</p></div></div>
                        <form method="GET" action="{{ route('operations.history') }}" class="management-create-form">
                            <label class="form-field">Filter asset<select name="asset_id"><option value="">All assets</option>@foreach ($assets as $asset)<option value="{{ $asset->id }}" @selected(request('asset_id') == $asset->id)>{{ $asset->asset_code }} — {{ $asset->name }}</option>@endforeach</select></label>
                            <button class="button button-secondary" type="submit">Filter history</button>
                        </form>
                        <div class="management-table-wrap"><table class="management-table">
                            <thead><tr><th>Date</th><th>Asset</th><th>Event</th><th>Responsible user</th><th>Details</th></tr></thead>
                            <tbody>@forelse ($activities as $activity)
                                <tr><td>{{ $activity->occurred_at?->format('M j, Y H:i') }}</td><td>{{ $activity->asset?->asset_code }} · {{ $activity->asset?->name }}</td><td>{{ ucfirst(str_replace('_', ' ', $activity->action_type)) }}</td><td>{{ $activity->user?->name ?? 'System' }}</td><td>{{ $activity->description }}@if ($activity->details)<br><small>{{ json_encode($activity->details, JSON_UNESCAPED_SLASHES) }}</small>@endif</td></tr>
                            @empty<tr><td colspan="5">No history events match this filter.</td></tr>@endforelse</tbody>
                        </table></div>
                        {{ $activities->links() }}
                        <h2>Status and condition changes</h2>
                        <div class="management-table-wrap"><table class="management-table">
                            <thead><tr><th>Date</th><th>Asset</th><th>Previous status / condition</th><th>New status / condition</th><th>Responsible user</th><th>Remarks</th></tr></thead>
                            <tbody>@forelse ($statusHistories as $history)
                                <tr><td>{{ $history->changed_at?->format('M j, Y H:i') }}</td><td>{{ $history->asset?->asset_code }} · {{ $history->asset?->name }}</td><td>{{ ucfirst(str_replace('_', ' ', $history->old_status ?? '—')) }} / {{ ucfirst($history->old_condition ?? '—') }}</td><td>{{ ucfirst(str_replace('_', ' ', $history->new_status)) }} / {{ ucfirst($history->new_condition ?? '—') }}</td><td>{{ $history->changedBy?->name ?? 'System' }}</td><td>{{ $history->remarks ?? '—' }}</td></tr>
                            @empty<tr><td colspan="6">No status or condition changes found.</td></tr>@endforelse</tbody>
                        </table></div>
                        {{ $statusHistories->links() }}
                    </section>
                @endif

                @if ($section === 'reports')
                    <section class="management-section"><div class="management-section-heading"><div><h2>Asset reports</h2><p>View and download the asset register, assignment list, availability, grouped counts and movement history.</p></div></div>
                        <nav class="management-tabs" aria-label="Report types">
                            @php
                                $reportTypes = auth()->user()->role?->slug === 'department_manager'
                                    ? ['assets' => 'Asset register', 'assignments' => 'Assignments', 'available' => 'Available assets', 'movements' => 'Movement history']
                                    : ['assets' => 'Asset register', 'assignments' => 'Assignments', 'available' => 'Available assets', 'department' => 'By department', 'location' => 'By location', 'category' => 'By category', 'movements' => 'Movement history'];
                            @endphp
                            @foreach ($reportTypes as $type => $label)
                                <a href="{{ route('operations.reports', ['type' => $type]) }}" @if ($reportType === $type) aria-current="page" @endif>{{ $label }}</a>
                            @endforeach
                        </nav>
                        <p><a class="button button-secondary" href="{{ route('operations.reports', ['type' => $reportType, 'download' => 1]) }}">Download CSV</a></p>
                        <div class="management-table-wrap"><table class="management-table">
                            @if ($reportType === 'assignments')
                                <thead><tr><th>Asset</th><th>Employee</th><th>Department</th><th>Assigned by</th><th>Assigned</th><th>Returned</th></tr></thead>
                                <tbody>@forelse ($reportRows as $row)<tr><td>{{ $row->asset?->asset_code }} · {{ $row->asset?->name }}</td><td>{{ $row->employee?->first_name }} {{ $row->employee?->last_name }}</td><td>{{ $row->department?->name ?? '—' }}</td><td>{{ $row->assignedBy?->name ?? '—' }}</td><td>{{ $row->assigned_at?->format('M j, Y') }}</td><td>{{ $row->closed_at?->format('M j, Y') ?? 'Active' }}</td></tr>@empty<tr><td colspan="6">No assignment records.</td></tr>@endforelse</tbody>
                            @elseif ($reportType === 'movements')
                                <thead><tr><th>Date</th><th>Asset</th><th>Action</th><th>Description</th><th>Responsible user</th></tr></thead>
                                <tbody>@forelse ($reportRows as $row)<tr><td>{{ $row->occurred_at?->format('M j, Y H:i') }}</td><td>{{ $row->asset?->asset_code }} · {{ $row->asset?->name }}</td><td>{{ ucfirst(str_replace('_', ' ', $row->action_type)) }}</td><td>{{ $row->description }}</td><td>{{ $row->user?->name ?? 'System' }}</td></tr>@empty<tr><td colspan="5">No asset movements found.</td></tr>@endforelse</tbody>
                            @elseif (in_array($reportType, ['department', 'location', 'category'], true))
                                <thead><tr><th>{{ ucfirst($reportType) }}</th><th>{{ $reportType === 'department' ? 'Current assignments' : 'Assets' }}</th></tr></thead>
                                <tbody>@forelse ($reportRows as $row)<tr><td>{{ $row->name }}</td><td>{{ $reportType === 'department' ? $row->assigned_assets_count : $row->assets_count }}</td></tr>@empty<tr><td colspan="2">No group records found.</td></tr>@endforelse</tbody>
                            @else
                                <thead><tr><th>Asset code</th><th>Asset</th><th>Category</th><th>Location</th><th>Holder</th><th>Status</th><th>Condition</th></tr></thead>
                                <tbody>@forelse ($reportRows as $row)<tr><td>{{ $row->asset_code }}</td><td>{{ $row->name }}</td><td>{{ $row->category?->name }}</td><td>{{ $row->location?->name ?? '—' }}</td><td>{{ $row->currentEmployee?->first_name }} {{ $row->currentEmployee?->last_name }}</td><td>{{ ucfirst(str_replace('_', ' ', $row->status)) }}</td><td>{{ ucfirst($row->condition) }}</td></tr>@empty<tr><td colspan="7">No assets found.</td></tr>@endforelse</tbody>
                            @endif
                        </table></div>
                        {{ $reportRows->links() }}
                    </section>
                @endif

                @if ($section === 'purchases')
                    <section class="management-section">
                        <div class="management-section-heading"><div><h2>Record a purchase</h2><p>Track supplier, purchase order, invoice, cost, and the date equipment was received.</p></div></div>
                        <form class="management-create-form" method="POST" enctype="multipart/form-data" action="{{ route('operations.purchases.store') }}">@csrf
                            <label class="form-field">Asset<select name="asset_id" required><option value="">Select asset</option>@foreach ($assets as $asset)<option value="{{ $asset->id }}">{{ $asset->asset_code }} · {{ $asset->name }}</option>@endforeach</select></label>
                            <label class="form-field">Supplier<input name="supplier" required maxlength="255" value="{{ old('supplier') }}"></label>
                            <label class="form-field">Purchase order reference<input name="purchase_order_reference" maxlength="255" value="{{ old('purchase_order_reference') }}"></label>
                            <label class="form-field">Invoice attachment<input type="file" name="invoice" accept=".pdf,.jpg,.jpeg,.png"></label>
                            <label class="form-field">Purchase cost<input type="number" name="purchase_cost" min="0" step="0.01" required value="{{ old('purchase_cost') }}"></label>
                            <label class="form-field">Received date<input type="date" name="received_at" required value="{{ old('received_at', today()->toDateString()) }}"></label>
                            <button class="button button-primary" type="submit">Save purchase</button>
                        </form>
                        <div class="management-table-wrap"><table class="management-table">
                            <thead><tr><th>Received</th><th>Asset</th><th>Supplier</th><th>PO reference</th><th>Cost</th><th>Invoice</th><th>Recorded by</th><th>Actions</th></tr></thead>
                            <tbody>@forelse ($purchaseRecords as $purchase)
                                <tr><td>{{ $purchase->received_at?->format('M j, Y') }}</td><td>{{ $purchase->asset?->asset_code }} · {{ $purchase->asset?->name }}</td><td>{{ $purchase->supplier }}</td><td>{{ $purchase->purchase_order_reference ?? '—' }}</td><td>{{ number_format((float) $purchase->purchase_cost, 2) }}</td><td>@if ($purchase->invoice_path)<a href="{{ route('operations.attachments.download', ['type' => 'invoice', 'id' => $purchase->id]) }}">Download invoice</a>@else — @endif</td><td>{{ $purchase->recordedBy?->name ?? '—' }}</td>
                                    <td><details><summary>Update</summary><form method="POST" action="{{ route('operations.records.update', ['type' => 'purchase', 'record' => $purchase->id]) }}" class="management-create-form">@csrf @method('PUT')
                                        <label class="form-field">Supplier<input name="supplier" value="{{ $purchase->supplier }}" required maxlength="255"></label>
                                        <label class="form-field">PO reference<input name="purchase_order_reference" value="{{ $purchase->purchase_order_reference }}" maxlength="255"></label>
                                        <label class="form-field">Cost<input type="number" name="purchase_cost" min="0" step="0.01" value="{{ $purchase->purchase_cost }}" required></label>
                                        <label class="form-field">Received date<input type="date" name="received_at" value="{{ $purchase->received_at?->format('Y-m-d') }}" required></label>
                                        <button class="button button-secondary" type="submit">Save update</button></form></details>
                                        <form method="POST" action="{{ route('operations.records.destroy', ['type' => 'purchase', 'record' => $purchase->id]) }}" onsubmit="return confirm('Permanently delete this purchase record and its invoice?')">@csrf @method('DELETE')<button class="button button-secondary" type="submit">Delete</button></form></td></tr>
                            @empty<tr><td colspan="8">No purchase records have been added.</td></tr>@endforelse</tbody>
                        </table></div>{{ $purchaseRecords->links() }}
                    </section>
                @endif

                @if ($section === 'warranties')
                    <section class="management-section">
                        <div class="management-section-heading"><div><h2>Register a warranty</h2><p>Expiry reminders are generated 30, 7, and 1 day before expiry by the daily scheduler.</p></div></div>
                        <form class="management-create-form" method="POST" enctype="multipart/form-data" action="{{ route('operations.warranties.store') }}">@csrf
                            <label class="form-field">Asset<select name="asset_id" required><option value="">Select asset</option>@foreach ($assets as $asset)<option value="{{ $asset->id }}">{{ $asset->asset_code }} · {{ $asset->name }}</option>@endforeach</select></label>
                            <label class="form-field">Warranty provider<input name="provider" required maxlength="255"></label>
                            <label class="form-field">Start date<input type="date" name="starts_at" required></label>
                            <label class="form-field">Expiry date<input type="date" name="expires_at" required></label>
                            <label class="form-field">Warranty document<input type="file" name="document" accept=".pdf,.jpg,.jpeg,.png"></label>
                            <button class="button button-primary" type="submit">Save warranty</button>
                        </form>
                        <div class="management-table-wrap"><table class="management-table">
                            <thead><tr><th>Asset</th><th>Provider</th><th>Starts</th><th>Expires</th><th>Document</th><th>Claim records</th><th>Actions</th></tr></thead>
                            <tbody>@forelse ($warranties as $warranty)
                                <tr><td>{{ $warranty->asset?->asset_code }} · {{ $warranty->asset?->name }}</td><td>{{ $warranty->provider }}</td><td>{{ $warranty->starts_at?->format('M j, Y') }}</td><td>{{ $warranty->expires_at?->format('M j, Y') }} @if ($warranty->expires_at?->lt(today()))<strong>Expired</strong>@endif</td><td>@if ($warranty->document_path)<a href="{{ route('operations.attachments.download', ['type' => 'warranty', 'id' => $warranty->id]) }}">Download document</a>@else — @endif</td>
                                    <td>
                                        <form method="POST" action="{{ route('operations.warranties.claims.store', $warranty) }}" class="management-create-form">@csrf
                                            <label class="form-field">Claim reference<input name="claim_reference" maxlength="255"></label>
                                            <label class="form-field">Claim description<input name="description" required maxlength="5000"></label>
                                            <label class="form-field">Submitted date<input type="date" name="submitted_at" required value="{{ today()->toDateString() }}"></label>
                                            <button class="button button-secondary" type="submit">Add claim</button>
                                        </form>
                                        @foreach ($warranty->claims as $claim)
                                            <form method="POST" action="{{ route('operations.warranties.claims.update', $claim) }}" class="management-create-form">@csrf @method('PUT')
                                                <span>{{ $claim->claim_reference ?? 'Claim #'.$claim->id }}: {{ $claim->description }}</span>
                                                <label class="form-field">Status<select name="status">@foreach (['submitted', 'in_review', 'approved', 'rejected', 'completed'] as $status)<option value="{{ $status }}" @selected($claim->status === $status)>{{ ucfirst(str_replace('_', ' ', $status)) }}</option>@endforeach</select></label>
                                                <label class="form-field">Resolution<input name="resolution" value="{{ $claim->resolution }}" maxlength="5000"></label>
                                                <button class="button button-secondary" type="submit">Update claim</button>
                                            </form>
                                                <form method="POST" action="{{ route('operations.records.destroy', ['type' => 'claim', 'record' => $claim->id]) }}" onsubmit="return confirm('Permanently delete this warranty claim?')">@csrf @method('DELETE')<button class="button button-secondary" type="submit">Delete claim</button></form>
                                        @endforeach
                                    </td><td><details><summary>Update</summary><form method="POST" action="{{ route('operations.records.update', ['type' => 'warranty', 'record' => $warranty->id]) }}" class="management-create-form">@csrf @method('PUT')
                                        <label class="form-field">Provider<input name="provider" value="{{ $warranty->provider }}" maxlength="255" required></label>
                                        <label class="form-field">Starts<input type="date" name="starts_at" value="{{ $warranty->starts_at?->format('Y-m-d') }}" required></label>
                                        <label class="form-field">Expires<input type="date" name="expires_at" value="{{ $warranty->expires_at?->format('Y-m-d') }}" required></label>
                                        <button class="button button-secondary" type="submit">Save update</button></form></details>
                                        <form method="POST" action="{{ route('operations.records.destroy', ['type' => 'warranty', 'record' => $warranty->id]) }}" onsubmit="return confirm('Permanently delete this warranty and its claims?')">@csrf @method('DELETE')<button class="button button-secondary" type="submit">Delete</button></form></td>
                                </tr>
                            @empty<tr><td colspan="7">No warranty records have been added.</td></tr>@endforelse</tbody>
                        </table></div>{{ $warranties->links() }}
                    </section>
                @endif

                @if ($section === 'verification')
                    <section class="management-section">
                        <div class="management-section-heading"><div><h2>Record an asset check</h2><p>Record the verifier and identify missing, mismatched, or unregistered equipment.</p></div></div>
                        <form class="management-create-form" method="POST" action="{{ route('operations.verification.store') }}">@csrf
                            <label class="form-field">Finding<select name="finding" required>@foreach (['verified', 'missing', 'mismatch', 'unregistered'] as $finding)<option value="{{ $finding }}">{{ ucfirst($finding) }}</option>@endforeach</select></label>
                            <label class="form-field">Expected asset<select name="asset_id"><option value="">Not registered / not applicable</option>@foreach ($assets as $asset)<option value="{{ $asset->id }}">{{ $asset->asset_code }} · {{ $asset->name }}</option>@endforeach</select></label>
                            <label class="form-field">Observed asset code<input name="observed_asset_code" maxlength="255"></label>
                            <label class="form-field">Verified date<input type="datetime-local" name="verified_at" required value="{{ now()->format('Y-m-d\TH:i') }}"></label>
                            <label class="form-field">Notes<input name="notes" maxlength="5000"></label>
                            <button class="button button-primary" type="submit">Save verification</button>
                        </form>
                        <div class="management-table-wrap"><table class="management-table">
                            <thead><tr><th>Date</th><th>Verifier</th><th>Asset expected</th><th>Observed code</th><th>Finding</th><th>Notes</th><th>Actions</th></tr></thead>
                            <tbody>@forelse ($verificationSessions as $session)
                                @forelse ($session->items as $item)
                                    <tr><td>{{ $session->verified_at?->format('M j, Y H:i') }}</td><td>{{ $session->verifier?->name }}</td><td>{{ $item->asset?->asset_code ?? '—' }}</td><td>{{ $item->observed_asset_code ?? '—' }}</td><td>{{ ucfirst($item->finding) }}</td><td>{{ $item->notes ?? $session->notes }}</td>
                                        <td><details><summary>Update</summary><form method="POST" action="{{ route('operations.records.update', ['type' => 'verification', 'record' => $item->id]) }}" class="management-create-form">@csrf @method('PUT')
                                            <label class="form-field">Date<input type="datetime-local" name="verified_at" value="{{ $session->verified_at?->format('Y-m-d\TH:i') }}" required></label>
                                            <label class="form-field">Finding<select name="finding">@foreach (['verified', 'missing', 'mismatch', 'unregistered'] as $finding)<option value="{{ $finding }}" @selected($item->finding === $finding)>{{ ucfirst($finding) }}</option>@endforeach</select></label>
                                            <label class="form-field">Observed code<input name="observed_asset_code" value="{{ $item->observed_asset_code }}" maxlength="255"></label>
                                            <label class="form-field">Notes<input name="notes" value="{{ $item->notes }}" maxlength="5000"></label>
                                            <button class="button button-secondary" type="submit">Save update</button></form></details>
                                            <form method="POST" action="{{ route('operations.records.destroy', ['type' => 'verification', 'record' => $item->id]) }}" onsubmit="return confirm('Permanently delete this verification finding?')">@csrf @method('DELETE')<button class="button button-secondary" type="submit">Delete</button></form></td></tr>
                                @empty<tr><td colspan="7">No findings in this verification session.</td></tr>@endforelse
                            @empty<tr><td colspan="7">No asset verifications recorded.</td></tr>@endforelse</tbody>
                        </table></div>{{ $verificationSessions->links() }}
                    </section>
                @endif

                @if ($section === 'disposals')
                    <section class="management-section">
                        <div class="management-section-heading"><div><h2>Request asset disposal</h2><p>Disposal approval is required; assets with active assignments or maintenance cannot be disposed.</p></div></div>
                        <form class="management-create-form" method="POST" action="{{ route('operations.disposals.store') }}">@csrf
                            <label class="form-field">Asset<select name="asset_id" required><option value="">Select asset</option>@foreach ($assets as $asset)<option value="{{ $asset->id }}">{{ $asset->asset_code }} · {{ $asset->name }}</option>@endforeach</select></label>
                            <label class="form-field">Disposal reason<input name="reason" required minlength="10" maxlength="5000"></label>
                            <button class="button button-primary" type="submit">Request disposal</button>
                        </form>
                        <div class="management-table-wrap"><table class="management-table">
                            <thead><tr><th>Asset</th><th>Reason</th><th>Requested by</th><th>Status</th><th>Approval / disposal date</th><th>Actions</th></tr></thead>
                            <tbody>@forelse ($disposals as $disposal)
                                <tr><td>{{ $disposal->asset?->asset_code }} · {{ $disposal->asset?->name }}</td><td>{{ $disposal->reason }}</td><td>{{ $disposal->requestedBy?->name }}</td><td>{{ ucfirst($disposal->status) }}</td>
                                    <td>@if ($disposal->status === 'pending')
                                        <form method="POST" action="{{ route('operations.disposals.review', $disposal) }}" class="management-create-form">@csrf @method('PUT')
                                            <label class="form-field">Decision<select name="decision" required><option value="approve">Approve</option><option value="reject">Reject</option></select></label>
                                            <label class="form-field">Disposal date<input type="datetime-local" name="disposed_at" value="{{ now()->format('Y-m-d\TH:i') }}"></label>
                                            <label class="form-field">Review remarks<input name="reviewer_remarks" maxlength="5000"></label>
                                            <button class="button button-primary" type="submit">Review</button>
                                        </form>
                                    @else {{ $disposal->reviewedBy?->name }} · {{ $disposal->disposed_at?->format('M j, Y') ?? 'Not disposed' }}<br>{{ $disposal->reviewer_remarks }} @endif</td>
                                    <td>@if (in_array($disposal->status, ['pending', 'rejected'], true))<form method="POST" action="{{ route('operations.records.destroy', ['type' => 'disposal', 'record' => $disposal->id]) }}" onsubmit="return confirm('Permanently delete this disposal request?')">@csrf @method('DELETE')<button class="button button-secondary" type="submit">Delete</button></form>@else — @endif</td>
                                </tr>
                            @empty<tr><td colspan="6">No disposal requests recorded.</td></tr>@endforelse</tbody>
                        </table></div>{{ $disposals->links() }}
                    </section>
                @endif

                @if ($section === 'maintenance')
                    <section class="management-section">
                        <div class="management-section-heading"><div><h2>Submit a maintenance request</h2><p>Describe the problem, attach a photo, and assign a technician.</p></div></div>
                        <form class="management-create-form" method="POST" enctype="multipart/form-data" action="{{ route('operations.maintenance.store') }}">@csrf
                            <label class="form-field">Asset<select name="asset_id" required><option value="">Select asset</option>@foreach ($assets as $asset)<option value="{{ $asset->id }}">{{ $asset->asset_code }} · {{ $asset->name }}</option>@endforeach</select></label>
                            @if (auth()->user()->role?->slug === 'it_technician')
                                <input type="hidden" name="assigned_to_user_id" value="{{ auth()->id() }}">
                                <p class="form-field">Assigned technician: {{ auth()->user()->name }}</p>
                            @else
                                <label class="form-field">Technician<select name="assigned_to_user_id"><option value="">Unassigned</option>@foreach ($technicians as $technician)<option value="{{ $technician->id }}">{{ $technician->name }}</option>@endforeach</select></label>
                            @endif
                            <label class="form-field">Problem description<input name="description" required maxlength="5000"></label>
                            <label class="form-field">Photo attachment<input type="file" name="photo" accept="image/jpeg,image/png,image/webp"></label>
                            <button class="button button-primary" type="submit">Create maintenance request</button>
                        </form>
                        <div class="management-table-wrap"><table class="management-table">
                            <thead><tr><th>Reported</th><th>Asset / problem</th><th>Photo</th><th>Technician</th><th>Status</th><th>Cost</th><th>Completed</th><th>Save</th></tr></thead>
                            <tbody>@forelse ($maintenanceRecords as $record)
                                <tr><td>{{ $record->reported_at?->format('M j, Y') }}</td><td>{{ $record->asset?->asset_code }} · {{ $record->asset?->name }}<br>{{ $record->description }}</td>
                                    <td>@if ($record->attachment_path && (auth()->user()->role?->slug !== 'it_technician' || $record->assigned_to_user_id === auth()->id()))<a href="{{ route('operations.attachments.download', ['type' => 'maintenance', 'id' => $record->id]) }}">View photo</a>@else — @endif</td>
                                    <td colspan="5"><form id="ops-maintenance-{{ $record->id }}" method="POST" action="{{ route('operations.maintenance.update', $record) }}" class="management-create-form">@csrf @method('PUT')
                                        @if (auth()->user()->role?->slug === 'it_technician')
                                            <input type="hidden" name="assigned_to_user_id" value="{{ auth()->id() }}">
                                            <p class="form-field">Technician: {{ auth()->user()->name }}</p>
                                        @else
                                            <label class="form-field">Technician<select name="assigned_to_user_id"><option value="">Unassigned</option>@foreach ($technicians as $technician)<option value="{{ $technician->id }}" @selected($record->assigned_to_user_id === $technician->id)>{{ $technician->name }}</option>@endforeach</select></label>
                                        @endif
                                        <label class="form-field">Repair status<select name="status">@foreach (['requested', 'in_progress', 'completed'] as $status)<option value="{{ $status }}" @selected($record->status === $status)>{{ ucfirst(str_replace('_', ' ', $status)) }}</option>@endforeach</select></label>
                                        <label class="form-field">Cost<input type="number" min="0" step="0.01" name="cost" value="{{ $record->cost }}"></label>
                                        <label class="form-field">Completion date<input type="datetime-local" name="completed_at" value="{{ $record->completed_at?->format('Y-m-d\TH:i') ?? now()->format('Y-m-d\TH:i') }}"></label>
                                        <button class="button button-secondary" type="submit">Save update</button>
                                    </form>@if ($record->status === 'completed' && in_array(auth()->user()->role?->slug, ['admin', 'manager', 'asset_controller'], true))<form method="POST" action="{{ route('operations.records.destroy', ['type' => 'maintenance', 'record' => $record->id]) }}" onsubmit="return confirm('Permanently delete this completed maintenance record and attachment?')">@csrf @method('DELETE')<button class="button button-secondary" type="submit">Delete</button></form>@endif</td>
                                </tr>
                            @empty<tr><td colspan="8">No maintenance requests yet.</td></tr>@endforelse</tbody>
                        </table></div>{{ $maintenanceRecords->links() }}
                    </section>
                @endif

                @if ($section === 'people')
                    <section class="management-section">
                        <div class="management-section-heading"><div><h2>Employee asset register</h2><p>Read-only view of active employees and the assets currently assigned to them.</p></div></div>
                        <div class="management-table-wrap"><table class="management-table">
                            <thead><tr><th>Employee</th><th>Department</th><th>Job title</th><th>Assigned assets</th></tr></thead>
                            <tbody>@forelse ($employees as $employee)
                                <tr>
                                    <td>{{ $employee->first_name }} {{ $employee->last_name }}<br>{{ $employee->email }}</td>
                                    <td>{{ $employee->department?->name ?? '—' }}</td>
                                    <td>{{ $employee->job_title ?? '—' }}</td>
                                    <td>@forelse ($employee->assets as $asset)<span>{{ $asset->asset_code }} · {{ $asset->name }} ({{ ucfirst(str_replace('_', ' ', $asset->status)) }})</span>@if (! $loop->last)<br>@endif @empty — @endforelse</td>
                                </tr>
                            @empty<tr><td colspan="4">No active employee profiles found.</td></tr>@endforelse</tbody>
                        </table></div>
                        {{ $employees->links() }}
                    </section>
                @endif

                @if ($section === 'notifications')
                    <section class="management-section">
                        <div class="management-section-heading"><div><h2>Your notifications</h2><p>{{ $unreadNotificationCount }} unread notification(s). Assignments, request approvals, maintenance, and warranty reminders appear here.</p></div>
                            @if ($unreadNotificationCount > 0)
                                <form method="POST" action="{{ auth()->user()->role?->slug === 'employee' ? route('employee.notifications.read-all') : route('operations.notifications.read-all') }}">@csrf<button class="button button-secondary" type="submit">Mark all read</button></form>
                            @endif
                        </div>
                        <div class="management-table-wrap"><table class="management-table">
                            <thead><tr><th>Date</th><th>Notification</th><th>Status</th><th>Action</th></tr></thead>
                            <tbody>@forelse ($notifications as $notification)
                                <tr><td>{{ $notification->created_at?->format('M j, Y H:i') }}</td><td><strong>{{ $notification->data['title'] ?? 'Asset update' }}</strong><br>{{ $notification->data['message'] ?? '' }}</td>
                                    <td>{{ $notification->read_at ? 'Read' : 'Unread' }}</td><td>
                                        @if (! $notification->read_at)
                                            <form method="POST" action="{{ auth()->user()->role?->slug === 'employee' ? route('employee.notifications.read', $notification->id) : route('operations.notifications.read', $notification->id) }}">@csrf<button class="button button-secondary" type="submit">Mark read</button></form>
                                        @endif
                                        @if (auth()->user()->role?->slug === 'employee' || in_array(auth()->user()->role?->slug, ['admin', 'manager', 'asset_controller'], true))<form method="POST" action="{{ auth()->user()->role?->slug === 'employee' ? route('employee.notifications.destroy', $notification->id) : route('operations.records.destroy', ['type' => 'notification', 'record' => $notification->id]) }}" onsubmit="return confirm('Permanently delete this notification?')">@csrf @method('DELETE')<button class="button button-secondary" type="submit">Delete</button></form>@endif
                                        @if (! empty($notification->data['url']))<a href="{{ $notification->data['url'] }}">Open</a>@endif
                                    </td></tr>
                            @empty<tr><td colspan="4">You have no notifications.</td></tr>@endforelse</tbody>
                        </table></div>{{ $notifications->links() }}
                    </section>
                @endif
            </div>
        </main>
    </div>
</body>
</html>
