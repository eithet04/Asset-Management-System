<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#111a2b">
    <title>Create an account · Axora</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="auth-page">
    <main class="auth-shell">
        <a class="brand auth-brand" href="{{ route('register') }}" aria-label="Axora registration">
            <span class="brand-mark" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none"><path d="M4.5 7.25 12 3l7.5 4.25v9.5L12 21l-7.5-4.25v-9.5Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/><path d="m4.8 7.4 7.2 4.1 7.2-4.1M12 11.6V21" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/></svg></span>
            <span class="brand-copy"><strong>Ax<span>ora</span></strong><small>CONTROL CENTER</small></span>
        </a>
        <section class="auth-card">
            <p class="eyebrow">GET STARTED</p>
            <h1>Create your account</h1>
            <p class="auth-subtitle">Register for employee self-service access. You’ll verify your Gmail before signing in.</p>
            <form class="auth-form" method="POST" action="{{ route('register.store') }}">
                @csrf
                <label class="form-field">Full name
                    <input type="text" name="name" value="{{ old('name') }}" autocomplete="name" required autofocus placeholder="Your name">
                    @error('name')<span class="field-error">{{ $message }}</span>@enderror
                </label>
                <label class="form-field">Email
                    <input type="email" name="email" value="{{ old('email') }}" autocomplete="email" required placeholder="you@gmail.com">
                    @error('email')<span class="field-error">{{ $message }}</span>@enderror
                </label>
                <label class="form-field">Mobile Number
                    <input type="tel" name="phone" value="{{ old('phone') }}" autocomplete="tel" required placeholder="09 XX XXX XXXX">
                    @error('phone')<span class="field-error">{{ $message }}</span>@enderror
                </label>
                <label class="form-field">Password
                    <span class="password-input-wrap">
                        <input id="register-password" type="password" name="password" autocomplete="new-password" required placeholder="Uppercase, lowercase, number, symbol">
                        <button class="password-toggle" type="button" aria-label="Show password" aria-pressed="false" aria-controls="register-password" data-password-toggle>
                            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true" data-eye-open><path d="M2.5 12s3.4-6 9.5-6 9.5 6 9.5 6-3.4 6-9.5 6-9.5-6-9.5-6Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/><circle cx="12" cy="12" r="2.7" stroke="currentColor" stroke-width="1.7"/></svg>
                            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true" data-eye-closed hidden><path d="m3 3 18 18M10.6 6.2A10.7 10.7 0 0 1 12 6c6.1 0 9.5 6 9.5 6a17.5 17.5 0 0 1-3 3.6M6.2 6.3C3.8 7.9 2.5 12 2.5 12s3.4 6 9.5 6c1 0 1.9-.2 2.7-.5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/><path d="M9.9 9.9a3 3 0 0 0 4.2 4.2" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg>
                        </button>
                    </span>
                </label>
                @error('password')<span class="field-error">{{ $message }}</span>@enderror
                <label class="form-field">Confirm password
                    <span class="password-input-wrap">
                        <input id="register-password-confirmation" type="password" name="password_confirmation" autocomplete="new-password" required placeholder="Re-enter your password">
                        <button class="password-toggle" type="button" aria-label="Show password" aria-pressed="false" aria-controls="register-password-confirmation" data-password-toggle>
                            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true" data-eye-open><path d="M2.5 12s3.4-6 9.5-6 9.5 6 9.5 6-3.4 6-9.5 6-9.5-6-9.5-6Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/><circle cx="12" cy="12" r="2.7" stroke="currentColor" stroke-width="1.7"/></svg>
                            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true" data-eye-closed hidden><path d="m3 3 18 18M10.6 6.2A10.7 10.7 0 0 1 12 6c6.1 0 9.5 6 9.5 6a17.5 17.5 0 0 1-3 3.6M6.2 6.3C3.8 7.9 2.5 12 2.5 12s3.4 6 9.5 6c1 0 1.9-.2 2.7-.5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/><path d="M9.9 9.9a3 3 0 0 0 4.2 4.2" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg>
                        </button>
                    </span>
                </label>
                <button class="button button-primary auth-submit" type="submit">Create employee account <span aria-hidden="true">→</span></button>
            </form>
            <p class="auth-role-note"><span aria-hidden="true">✓</span> New accounts are always created with employee access.</p>
            <p class="auth-switch">Already registered? <a href="{{ route('login') }}">Sign in</a></p>
        </section>
        <p class="auth-footnote">Secure access to your organization's asset workspace.</p>
    </main>
</body>
</html>
