<x-guest-layout>
    <div class="container mt-5" style="max-width: 500px;">
        <h2 class="mb-4 text-center">Forgot Your Password?</h2>

        @if (session('status'))
            <div class="alert alert-success">{{ session('status') }}</div>
        @endif

        <form method="POST" action="{{ route('password.email') }}">
            @csrf

            <div class="mb-3">
                <label for="email" class="form-label">Email address</label>
                <input id="email" class="form-control" type="email" name="email" value="{{ old('email') }}" required autofocus>
                @error('email') <div class="text-danger small">{{ $message }}</div> @enderror
            </div>

            <button type="submit" class="btn btn-primary w-100">Send Password Reset Link</button>
        </form>
    </div>
</x-guest-layout>
