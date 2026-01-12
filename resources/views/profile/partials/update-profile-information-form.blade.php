<section>
    <div class="mb-4">
        <h4>Profile Information</h4>
        <p class="text-muted">Update your account's profile information and email address.</p>
    </div>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" id="profile-form">
        @csrf
        @method('patch')

        <div class="mb-3">
            <label for="name" class="form-label">Name</label>
            <input id="name" name="name" type="text" class="form-control" value="{{ old('name', $user->name) }}" required autofocus>
            <div id="name-feedback" class="small mt-1" style="display: none;"></div>
            @error('name') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input id="email" name="email" type="email" class="form-control" value="{{ old('email', $user->email) }}" required>
            <div id="email-feedback" class="small mt-1" style="display: none;"></div>
            @error('email') <div class="text-danger small mt-1">{{ $message }}</div> @enderror

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div class="alert alert-warning mt-2">
                    <p class="mb-1">Your email address is unverified.</p>
                    <button form="send-verification" class="btn btn-link p-0">
                        Click here to re-send the verification email.
                    </button>
                    
                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 text-success mb-0">
                            A new verification link has been sent to your email address.
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div class="d-flex align-items-center gap-3">
            <button type="submit" class="btn btn-primary" id="save-profile-btn">Save</button>

            @if (session('status') === 'profile-updated')
                <span class="text-success small">Saved!</span>
            @endif
        </div>
    </form>
</section>

@push('scripts')
<script nonce="{{ $cspNonce }}">
document.addEventListener('DOMContentLoaded', function() {
    const nameInput = document.getElementById('name');
    const nameFeedback = document.getElementById('name-feedback');
    const emailInput = document.getElementById('email');
    const emailFeedback = document.getElementById('email-feedback');
    const saveBtn = document.getElementById('save-profile-btn');
    const originalEmail = '{{ $user->email }}';

    nameInput.addEventListener('input', function() {
        const name = this.value.trim();
        
        nameFeedback.style.display = 'none';
        nameInput.classList.remove('is-invalid', 'is-valid');
        
        if (name.length === 0) {
            nameFeedback.textContent = 'Please enter your name.';
            nameFeedback.className = 'small mt-1 text-danger';
            nameFeedback.style.display = 'block';
            nameInput.classList.add('is-invalid');
        } else {
            nameFeedback.textContent = '✓ Name is valid';
            nameFeedback.className = 'small mt-1 text-success';
            nameFeedback.style.display = 'block';
            nameInput.classList.add('is-valid');
        }
        updateSaveButton();
    });

    emailInput.addEventListener('input', function() {
        const email = this.value.trim();
        
        emailFeedback.style.display = 'none';
        emailInput.classList.remove('is-invalid', 'is-valid');
        
        if (email.length === 0) {
            emailFeedback.textContent = 'Please enter your email address.';
            emailFeedback.className = 'small mt-1 text-danger';
            emailFeedback.style.display = 'block';
            emailInput.classList.add('is-invalid');
        } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
            emailFeedback.textContent = 'Please enter a valid email address.';
            emailFeedback.className = 'small mt-1 text-danger';
            emailFeedback.style.display = 'block';
            emailInput.classList.add('is-invalid');
        } else {
            if (email === originalEmail) {
                emailFeedback.textContent = '✓ Current email address';
                emailFeedback.className = 'small mt-1 text-info';
            } else {
                emailFeedback.textContent = '✓ Valid email format';
                emailFeedback.className = 'small mt-1 text-success';
            }
            emailFeedback.style.display = 'block';
            emailInput.classList.add('is-valid');
        }
        updateSaveButton();
    });

    function updateSaveButton() {
        const nameInvalid = nameInput.classList.contains('is-invalid');
        const emailInvalid = emailInput.classList.contains('is-invalid');
        saveBtn.disabled = nameInvalid || emailInvalid;
    }
});
</script>
@endpush