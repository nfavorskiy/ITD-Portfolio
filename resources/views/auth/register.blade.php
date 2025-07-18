<x-guest-layout :title="'Sign Up - ' . config('app.name')">
    <!-- Error notification for failed registration -->
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
                
                <!-- Password feedback for empty field -->
                <div id="password-feedback" class="small mt-1" style="display: none;"></div>

                <!-- Password Requirements Checklist -->
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

    <script>
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

        // Check if there are server errors
        const hasServerErrors = document.querySelectorAll('.server-error').length > 0;
        
        // If no server errors, disable button initially
        if (!hasServerErrors) {
            registerBtn.disabled = true;
        }

        // Auto-dismiss error notification after 5 seconds
        const errorNotification = document.getElementById('registration-error-notification');
        if (errorNotification) {
            // Manual close functionality
            const closeBtn = errorNotification.querySelector('.btn-close');
            if (closeBtn) {
                closeBtn.addEventListener('click', function() {
                    errorNotification.remove();
                });
            }
            
            // Auto-dismiss after 10 seconds
            setTimeout(() => {
                if (errorNotification && errorNotification.parentNode) {
                    errorNotification.remove();
                }
            }, 10000);
        }

        // Function to hide server error for a specific field
        function hideServerError(fieldName) {
            const serverError = document.querySelector(`.server-error[data-field="${fieldName}"]`);
            if (serverError) {
                serverError.style.display = 'none';
            }
        }

        // Function to show client feedback if no server error is visible
        function showClientFeedback(fieldName, feedbackElement) {
            const serverError = document.querySelector(`.server-error[data-field="${fieldName}"]`);
            const serverErrorVisible = serverError && serverError.style.display !== 'none';
            
            if (!serverErrorVisible) {
                feedbackElement.style.display = 'block';
            }
        }

        // Name validation
        nameInput.addEventListener('input', function() {
            clearTimeout(nameDebounceTimer);
            
            // Hide server error when user starts typing
            hideServerError('name');
            
            // Reset feedback
            nameFeedback.style.display = 'none';
            if (nameLoading) nameLoading.style.display = 'none';
            nameInput.classList.remove('is-invalid', 'is-valid');
            updateRegisterButton();
            
            const name = String(this.value).trim();
            
            // Check if name is empty
            if (name.length === 0) {
                nameFeedback.textContent = 'Please enter your username.';
                nameFeedback.className = 'small mt-1 text-danger';
                showClientFeedback('name', nameFeedback);
                nameInput.classList.add('is-invalid');
                updateRegisterButton();
                return;
            }

            // Check if name exceeds 255 characters
            if (name.length > 255) {
                nameFeedback.textContent = 'Username must not exceed 255 characters.';
                nameFeedback.className = 'small mt-1 text-danger';
                showClientFeedback('name', nameFeedback);
                nameInput.classList.add('is-invalid');
                updateRegisterButton();
                return;
            }
            
            // Show loading indicator
            if (nameLoading) nameLoading.style.display = 'block';
            
            // Debounce the API call
            nameDebounceTimer = setTimeout(() => {
                checkNameAvailability(name);
            }, 300);
        });

        nameInput.addEventListener('blur', function() {
            const name = String(this.value).trim();
            
            // Check if name is empty when user leaves the field
            if (name.length === 0) {
                nameFeedback.textContent = 'Please enter your username.';
                nameFeedback.className = 'small mt-1 text-danger';
                showClientFeedback('name', nameFeedback);
                nameInput.classList.add('is-invalid');
                if (nameLoading) nameLoading.style.display = 'none';
                updateRegisterButton();
                return;
            }

            // Check if name exceeds 255 characters on blur
            if (name.length > 255) {
                nameFeedback.textContent = 'Username must not exceed 255 characters.';
                nameFeedback.className = 'small mt-1 text-danger';
                showClientFeedback('name', nameFeedback);
                nameInput.classList.add('is-invalid');
                if (nameLoading) nameLoading.style.display = 'none';
                updateRegisterButton();
            }
        });

        // Email validation
        emailInput.addEventListener('input', function() {
            clearTimeout(emailDebounceTimer);
            
            // Hide server error when user starts typing
            hideServerError('email');
            
            // Reset feedback
            emailFeedback.style.display = 'none';
            if (emailLoading) emailLoading.style.display = 'none';
            emailInput.classList.remove('is-invalid', 'is-valid');
            updateRegisterButton();
            
            const email = String(this.value).trim();
            
            // Check if email is empty
            if (email.length === 0) {
                emailFeedback.textContent = 'Please enter your email address.';
                emailFeedback.className = 'small mt-1 text-danger';
                showClientFeedback('email', emailFeedback);
                emailInput.classList.add('is-invalid');
                updateRegisterButton();
                return;
            }

            // Check if email exceeds 255 characters
            if (email.length > 255) {
                emailFeedback.textContent = 'Email must not exceed 255 characters.';
                emailFeedback.className = 'small mt-1 text-danger';
                showClientFeedback('email', emailFeedback);
                emailInput.classList.add('is-invalid');
                updateRegisterButton();
                return;
            }
            
            // Strict email format check - same as backend
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                // Show invalid format error immediately
                emailFeedback.textContent = 'Please enter a valid email address (e.g., user@example.com).';
                emailFeedback.className = 'small mt-1 text-danger';
                showClientFeedback('email', emailFeedback);
                emailInput.classList.add('is-invalid');
                updateRegisterButton();
                return;
            }
            
            // Show loading indicator for valid format emails
            if (emailLoading) emailLoading.style.display = 'block';
            
            // Debounce the API call
            emailDebounceTimer = setTimeout(() => {
                checkEmailAvailability(email);
            }, 300);
        });

        emailInput.addEventListener('blur', function() {
            const email = String(this.value).trim();
            
            // Check if email is empty when user leaves the field
            if (email.length === 0) {
                emailFeedback.textContent = 'Please enter your email address.';
                emailFeedback.className = 'small mt-1 text-danger';
                showClientFeedback('email', emailFeedback);
                emailInput.classList.add('is-invalid');
                if (emailLoading) emailLoading.style.display = 'none';
                updateRegisterButton();
            }
        });

        // Password validation
        passwordInput.addEventListener('focus', function() {
            // Hide server error when user focuses on password
            hideServerError('password');
            passwordRequirements.style.display = 'block';
        });

        passwordInput.addEventListener('input', function() {
            // Hide server error when user starts typing
            hideServerError('password');
            validatePassword();
            validatePasswordConfirmation();
        });

        passwordInput.addEventListener('blur', function() {
            // Check if password is empty when user leaves the field
            if (passwordInput.value.length === 0) {
                passwordRequirements.style.display = 'none';
                passwordFeedback.textContent = 'Please enter your password.';
                passwordFeedback.className = 'small mt-1 text-danger';
                showClientFeedback('password', passwordFeedback);
                passwordInput.classList.add('is-invalid');
                updateRegisterButton();
            }
        });

        // Password confirmation validation
        passwordConfirmationInput.addEventListener('input', function() {
            // Hide server error when user starts typing
            hideServerError('password_confirmation');
            validatePasswordConfirmation();
        });

        passwordConfirmationInput.addEventListener('blur', function() {
            // Check if password confirmation is empty when user leaves the field
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

            // Reset feedback
            passwordFeedback.style.display = 'none';

            // Check if password is empty
            if (password.length === 0) {
                passwordRequirements.style.display = 'none';
                passwordFeedback.textContent = 'Please enter your password.';
                passwordFeedback.className = 'small mt-1 text-danger';
                showClientFeedback('password', passwordFeedback);
                passwordInput.classList.add('is-invalid');
                
                // Reset all requirements to invalid state when password is empty
                updateRequirement('req-length', false);
                updateRequirement('req-uppercase', false);
                updateRequirement('req-lowercase', false);
                updateRequirement('req-number', false);
                updateRequirement('req-special', false);
                
                updateRegisterButton();
                return;
            }
            
            // Show requirements when user starts typing
            if (password.length > 0) {
                passwordRequirements.style.display = 'block';
            }
            
            // Reset password input styling
            passwordInput.classList.remove('is-invalid', 'is-valid');
            
            let allValid = true;
            
            // Check length
            const lengthValid = password.length >= 8 && password.length <= 255;
            updateRequirement('req-length', lengthValid);
            if (!lengthValid) allValid = false;
            
            // Check for uppercase letter
            const uppercaseValid = /[A-Z]/.test(password);
            updateRequirement('req-uppercase', uppercaseValid);
            if (!uppercaseValid) allValid = false;
            
            // Check for lowercase letter
            const lowercaseValid = /[a-z]/.test(password);
            updateRequirement('req-lowercase', lowercaseValid);
            if (!lowercaseValid) allValid = false;
            
            // Check for number
            const numberValid = /\d/.test(password);
            updateRequirement('req-number', numberValid);
            if (!numberValid) allValid = false;
            
            // Check for special character
            const specialValid = /[^a-zA-Z0-9\s]/.test(password);
            updateRequirement('req-special', specialValid);
            if (!specialValid) allValid = false;
            
            // Update password input styling
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
            
            // Reset feedback
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
                // Hide loading
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
                // Hide loading
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
            // Check if any field has is-invalid class or is empty
            const nameInvalid = nameInput.classList.contains('is-invalid');
            const emailInvalid = emailInput.classList.contains('is-invalid');
            const passwordInvalid = passwordInput.classList.contains('is-invalid');
            const confirmationInvalid = passwordConfirmationInput.classList.contains('is-invalid');
            
            // Check if required fields are empty
            const nameEmpty = nameInput.value.trim() === '';
            const emailEmpty = emailInput.value.trim() === '';
            const passwordEmpty = passwordInput.value === '';
            const confirmationEmpty = passwordConfirmationInput.value === '';
            
            // Check if fields have valid status
            const nameValid = nameInput.classList.contains('is-valid');
            const emailValid = emailInput.classList.contains('is-valid');
            const passwordValid = passwordInput.classList.contains('is-valid');
            const confirmationValid = passwordConfirmationInput.classList.contains('is-valid');
            
            // If there are server errors, allow submission to show them
            const hasVisibleServerErrors = Array.from(document.querySelectorAll('.server-error'))
                .some(error => error.style.display !== 'none');
            
            if (hasVisibleServerErrors) {
                registerBtn.disabled = false;
                return;
            }
            
            // Button should only be enabled if all fields are valid and none are empty
            const shouldEnable = nameValid && emailValid && passwordValid && confirmationValid && 
                                !nameEmpty && !emailEmpty && !passwordEmpty && !confirmationEmpty &&
                                !nameInvalid && !emailInvalid && !passwordInvalid && !confirmationInvalid;
            
            registerBtn.disabled = !shouldEnable;
        }

        updateRegisterButton();
    });
    </script>
</x-guest-layout>