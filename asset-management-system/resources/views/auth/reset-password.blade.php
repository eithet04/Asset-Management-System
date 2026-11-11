<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#111a2b">
    <title>Choose a new password · Axora</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="auth-page">
    <main class="auth-shell">
        <a class="brand auth-brand" href="{{ route('login') }}" aria-label="Axora sign in">
            <span class="brand-mark" aria-hidden="true">▣</span>
            <span class="brand-copy"><strong>Ax<span>ora</span></strong><small>CONTROL CENTER</small></span>
        </a>
        <section class="auth-card">
            <p class="eyebrow">ACCOUNT RECOVERY</p>
            <h1>Choose a new password</h1>
            <p class="auth-subtitle">Use at least 8 characters with uppercase, lowercase, a number, and a symbol.</p>
            <form class="auth-form" method="POST" action="{{ route('password.update') }}">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">
                <label class="form-field">Gmail address
                    <input type="email" name="email" value="{{ old('email', $email) }}" autocomplete="email" required>
                    @error('email')<span class="field-error">{{ $message }}</span>@enderror
                </label>
                @foreach (['password' => 'New password', 'password_confirmation' => 'Confirm new password'] as $name => $label)
                    <label class="form-field">{{ $label }}
                        <span class="password-input-wrap">
                            <input id="reset-{{ $name }}" type="password" name="{{ $name }}" autocomplete="new-password" required>
                            <button class="password-toggle" type="button" aria-label="Show password" aria-pressed="false" aria-controls="reset-{{ $name }}" data-password-toggle>
                                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true" data-eye-open><path d="M2.5 12s3.4-6 9.5-6 9.5 6 9.5 6-3.4 6-9.5 6-9.5-6-9.5-6Z" stroke="currentColor" stroke-width="1.7"/><circle cx="12" cy="12" r="2.7" stroke="currentColor" stroke-width="1.7"/></svg>
                                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true" data-eye-closed hidden><path d="m3 3 18 18M10.6 6.2A10.7 10.7 0 0 1 12 6c6.1 0 9.5 6 9.5 6a17.5 17.5 0 0 1-3 3.6M6.2 6.3C3.8 7.9 2.5 12 2.5 12s3.4 6 9.5 6c1 0 1.9-.2 2.7-.5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg>
                            </button>
                        </span>
                        @error('password')<span class="field-error">{{ $message }}</span>@enderror
                    </label>
                @endforeach
                <button class="button button-primary auth-submit" type="submit">Save new password</button>
            </form>
        </section>
        <p class="auth-footnote">Secure access to your organization's asset workspace.</p>
    </main>
</body>
</html>
