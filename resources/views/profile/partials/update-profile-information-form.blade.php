<section>
    <p class="text-muted mb-4">
        {{ __("Update your account's profile information and email address.") }}
    </p>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" enctype="multipart/form-data">
        @csrf
        @method('patch')

        <div class="mb-3">
            <label for="name" class="form-label">{{ __('Name') }}</label>
            <input id="name" 
                   name="name" 
                   type="text" 
                   class="form-control @error('name') is-invalid @enderror" 
                   value="{{ old('name', $user->name) }}" 
                   required 
                   autofocus 
                   autocomplete="name">
            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">{{ __('Email') }}</label>
            <input id="email" 
                   name="email" 
                   type="email" 
                   class="form-control @error('email') is-invalid @enderror" 
                   value="{{ old('email', $user->email) }}" 
                   required 
                   autocomplete="username">
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div class="alert alert-warning mt-2">
                    <small>
                        {{ __('Your email address is unverified.') }}
                        <button form="send-verification" class="btn btn-link btn-sm p-0 align-baseline text-decoration-underline">
                            {{ __('Click here to re-send the verification email.') }}
                        </button>
                    </small>

                    @if (session('status') === 'verification-link-sent')
                        <div class="mt-2">
                            <small class="text-success fw-medium">
                                {{ __('A new verification link has been sent to your email address.') }}
                            </small>
                        </div>
                    @endif
                </div>
            @endif
        </div>

        <!-- Profile Image -->
        <div class="mb-3">
            <label for="image" class="form-label">{{ __('Profile Image') }}</label>
            @if($user->image_url)
                <div class="mb-2">
                    <img src="{{ $user->image_url }}" 
                         alt="{{ __('Current profile image') }}" 
                         class="rounded-circle"
                         style="width: 80px; height: 80px; object-fit: cover;">
                    <div class="form-text text-muted">{{ __('Current profile image') }}</div>
                </div>
            @endif
            <input id="image" 
                   name="image" 
                   type="file" 
                   class="form-control @error('image') is-invalid @enderror" 
                   accept="image/*">
            @error('image')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            <div class="form-text">{{ __('Upload a new profile image (JPG, PNG, GIF - Max: 8MB)') }}</div>
        </div>

        <!-- Phone -->
        <div class="mb-3">
            <label for="phone" class="form-label">{{ __('Phone Number') }}</label>
            <input id="phone" 
                   name="phone" 
                   type="tel" 
                   class="form-control @error('phone') is-invalid @enderror" 
                   value="{{ old('phone', $user->phone) }}" 
                   autocomplete="tel">
            @error('phone')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            <div class="form-text">{{ __('Optional: Your contact phone number') }}</div>
        </div>

        <!-- City -->
        <div class="mb-3">
            <label for="city" class="form-label">{{ __('City') }}</label>
            <input id="city" 
                   name="city" 
                   type="text" 
                   class="form-control @error('city') is-invalid @enderror" 
                   value="{{ old('city', $user->city) }}" 
                   autocomplete="address-level2">
            @error('city')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            <div class="form-text">{{ __('Optional: Your city or location') }}</div>
        </div>

        <div class="d-flex align-items-center gap-3">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save me-1"></i>
                {{ __('Save Changes') }}
            </button>
        </div>
    </form>
</section>
