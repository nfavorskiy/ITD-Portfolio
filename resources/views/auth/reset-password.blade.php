<x-guest-layout>
    <div class="container mt-5" style="max-width: 500px;">
        <h2 class="mb-4 text-center">Reset Password</h2>

        <form method="POST" action="{{ route('password.store') }}">
            @csrf

            <input type="hidden" name="token" value="{{ $request->route('token') }}">

            <div class="mb-3">
                <label for="email" class="form-label">Email address</label>
                <input id="email" class="form-control" type="email" name="email" value="{{ old('email', $request->email) }}" required autofocus>
                @error('email') <div class="text-danger small">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">New Password</label>
                <input id="password" class="form-control @error('password') is-invalid @enderror" type="password" name="password" required autocomplete="new-password">
                <div id="password-requirements" class="small mt-2" style="display: none;">
                    <div class="mb-1 text-muted">Password must contain:</div>
                    <ul class="list-unstyled mb-0">
                        <li id="req-length" class="text-danger">
                            <span class="req-icon">✗</span> From 8 to 255 characters
                        </li>
                        <li id="req-uppercase" class="text-danger">
                            <span class="req-icon">✗</span> One uppercase letter
                        </li>
                        <li id="req-lowercase" class="text-danger">
                            <span class="req-icon">✗</span> One lowercase letter
                        </li>
                        <li id="req-number" class="text-danger">
                            <span class="req-icon">✗</span> One number
                        </li>
                        <li id="req-special" class="text-danger">
                            <span class="req-icon">✗</span> One special character
                        </li>
                    </ul>
                </div>
                <div id="password-feedback" class="small mt-1" style="display: none;"></div>
                @error('password') <div class="text-danger small">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3">
                <label for="password_confirmation" class="form-label">Confirm New Password</label>
                <input id="password_confirmation" class="form-control" type="password" name="password_confirmation" required>
                <div id="password-confirmation-feedback" class="small mt-1" style="display: none;"></div>
            </div>

            <button type="submit" class="btn btn-success w-100" id="reset-btn">Reset Password</button>
        </form>
    </div>
    <script nonce="{{ $cspNonce }}">
    document.addEventListener('DOMContentLoaded', function() {
        const passwordInput = document.getElementById('password');
        const passwordRequirements = document.getElementById('password-requirements');
        const passwordFeedback = document.getElementById('password-feedback');
        const passwordConfirmationInput = document.getElementById('password_confirmation');
        const passwordConfirmationFeedback = document.getElementById('password-confirmation-feedback');
        const resetBtn = document.getElementById('reset-btn');

        passwordInput.addEventListener('focus', function() {
            passwordRequirements.style.display = 'block';
        });

        passwordInput.addEventListener('input', function() {
            validatePassword();
            validatePasswordConfirmation();
        });

        passwordInput.addEventListener('blur', function() {
            if (passwordInput.value.length === 0) {
                passwordRequirements.style.display = 'none';
                passwordFeedback.textContent = 'Please enter your password.';
                passwordFeedback.className = 'small mt-1 text-danger';
                passwordFeedback.style.display = 'block';
                passwordInput.classList.add('is-invalid');
                updateResetButton();
            }
        });

        passwordConfirmationInput.addEventListener('input', function() {
            validatePasswordConfirmation();
        });

        passwordConfirmationInput.addEventListener('blur', function() {
            if (passwordConfirmationInput.value.length === 0) {
                passwordConfirmationFeedback.textContent = 'Please confirm your password.';
                passwordConfirmationFeedback.className = 'small mt-1 text-danger';
                passwordConfirmationFeedback.style.display = 'block';
                passwordConfirmationInput.classList.add('is-invalid');
                updateResetButton();
            }
        });

        function validatePassword() {
            const password = passwordInput.value;
            passwordFeedback.style.display = 'none';

            if (password.length === 0) {
                passwordRequirements.style.display = 'none';
                passwordFeedback.textContent = 'Please enter your password.';
                passwordFeedback.className = 'small mt-1 text-danger';
                passwordFeedback.style.display = 'block';
                passwordInput.classList.add('is-invalid');
                updateRequirement('req-length', false);
                updateRequirement('req-uppercase', false);
                updateRequirement('req-lowercase', false);
                updateRequirement('req-number', false);
                updateRequirement('req-special', false);
                updateResetButton();
                return;
            }

            passwordRequirements.style.display = 'block';
            passwordInput.classList.remove('is-invalid', 'is-valid');
            let allValid = true;

            const lengthValid = password.length >= 8 && password.length <= 255;
            updateRequirement('req-length', lengthValid);
            if (!lengthValid) allValid = false;

            const uppercaseValid = /[A-Z]/.test(password);
            updateRequirement('req-uppercase', uppercaseValid);
            if (!uppercaseValid) allValid = false;

            const lowercaseValid = /[a-z]/.test(password);
            updateRequirement('req-lowercase', lowercaseValid);
            if (!lowercaseValid) allValid = false;

            const numberValid = /\d/.test(password);
            updateRequirement('req-number', numberValid);
            if (!numberValid) allValid = false;

            const specialValid = /[^a-zA-Z0-9\s]/.test(password);
            updateRequirement('req-special', specialValid);
            if (!specialValid) allValid = false;

            if (password.length > 0) {
                if (allValid) {
                    passwordInput.classList.add('is-valid');
                } else {
                    passwordInput.classList.add('is-invalid');
                }
            }
            updateResetButton();
        }

        function updateRequirement(requirementId, isValid) {
            const element = document.getElementById(requirementId);
            const icon = element.querySelector('.req-icon');
            if (isValid) {
                element.className = 'text-success';
                icon.textContent = '✓';
            } else {
                element.className = 'text-danger';
                icon.textContent = '✗';
            }
        }

        function validatePasswordConfirmation() {
            const password = passwordInput.value;
            const confirmation = passwordConfirmationInput.value;
            passwordConfirmationFeedback.style.display = 'none';
            passwordConfirmationInput.classList.remove('is-invalid', 'is-valid');

            if (confirmation.length === 0) {
                passwordConfirmationFeedback.textContent = 'Please confirm your password.';
                passwordConfirmationFeedback.className = 'small mt-1 text-danger';
                passwordConfirmationFeedback.style.display = 'block';
                passwordConfirmationInput.classList.add('is-invalid');
                updateResetButton();
                return;
            }

            if (password !== confirmation) {
                passwordConfirmationFeedback.textContent = 'Passwords do not match.';
                passwordConfirmationFeedback.className = 'small mt-1 text-danger';
                passwordConfirmationFeedback.style.display = 'block';
                passwordConfirmationInput.classList.add('is-invalid');
            } else {
                passwordConfirmationFeedback.textContent = '✓ Passwords match!';
                passwordConfirmationFeedback.className = 'small mt-1 text-success';
                passwordConfirmationFeedback.style.display = 'block';
                passwordConfirmationInput.classList.add('is-valid');
            }
            updateResetButton();
        }

        function updateResetButton() {
            const passwordValid = passwordInput.classList.contains('is-valid');
            const confirmationValid = passwordConfirmationInput.classList.contains('is-valid');
            resetBtn.disabled = !(passwordValid && confirmationValid);
        }

        updateResetButton();
    });
    </script>
</x-guest-layout>