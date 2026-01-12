<x-guest-layout :title="'Sign In - ' . config('app.name')">
    {{-- CSRF error notification --}}
    @if(session('csrf_error'))
        <div class="alert alert-warning alert-dismissible fade show position-fixed" style="top: 62px; right: 20px; z-index: 1050; min-width: 300px;">
            <strong>Session expired!</strong> {{ session('csrf_error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Login error notification --}}
    @if($errors->any())
        <div id="login-error-notification" class="alert alert-danger alert-dismissible fade show position-fixed" style="top: 62px; right: 20px; z-index: 1050; min-width: 300px;">
            <strong>Login Failed!</strong> Please check your credentials and try again.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="container mt-5" style="max-width: 500px;">
        <h2 class="mb-4 text-center">Sign In</h2>

        {{-- Success notification --}}
        @if (session('status'))
            <div class="alert alert-success">{{ session('status') }}</div>
            <script nonce="{{ $cspNonce }}">
                if (window.history.replaceState) {
                    window.history.replaceState(null, '', '/');
                }
            </script>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf

            @if(request('redirect_to_posts'))
                <input type="hidden" name="redirect_to_posts" value="true">
            @endif

            <div class="mb-3">
                <label for="email" class="form-label">Email address</label>
                <input id="email" class="form-control @error('email') is-invalid @enderror" type="email" name="email" value="{{ old('email') }}" required autofocus>
                <div id="email-feedback" class="small mt-1" style="display: none;"></div>
                @error('email') 
                    <div class="text-danger small server-error" data-field="email">{{ $message }}</div> 
                @enderror
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input id="password" class="form-control @error('password') is-invalid @enderror" type="password" name="password" required autocomplete="current-password">
                <div id="password-feedback" class="small mt-1" style="display: none;"></div>
                @error('password') 
                    <div class="text-danger small server-error" data-field="password">{{ $message }}</div> 
                @enderror
            </div>

            <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input" id="remember_me" name="remember">
                <label class="form-check-label" for="remember_me">Remember Me</label>
            </div>

            <button type="submit" class="btn btn-primary w-100" id="login-btn">Log in</button>

            <div class="mt-3 text-center">
                <a href="{{ route('password.request') }}">Forgot your password?</a><br>
                <a href="{{ route('register') }}">Don't have an account? Sign up</a>
            </div>
        </form>
    </div>

    <script nonce="{{ $cspNonce }}">
    document.addEventListener('DOMContentLoaded', function() {
        const emailInput = document.getElementById('email');
        const emailFeedback = document.getElementById('email-feedback');
        const passwordInput = document.getElementById('password');
        const passwordFeedback = document.getElementById('password-feedback');
        const loginBtn = document.getElementById('login-btn');

        const hasServerErrors = document.querySelectorAll('.server-error').length > 0;
        
        const errorNotification = document.getElementById('login-error-notification');
        if (errorNotification) {
            const closeBtn = errorNotification.querySelector('.btn-close');
            if (closeBtn) {
                closeBtn.addEventListener('click', function() {
                    errorNotification.remove();
                });
            }
            
            setTimeout(() => {
                if (errorNotification && errorNotification.parentNode) {
                    errorNotification.remove();
                }
            }, 8000);
        }

        function hideServerError(fieldName) {
            const serverError = document.querySelector(`.server-error[data-field="${fieldName}"]`);
            if (serverError) {
                serverError.style.display = 'none';
            }
        }

        function showClientFeedback(fieldName, feedbackElement) {
            const serverError = document.querySelector(`.server-error[data-field="${fieldName}"]`);
            const serverErrorVisible = serverError && serverError.style.display !== 'none';
            
            if (!serverErrorVisible) {
                feedbackElement.style.display = 'block';
            }
        }

        emailInput.addEventListener('input', function() {
            const email = this.value.trim();
            
            hideServerError('email');
            
            emailFeedback.style.display = 'none';
            emailInput.classList.remove('is-invalid', 'is-valid');
            updateLoginButton();
            
            if (email.length === 0) {
                emailFeedback.textContent = 'Please enter your email address.';
                emailFeedback.className = 'small mt-1 text-danger';
                showClientFeedback('email', emailFeedback);
                emailInput.classList.add('is-invalid');
                updateLoginButton();
                return;
            }
            
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                emailFeedback.textContent = 'Please enter a valid email address.';
                emailFeedback.className = 'small mt-1 text-danger';
                showClientFeedback('email', emailFeedback);
                emailInput.classList.add('is-invalid');
                updateLoginButton();
                return;
            }
            
            emailFeedback.textContent = '✓ Valid email format';
            emailFeedback.className = 'small mt-1 text-success';
            showClientFeedback('email', emailFeedback);
            emailInput.classList.add('is-valid');
            updateLoginButton();
        });

        passwordInput.addEventListener('input', function() {
            const password = this.value;
            
            hideServerError('password');
            
            passwordFeedback.style.display = 'none';
            passwordInput.classList.remove('is-invalid', 'is-valid');
            updateLoginButton();
            
            if (password.length === 0) {
                passwordFeedback.textContent = 'Please enter your password.';
                passwordFeedback.className = 'small mt-1 text-danger';
                showClientFeedback('password', passwordFeedback);
                passwordInput.classList.add('is-invalid');
                updateLoginButton();
                return;
            }
            
            passwordFeedback.textContent = '✓ Password entered';
            passwordFeedback.className = 'small mt-1 text-success';
            showClientFeedback('password', passwordFeedback);
            passwordInput.classList.add('is-valid');
            updateLoginButton();
        });

        function updateLoginButton() {
            const emailInvalid = emailInput.classList.contains('is-invalid');
            const passwordInvalid = passwordInput.classList.contains('is-invalid');
            
            const hasVisibleServerErrors = Array.from(document.querySelectorAll('.server-error'))
                .some(error => error.style.display !== 'none');
            
            if (hasVisibleServerErrors) {
                loginBtn.disabled = false;
                return;
            }
            
            loginBtn.disabled = emailInvalid || passwordInvalid;
        }

        updateLoginButton();
    });
    </script>
</x-guest-layout>