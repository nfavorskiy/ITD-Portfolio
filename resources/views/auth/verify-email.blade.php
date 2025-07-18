<x-guest-layout>
    <div class="container mt-5" style="max-width: 600px;">
        <h2 class="mb-4 text-center">Verify Your Email</h2>

        @if (session('status') === 'verification-link-sent')
            <div class="alert alert-success text-center">
                A new verification link has been sent to your email address.
            </div>
        @endif

        <p class="text-center mb-4">
            Thanks for signing up! Before getting started, please verify your email address by clicking the link we just emailed to you.
        </p>

        <div class="d-flex flex-column align-items-center gap-3 mt-4">
            <form method="POST" action="{{ route('verification.send') }}">
                @csrf
                <button type="submit" class="btn btn-primary" style="width:240px;">Resend Verification Email</button>
            </form>

            <a href="{{ route('home') }}" class="btn btn-secondary" style="width:240px;">Go to Homepage</a>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn btn-danger" style="width:240px;">Logout</button>
            </form>
        </div>
    </div>
</x-guest-layout>
