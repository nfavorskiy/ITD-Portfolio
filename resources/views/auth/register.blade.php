<x-guest-layout :title="'Sign Up - ' . config('app.name')">
    @if($errors->any() || session('registration_failed'))
        <div id="registration-error-notification" class="alert alert-danger alert-dismissible fade show position-fixed" style="top: 62px; right: 20px; z-index: 1050; min-width: 300px;">
            <strong>Failed!</strong> Please make changes to the form and try again.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    <div class="container mt-5" style="max-width: 500px;">
        <h2 class="mb-4 text-center">Sign Up</h2>

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <div class="mb-3">
                <label for="name" class="form-label">Username<span class="text-danger">*</span></label>
                <input id="name" class="form-control @error('name') is-invalid @enderror" type="text" name="name" value="{{ old('name') }}" autofocus placeholder="Choose a username">
                <div id="name-feedback" class="small mt-1" style="display: none;"></div>
                <div id="name-loading" class="small mt-1 text-muted" style="display: none;">Checking availability...</div>
                @error('name') 
                    <div class="text-danger small server-error" data-field="name">{{ $message }}</div> 
                @enderror
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">Email Address<span class="text-danger">*</span></label>
                <input id="email" class="form-control @error('email') is-invalid @enderror" type="text" name="email" value="{{ old('email') }}" placeholder="Enter your email address">
                <div id="email-feedback" class="small mt-1" style="display: none;"></div>
                <div id="email-loading" class="small mt-1 text-muted" style="display: none;">Checking availability...</div>
                @error('email') 
                    <div class="text-danger small server-error" data-field="email">{{ $message }}</div> 
                @enderror
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Password<span class="text-danger">*</span></label>
                <input id="password" class="form-control @error('password') is-invalid @enderror" type="password" name="password" autocomplete="new-password" placeholder="Create a secure password">
                
                <div id="password-feedback" class="small mt-1" style="display: none;"></div>

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
                
                @error('password') 
                    <div class="text-danger small server-error" data-field="password">{{ $message }}</div> 
                @enderror
            </div>

            <div class="mb-3">
                <label for="password_confirmation" class="form-label">Confirm Password<span class="text-danger">*</span></label>
                <input id="password_confirmation" class="form-control @error('password_confirmation') is-invalid @enderror" type="password" name="password_confirmation" placeholder="Re-enter your password">
                <div id="password-confirmation-feedback" class="small mt-1" style="display: none;"></div>
                @error('password_confirmation') 
                    <div class="text-danger small server-error" data-field="password_confirmation">{{ $message }}</div> 
                @enderror
            </div>

            <button type="submit" class="btn btn-success w-100" id="register-btn">Register</button>

            <div class="mt-3 text-center">
                <a href="{{ route('login') }}">Already have an account? Log in</a>
            </div>
        </form>
    </div>

    <script nonce="{{ $cspNonce }}">
    document.addEventListener('DOMContentLoaded', function() {
        const nameInput = document.getElementById('name');
        const nameFeedback = document.getElementById('name-feedback');
        const nameLoading = document.getElementById('name-loading');
        
        const emailInput = document.getElementById('email');
        const emailFeedback = document.getElementById('email-feedback');
        const emailLoading = document.getElementById('email-loading');
        
        const passwordInput = document.getElementById('password');
        const passwordFeedback = document.getElementById('password-feedback');
        const passwordRequirements = document.getElementById('password-requirements');
        
        const passwordConfirmationInput = document.getElementById('password_confirmation');
        const passwordConfirmationFeedback = document.getElementById('password-confirmation-feedback');
        
        const registerBtn = document.getElementById('register-btn');
        let nameDebounceTimer;
        let emailDebounceTimer;

        const hasServerErrors = document.querySelectorAll('.server-error').length > 0;
        
        if (!hasServerErrors) {
            registerBtn.disabled = true;
        }

        const errorNotification = document.getElementById('registration-error-notification');
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
            }, 10000);
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

        nameInput.addEventListener('input', function() {
            clearTimeout(nameDebounceTimer);
            hideServerError('name');
            nameFeedback.style.display = 'none';
            if (nameLoading) nameLoading.style.display = 'none';
            nameInput.classList.remove('is-invalid', 'is-valid');
            updateRegisterButton();
            
            const name = String(this.value).trim();
            
            if (name.length === 0) {
                nameFeedback.textContent = 'Please enter your username.';
                nameFeedback.className = 'small mt-1 text-danger';
                showClientFeedback('name', nameFeedback);
                nameInput.classList.add('is-invalid');
                updateRegisterButton();
                return;
            }

            if (name.length > 255) {
                nameFeedback.textContent = 'Username must not exceed 255 characters.';
                nameFeedback.className = 'small mt-1 text-danger';
                showClientFeedback('name', nameFeedback);
                nameInput.classList.add('is-invalid');
                updateRegisterButton();
                return;
            }

            const nameRegex = /^[a-zA-Z0-9\s\-\_\.]+$/;
            if (!nameRegex.test(name)) {
                nameFeedback.textContent = 'Username can only contain letters, numbers, spaces, hyphens, underscores, and dots.';
                nameFeedback.className = 'small mt-1 text-danger';
                showClientFeedback('name', nameFeedback);
                nameInput.classList.add('is-invalid');
                updateRegisterButton();
                return;
    }
            
            if (nameLoading) nameLoading.style.display = 'block';
            
            nameDebounceTimer = setTimeout(() => {
                checkNameAvailability(name);
            }, 300);
        });

        nameInput.addEventListener('blur', function() {
            const name = String(this.value).trim();
            
            if (name.length === 0) {
                nameFeedback.textContent = 'Please enter your username.';
                nameFeedback.className = 'small mt-1 text-danger';
                showClientFeedback('name', nameFeedback);
                nameInput.classList.add('is-invalid');
                if (nameLoading) nameLoading.style.display = 'none';
                updateRegisterButton();
                return;
            }

            if (name.length > 255) {
                nameFeedback.textContent = 'Username must not exceed 255 characters.';
                nameFeedback.className = 'small mt-1 text-danger';
                showClientFeedback('name', nameFeedback);
                nameInput.classList.add('is-invalid');
                if (nameLoading) nameLoading.style.display = 'none';
                updateRegisterButton();
            }

            const nameRegex = /^[a-zA-Z0-9\s\-\_\.]+$/;
            if (!nameRegex.test(name)) {
                nameFeedback.textContent = 'Username can only contain letters, numbers, spaces, hyphens, underscores, and dots.';
                nameFeedback.className = 'small mt-1 text-danger';
                showClientFeedback('name', nameFeedback);
                nameInput.classList.add('is-invalid');
                updateRegisterButton();
                return;
            }
        });

        emailInput.addEventListener('input', function() {
            clearTimeout(emailDebounceTimer);
            hideServerError('email');
            emailFeedback.style.display = 'none';
            if (emailLoading) emailLoading.style.display = 'none';
            emailInput.classList.remove('is-invalid', 'is-valid');
            updateRegisterButton();
            
            const email = String(this.value).trim();
            
            if (email.length === 0) {
                emailFeedback.textContent = 'Please enter your email address.';
                emailFeedback.className = 'small mt-1 text-danger';
                showClientFeedback('email', emailFeedback);
                emailInput.classList.add('is-invalid');
                updateRegisterButton();
                return;
            }

            if (email.length > 255) {
                emailFeedback.textContent = 'Email must not exceed 255 characters.';
                emailFeedback.className = 'small mt-1 text-danger';
                showClientFeedback('email', emailFeedback);
                emailInput.classList.add('is-invalid');
                updateRegisterButton();
                return;
            }
            
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                emailFeedback.textContent = 'Please enter a valid email address (e.g., user@example.com).';
                emailFeedback.className = 'small mt-1 text-danger';
                showClientFeedback('email', emailFeedback);
                emailInput.classList.add('is-invalid');
                updateRegisterButton();
                return;
            }
            
            if (emailLoading) emailLoading.style.display = 'block';
            
            emailDebounceTimer = setTimeout(() => {
                checkEmailAvailability(email);
            }, 300);
        });

        emailInput.addEventListener('blur', function() {
            const email = String(this.value).trim();
            
            if (email.length === 0) {
                emailFeedback.textContent = 'Please enter your email address.';
                emailFeedback.className = 'small mt-1 text-danger';
                showClientFeedback('email', emailFeedback);
                emailInput.classList.add('is-invalid');
                if (emailLoading) emailLoading.style.display = 'none';
                updateRegisterButton();
            }
        });

        passwordInput.addEventListener('focus', function() {
            hideServerError('password');
            passwordRequirements.style.display = 'block';
        });

        passwordInput.addEventListener('input', function() {
            hideServerError('password');
            validatePassword();
            validatePasswordConfirmation();
        });

        passwordInput.addEventListener('blur', function() {
            if (passwordInput.value.length === 0) {
                passwordRequirements.style.display = 'none';
                passwordFeedback.textContent = 'Please enter your password.';
                passwordFeedback.className = 'small mt-1 text-danger';
                showClientFeedback('password', passwordFeedback);
                passwordInput.classList.add('is-invalid');
                updateRegisterButton();
            }
        });

        passwordConfirmationInput.addEventListener('input', function() {
            hideServerError('password_confirmation');
            validatePasswordConfirmation();
        });

        passwordConfirmationInput.addEventListener('blur', function() {
            if (passwordConfirmationInput.value.length === 0) {
                passwordConfirmationFeedback.textContent = 'Please confirm your password.';
                passwordConfirmationFeedback.className = 'small mt-1 text-danger';
                showClientFeedback('password_confirmation', passwordConfirmationFeedback);
                passwordConfirmationInput.classList.add('is-invalid');
                updateRegisterButton();
            }
        });

        function validatePassword() {
            const password = passwordInput.value;
            passwordFeedback.style.display = 'none';

            if (password.length === 0) {
                passwordRequirements.style.display = 'none';
                passwordFeedback.textContent = 'Please enter your password.';
                passwordFeedback.className = 'small mt-1 text-danger';
                showClientFeedback('password', passwordFeedback);
                passwordInput.classList.add('is-invalid');
                updateRequirement('req-length', false);
                updateRequirement('req-uppercase', false);
                updateRequirement('req-lowercase', false);
                updateRequirement('req-number', false);
                updateRequirement('req-special', false);
                updateRegisterButton();
                return;
            }
            
            if (password.length > 0) {
                passwordRequirements.style.display = 'block';
            }
            
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
            
            updateRegisterButton();
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
                showClientFeedback('password_confirmation', passwordConfirmationFeedback);
                passwordConfirmationInput.classList.add('is-invalid');
                updateRegisterButton();
                return;
            }
            
            if (password !== confirmation) {
                passwordConfirmationFeedback.textContent = 'Passwords do not match.';
                passwordConfirmationFeedback.className = 'small mt-1 text-danger';
                showClientFeedback('password_confirmation', passwordConfirmationFeedback);
                passwordConfirmationInput.classList.add('is-invalid');
            } else {
                passwordConfirmationFeedback.textContent = '✓ Passwords match!';
                passwordConfirmationFeedback.className = 'small mt-1 text-success';
                showClientFeedback('password_confirmation', passwordConfirmationFeedback);
                passwordConfirmationInput.classList.add('is-valid');
            }
            
            updateRegisterButton();
        }

        function checkNameAvailability(name) {
            const nameStr = String(name);
            
            fetch('/check-name-availability', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ name: nameStr })
            })
            .then(response => response.json())
            .then(data => {
                if (nameLoading) nameLoading.style.display = 'none';
                
                if (data.available === false) {
                    nameFeedback.textContent = 'This name is already taken. Please choose a different name.';
                    nameFeedback.className = 'small mt-1 text-danger';
                    showClientFeedback('name', nameFeedback);
                    nameInput.classList.add('is-invalid');
                } else {
                    nameFeedback.textContent = '✓ Name is available!';
                    nameFeedback.className = 'small mt-1 text-success';
                    showClientFeedback('name', nameFeedback);
                    nameInput.classList.add('is-valid');
                }
                updateRegisterButton();
            })
            .catch(error => {
                if (nameLoading) nameLoading.style.display = 'none';
                console.error('Error checking name availability:', error);
                nameFeedback.textContent = 'Error checking name availability. Please try again.';
                nameFeedback.className = 'small mt-1 text-warning';
                showClientFeedback('name', nameFeedback);
                updateRegisterButton();
            });
        }

        function checkEmailAvailability(email) {
            const emailStr = String(email);
            
            fetch('/check-email-availability', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ email: emailStr })
            })
            .then(response => response.json())
            .then(data => {
                if (emailLoading) emailLoading.style.display = 'none';
                
                if (data.available === false) {
                    emailFeedback.textContent = 'This email is already registered. Please use a different email.';
                    emailFeedback.className = 'small mt-1 text-danger';
                    showClientFeedback('email', emailFeedback);
                    emailInput.classList.add('is-invalid');
                } else {
                    emailFeedback.textContent = '✓ Email is available!';
                    emailFeedback.className = 'small mt-1 text-success';
                    showClientFeedback('email', emailFeedback);
                    emailInput.classList.add('is-valid');
                }
                updateRegisterButton();
            })
            .catch(error => {
                if (emailLoading) emailLoading.style.display = 'none';
                console.error('Error checking email availability:', error);
                emailFeedback.textContent = 'Error checking email availability. Please try again.';
                emailFeedback.className = 'small mt-1 text-warning';
                showClientFeedback('email', emailFeedback);
                updateRegisterButton();
            });
        }

        function updateRegisterButton() {
            const nameInvalid = nameInput.classList.contains('is-invalid');
            const emailInvalid = emailInput.classList.contains('is-invalid');
            const passwordInvalid = passwordInput.classList.contains('is-invalid');
            const confirmationInvalid = passwordConfirmationInput.classList.contains('is-invalid');
            
            const nameEmpty = nameInput.value.trim() === '';
            const emailEmpty = emailInput.value.trim() === '';
            const passwordEmpty = passwordInput.value === '';
            const confirmationEmpty = passwordConfirmationInput.value === '';
            
            const nameValid = nameInput.classList.contains('is-valid');
            const emailValid = emailInput.classList.contains('is-valid');
            const passwordValid = passwordInput.classList.contains('is-valid');
            const confirmationValid = passwordConfirmationInput.classList.contains('is-valid');
            
            const hasVisibleServerErrors = Array.from(document.querySelectorAll('.server-error'))
                .some(error => error.style.display !== 'none');
            
            if (hasVisibleServerErrors) {
                registerBtn.disabled = false;
                return;
            }
            
            const shouldEnable = nameValid && emailValid && passwordValid && confirmationValid && 
                                !nameEmpty && !emailEmpty && !passwordEmpty && !confirmationEmpty &&
                                !nameInvalid && !emailInvalid && !passwordInvalid && !confirmationInvalid;
            
            registerBtn.disabled = !shouldEnable;
        }

        updateRegisterButton();
    });
    </script>
</x-guest-layout>