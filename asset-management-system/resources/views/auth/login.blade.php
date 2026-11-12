<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#ffffff">
    <title>Sign in - Axora</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="auth-page auth-reference-page">
    <main class="auth-shell">
        <header class="auth-header">
            <a class="auth-login-brand" href="{{ route('login') }}">Axora</a>
            <div class="auth-header-help">
                <span class="auth-help-copy">Need help?</span>
                <span class="auth-help-link">Contact support</span>
            </div>
        </header>
        <div class="auth-main">
            <section class="auth-card" aria-label="Sign in to Axora">
                <p class="eyebrow">YOUR WORKSPACE AWAITS</p>
                <h1>Welcome back</h1>
                <p class="auth-subtitle">Log in to manage your assets with confidence.</p>
                <form class="auth-form" method="POST" action="{{ route('login.store') }}">
                    @csrf
                    <label class="form-field">Gmail address or Mobile Number
                        <input type="text" name="identifier" value="{{ old('identifier') }}" autocomplete="username" required autofocus placeholder="you@gmail.com or 09...">
                        @error('identifier')<span class="field-error">{{ $message }}</span>@enderror
                    </label>
                    <label class="form-field">Password
                        <span class="password-input-wrap">
                            <input id="login-password" type="password" name="password" autocomplete="current-password" required placeholder="Enter your password">
                            <button class="password-toggle" type="button" aria-label="Show password" aria-pressed="false" aria-controls="login-password" data-password-toggle>
                                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true" data-eye-open><path d="M2.5 12s3.4-6 9.5-6 9.5 6 9.5 6-3.4 6-9.5 6-9.5-6-9.5-6Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/><circle cx="12" cy="12" r="2.7" stroke="currentColor" stroke-width="1.7"/></svg>
                                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true" data-eye-closed hidden><path d="m3 3 18 18M10.6 6.2A10.7 10.7 0 0 1 12 6c6.1 0 9.5 6 9.5 6a17.5 17.5 0 0 1-3 3.6M6.2 6.3C3.8 7.9 2.5 12 2.5 12s3.4 6 9.5 6c1 0 1.9-.2 2.7-.5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/><path d="M9.9 9.9a3 3 0 0 0 4.2 4.2" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg>
                            </button>
                        </span>
                        @error('password')<span class="field-error">{{ $message }}</span>@enderror
                    </label>
                    <div class="auth-options">
                        <label class="remember-field"><input type="checkbox" name="remember" value="1" @checked(old('remember'))> <span>Remember me</span></label>
                        <a href="{{ route('password.request') }}">Forgot password?</a>
                    </div>
                    <button class="button button-primary auth-submit" type="submit">Log in</button>
                </form>
                @if (session('status'))<p class="auth-status" role="status">{{ session('status') }}</p>@endif
                <p class="auth-switch">Don’t have an account? <a href="{{ route('register') }}">Create an employee account</a></p>
            </section>
        </div>
        <footer class="auth-footer">
            <span>Privacy Policy</span>
            <span>Terms of Service</span>
            <span class="auth-footer-tagline">Axora &middot; Asset management, simplified.</span>
        </footer>
    </main>
</body>
</html>
