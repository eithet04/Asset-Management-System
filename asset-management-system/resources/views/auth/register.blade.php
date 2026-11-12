<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#ffffff">
    <title>Create an account - Axora</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="auth-page auth-reference-page">
    <main class="auth-shell">
        <header class="auth-header">
            <span class="auth-help-copy">Need help?</span>
            <span class="auth-help-link">Contact support</span>
        </header>
        <div class="auth-main">
            <section class="auth-card auth-card-register" aria-label="Create an Axora account">
                <p class="eyebrow">START WITH AXORA</p>
                <h1>Create your account</h1>
                <p class="auth-subtitle">One place for every asset. Let’s get you set up.</p>
                <form class="auth-form" method="POST" action="{{ route('register.store') }}">
                    @csrf
                    <label class="form-field">Full name
                        <input type="text" name="name" value="{{ old('name') }}" autocomplete="name" required autofocus placeholder="Enter your full name">
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
                            <input id="register-password" type="password" name="password" autocomplete="new-password" required placeholder="Create a password">
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
                    <button class="button button-primary auth-submit" type="submit">Create account</button>
                </form>
                <p class="auth-role-note"><span aria-hidden="true">✓</span> New accounts are always created with employee access.</p>
                <p class="auth-switch">Already have an account? <a href="{{ route('login') }}">Log in</a></p>
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
