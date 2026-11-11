<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#111a2b">
    <title>Sign in · Axora</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="auth-page">
    <main class="auth-shell">
        <a class="brand auth-brand" href="{{ route('login') }}" aria-label="Axora sign in">
            <span class="brand-mark" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none"><path d="M4.5 7.25 12 3l7.5 4.25v9.5L12 21l-7.5-4.25v-9.5Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/><path d="m4.8 7.4 7.2 4.1 7.2-4.1M12 11.6V21" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/></svg></span>
            <span class="brand-copy"><strong>Ax<span>ora</span></strong><small>CONTROL CENTER</small></span>
        </a>
        <section class="auth-card">
            <p class="eyebrow">WELCOME BACK</p>
            <h1>Sign in to your account</h1>
            <p class="auth-subtitle">Your assets and requests, all in one place.</p>
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
                    <label class="remember-field"><input type="checkbox" name="remember" value="1"> <span>Keep me signed in</span></label>
                    <a href="{{ route('password.request') }}">Forgot password?</a>
                </div>
                <button class="button button-primary auth-submit" type="submit">Sign in <span aria-hidden="true">→</span></button>
            </form>
            @if (session('status'))<p class="auth-status" role="status">{{ session('status') }}</p>@endif
            <p class="auth-switch">New to Axora? <a href="{{ route('register') }}">Create an employee account</a></p>
        </section>
        <p class="auth-footnote">Secure access to your organization's asset workspace.</p>
    </main>
</body>
</html>
