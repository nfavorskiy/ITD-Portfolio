<section>
    <div class="mb-4">
        <h4>Update Password</h4>
        <p class="text-muted">Ensure your account is using a long, random password to stay secure.</p>
    </div>

    <form method="post" action="{{ route('password.update') }}" id="password-form">
        @csrf
        @method('put')

        <div class="mb-3">
            <label for="update_password_current_password" class="form-label">Current Password</label>
            <input id="update_password_current_password" name="current_password" type="password" class="form-control" autocomplete="current-password" required>
            <div id="current-password-feedback" class="small mt-1" style="display: none;"></div>
            @error('current_password', 'updatePassword') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
        </div>

        <div class="mb-3">
            <label for="update_password_password" class="form-label">New Password</label>
            <input id="update_password_password" name="password" type="password" class="form-control" autocomplete="new-password" required>
            
            <div id="password-requirements" class="small mt-2" style="display: none;">
                <div class="mb-1 text-muted">Password must contain:</div>
                <ul class="list-unstyled">
                    <li id="req-length" class="text-danger">
                        <span class="req-icon">✗</span> At least 8 characters
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
            
            @error('password', 'updatePassword') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
        </div>

        <div class="mb-3">
            <label for="update_password_password_confirmation" class="form-label">Confirm Password</label>
            <input id="update_password_password_confirmation" name="password_confirmation" type="password" class="form-control" autocomplete="new-password" required>
            <div id="password-confirmation-feedback" class="small mt-1" style="display: none;"></div>
            @error('password_confirmation', 'updatePassword') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
        </div>

        <div class="d-flex align-items-center gap-3">
            <button type="submit" class="btn btn-primary" id="save-password-btn">Save</button>

            @if (session('status') === 'password-updated')
                <span class="text-success small">Saved!</span>
            @endif
        </div>
    </form>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const currentPasswordInput = document.getElementById('update_password_current_password');
        const currentPasswordFeedback = document.getElementById('current-password-feedback');
        const passwordInput = document.getElementById('update_password_password');
        const passwordRequirements = document.getElementById('password-requirements');
        const confirmationInput = document.getElementById('update_password_password_confirmation');
        const confirmationFeedback = document.getElementById('password-confirmation-feedback');
        const saveBtn = document.getElementById('save-password-btn');

        currentPasswordInput.addEventListener('input', function() {
            const password = this.value;
            
            currentPasswordFeedback.style.display = 'none';
            currentPasswordInput.classList.remove('is-invalid', 'is-valid');
            
            if (password.length === 0) {
                currentPasswordFeedback.textContent = 'Please enter your current password.';
                currentPasswordFeedback.className = 'small mt-1 text-danger';
                currentPasswordFeedback.style.display = 'block';
                currentPasswordInput.classList.add('is-invalid');
            } else {
                currentPasswordFeedback.textContent = '✓ Current password entered';
                currentPasswordFeedback.className = 'small mt-1 text-success';
                currentPasswordFeedback.style.display = 'block';
                currentPasswordInput.classList.add('is-valid');
            }
            updateSaveButton();
        });

        passwordInput.addEventListener('focus', function() {
            passwordRequirements.style.display = 'block';
        });

        passwordInput.addEventListener('input', function() {
            validatePassword();
            validatePasswordConfirmation();
        });

        confirmationInput.addEventListener('input', function() {
            validatePasswordConfirmation();
        });

        function validatePassword() {
            const password = passwordInput.value;
            
            if (password.length > 0) {
                passwordRequirements.style.display = 'block';
            }
            
            passwordInput.classList.remove('is-invalid', 'is-valid');
            
            let allValid = true;
            
            const lengthValid = password.length >= 8;
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
            
            updateSaveButton();
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
            const confirmation = confirmationInput.value;
            
            confirmationFeedback.style.display = 'none';
            confirmationInput.classList.remove('is-invalid', 'is-valid');
            
            if (confirmation.length === 0) {
                updateSaveButton();
                return;
            }
            
            if (password !== confirmation) {
                confirmationFeedback.textContent = 'Passwords do not match.';
                confirmationFeedback.className = 'small mt-1 text-danger';
                confirmationFeedback.style.display = 'block';
                confirmationInput.classList.add('is-invalid');
            } else {
                confirmationFeedback.textContent = '✓ Passwords match!';
                confirmationFeedback.className = 'small mt-1 text-success';
                confirmationFeedback.style.display = 'block';
                confirmationInput.classList.add('is-valid');
            }
            
            updateSaveButton();
        }

        function updateSaveButton() {
            const currentInvalid = currentPasswordInput.classList.contains('is-invalid');
            const passwordInvalid = passwordInput.classList.contains('is-invalid');
            const confirmationInvalid = confirmationInput.classList.contains('is-invalid');
            
            saveBtn.disabled = currentInvalid || passwordInvalid || confirmationInvalid;
        }
    });
    </script>
</section>