<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#111a2b">
    <title>Verify Gmail · Axora</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="auth-page">
    <main class="auth-shell">
        <a class="brand auth-brand" href="{{ route('login') }}" aria-label="Axora sign in">
            <span class="brand-mark" aria-hidden="true">▣</span>
            <span class="brand-copy"><strong>Ax<span>ora</span></strong><small>CONTROL CENTER</small></span>
        </a>
        <section class="auth-card">
            <p class="eyebrow">EMAIL VERIFICATION</p>
            <h1>Verify your Gmail</h1>
            <p class="auth-subtitle">Use the verification link we sent to your Gmail address before signing in.</p>
            @if (session('status'))<p class="auth-status" role="status">{{ session('status') }}</p>@endif
            @if ($errors->any())<p class="field-error" role="alert">{{ $errors->first() }}</p>@endif
            <form class="auth-form" method="POST" action="{{ route('verification.send') }}">
                @csrf
                <label class="form-field">Gmail address
                    <input type="email" name="email" value="{{ old('email', $email) }}" autocomplete="email" required>
                    @error('email')<span class="field-error">{{ $message }}</span>@enderror
                </label>
                <button class="button button-primary auth-submit" type="submit">Resend verification link</button>
            </form>
            <p class="auth-switch"><a href="{{ route('login') }}">Return to sign in</a></p>
        </section>
        <p class="auth-footnote">Secure access to your organization's asset workspace.</p>
    </main>
</body>
</html>
