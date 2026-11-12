<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="theme-color" content="#111a2b">
        <title>Axora | Asset control center</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="welcome-page">
        <div class="welcome-shell">
            <header class="welcome-header">
                <div class="welcome-brand" aria-label="Axora home">
                    <span class="welcome-brand-mark" aria-hidden="true">?</span>
                    <div>
                        <div style="font-size: 28px; letter-spacing: -.7px;">Axora</div>
                        <small>Control center</small>
                    </div>
                </div>

                <nav class="welcome-nav" aria-label="Primary navigation">
                    <a href="#features">Features</a>
                    <a href="#metrics">Metrics</a>
                    <a href="#solutions">Solutions</a>
                </nav>

                <div class="welcome-actions">
                    <a href="{{ route('login') }}" class="welcome-btn">Sign in</a>
                    <a href="{{ route('register') }}" class="welcome-btn primary">Create account</a>
                </div>
            </header>

            <main class="welcome-hero">
                <section class="welcome-copy">
                    <p class="eyebrow">ALL-ASSET CONTROL</p>
                    <h1>See every asset. Manage every move.</h1>
                    <p>
                        Axora keeps your inventory, assignments, maintenance, and approvals on one clean dashboard so your team can move faster with fewer manual gaps.
                    </p>

                    <div class="welcome-cta">
                        <a href="{{ route('login') }}" class="welcome-btn primary">Open workspace</a>
                        <a href="#features" class="welcome-btn">View features</a>
                    </div>

                    <div class="welcome-stats" id="metrics">
                        <div class="mini-stat">
                            <strong>1,284</strong>
                            <span>Assets tracked</span>
                        </div>
                        <div class="mini-stat">
                            <strong>94%</strong>
                            <span>Operational uptime</span>
                        </div>
                        <div class="mini-stat">
                            <strong>12</strong>
                            <span>Pending reviews</span>
                        </div>
                    </div>
                </section>

                <aside class="overview-panel" aria-label="Axora summary panel">
                    <div class="panel-header">
                        <span>Workspace overview</span>
                        <span class="status-pill">System operational</span>
                    </div>

                    <div class="overview-grid">
                        <div class="panel-card">
                            <h3>Total assets</h3>
                            <strong>1284</strong>
                            <span>Registered in the system</span>
                        </div>
                        <div class="panel-card">
                            <h3>Available</h3>
                            <strong>419</strong>
                            <span>Ready for assignment</span>
                        </div>
                        <div class="panel-card">
                            <h3>Assigned</h3>
                            <strong>564</strong>
                            <span>Currently in use</span>
                        </div>
                        <div class="panel-card">
                            <h3>Maintenance</h3>
                            <strong>27</strong>
                            <span>In repair or review</span>
                        </div>
                    </div>
                </aside>
            </main>

            <section class="feature-section" id="features">
                <div class="section-heading">
                    <h2>Built for modern asset operations</h2>
                    <p>Keep governance, accountability, and maintenance in one place.</p>
                </div>

                <div class="feature-grid" id="solutions">
                    <article class="feature-card">
                        <div class="feature-icon">?</div>
                        <h3>Asset registry</h3>
                        <p>Track ownership, lifecycle, condition, and location with a single source of truth for every device and asset.</p>
                    </article>
                    <article class="feature-card">
                        <div class="feature-icon">?</div>
                        <h3>Smart approvals</h3>
                        <p>Route assignment, transfer, return, and maintenance requests through clear approval flows with audit trails.</p>
                    </article>
                    <article class="feature-card">
                        <div class="feature-icon">?</div>
                        <h3>Operational visibility</h3>
                        <p>Monitor asset health, current usage, and pending attention from one clean dashboard built for teams.</p>
                    </article>
                </div>
            </section>

            <div class="cta-strip">
                <div>
                    <h3>Ready to work smarter with your assets?</h3>
                    <p>Keep your operations visible, responsive, and consistent from day one.</p>
                </div>
                <a href="{{ route('register') }}" class="welcome-btn primary">Get started</a>
            </div>
        </div>
    </body>
</html>
