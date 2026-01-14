@props(['cspNonce' => null])
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <title>@yield('title', config('app.name', 'Laravel'))</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    @include('layouts.navigation')

    {{-- Standard notification container --}}
    <div id="notification-container" style="position: fixed; top: 62px; right: 20px; z-index: 1050; min-width: 340px; max-width: 400px;">
        @if(session('login_success'))
            <div id="login-notification" class="alert alert-success alert-dismissible fade show mb-2">
                <strong>Welcome!</strong> You are now logged in.
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('post_created'))
            <div id="post-created-notification" class="alert alert-success alert-dismissible fade show mb-2">
                <strong></strong> {{ session('post_created') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('post_updated'))
            <div id="post-updated-notification" class="alert alert-info alert-dismissible fade show mb-2">
                <strong></strong> {{ session('post_updated') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('post_deleted'))
            <div id="post-deleted-notification" class="alert alert-warning alert-dismissible fade show mb-2">
                <strong></strong> {{ session('post_deleted') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('post_not_found'))
            <div id="post-not-found-notification" class="alert alert-warning alert-dismissible fade show mb-2">
                <strong></strong> {{ session('post_not_found') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(auth()->check() && auth()->user() instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && !auth()->user()->hasVerifiedEmail())
            <div class="alert alert-warning mb-2" style="pointer-events: none;">
                <strong>Action Required:</strong> Please verify your email address to unlock all features.
            </div>
        @endif

        @if(session('email_verified'))
            <div id="email-verified-notification" class="alert alert-success alert-dismissible fade show mb-2">
                <strong>Email Verified!</strong> {{ session('email_verified') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('password_updated'))
            <div id="password-updated-notification" class="alert alert-success alert-dismissible fade show mb-2">
                <strong>Password Updated!</strong> {{ session('password_updated') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('account_deleted'))
            <div id="account-deleted-notification" class="alert alert-warning alert-dismissible fade show mb-2">
                <strong>Account Deleted!</strong> {{ session('account_deleted') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('csrf_error'))
            <div class="alert alert-warning alert-dismissible fade show mb-2">
                {{ session('csrf_error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
    </div>

    <main class="container py-4">
        <x-breadcrumbs :links="$links ?? []" />
        @yield('content')
    </main>

    <script nonce="{{ $cspNonce }}" src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script nonce="{{ $cspNonce }}">
    document.addEventListener('DOMContentLoaded', function() {
        [
            'login-notification',
            'post-created-notification',
            'post-updated-notification',
            'post-deleted-notification',
            'post-not-found-notification',
            'account-deleted-notification',
            'email-verified-notification',
            'password-updated-notification',
        ].forEach(function(id) {
            const notification = document.getElementById(id);
            if (notification) {
                setTimeout(function() {
                    notification.classList.remove('show');
                    setTimeout(function() {
                        notification.remove();
                    }, 150);
                }, 3000);
            }
        });
    });
    </script>

    @stack('scripts')
</body>
</html>