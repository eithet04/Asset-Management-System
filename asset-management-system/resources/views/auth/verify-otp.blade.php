<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#111a2b">
    <title>Verify sign-in · Axora</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="auth-page">
    <main class="auth-shell">
        <a class="brand auth-brand" href="{{ route('login') }}" aria-label="Axora sign in">
            <span class="brand-mark" aria-hidden="true">▣</span>
            <span class="brand-copy"><strong>Ax<span>ora</span></strong><small>CONTROL CENTER</small></span>
        </a>
        <section class="auth-card">
            <p class="eyebrow">TWO-STEP VERIFICATION</p>
            <h1>Check your Gmail</h1>
            <p class="auth-subtitle">Enter the 6-digit code sent to {{ $email }}. It expires in 5 minutes.</p>
            @if (session('status'))<p class="auth-status" role="status">{{ session('status') }}</p>@endif
            @if ($expired)<p class="field-error" role="alert">This code has expired. Request a new one.</p>@endif
            @if ($locked)<p class="field-error" role="alert">Too many incorrect attempts. Request a new code.</p>@endif
            <form class="auth-form" method="POST" action="{{ route('otp.verify') }}">
                @csrf
                <label class="form-field">6-digit verification code
                    <input class="otp-input" type="text" name="code" inputmode="numeric" autocomplete="one-time-code" pattern="[0-9]{6}" maxlength="6" required autofocus aria-label="6-digit verification code">
                    @error('code')<span class="field-error">{{ $message }}</span>@enderror
                </label>
                <button class="button button-primary auth-submit" type="submit" @if ($expired || $locked) disabled @endif>Verify and sign in</button>
            </form>
            <form class="resend-form" method="POST" action="{{ route('otp.resend') }}" data-resend-form data-cooldown="{{ $resendAfter }}">
                @csrf
                <button class="text-button" type="submit" @if ($resendAfter > 0) disabled @endif data-resend-button>
                    <span data-resend-label>Resend code</span>
                    <span data-countdown @if ($resendAfter === 0) hidden @endif> ({{ $resendAfter }}s)</span>
                </button>
            </form>
            <p class="auth-switch"><a href="{{ route('login') }}">Return to sign in</a></p>
        </section>
        <p class="auth-footnote">Secure access to your organization's asset workspace.</p>
    </main>
</body>
</html>
