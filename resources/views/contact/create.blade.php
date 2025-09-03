@extends('layouts.app')

@section('title', __('Contact Us'))

@section('content')
<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-md-8">
      <div class="card shadow-sm">
        <div class="card-header bg-info text-white">
          <h3 class="mb-0">
            <i class="fas fa-envelope me-2"></i>{{ __('Contact Us') }}
          </h3>
        </div>
        <div class="card-body">
          <p class="text-muted mb-4">
            {{ __('Have a question, suggestion, or need help? Send us a message and our team will get back to you as soon as possible.') }}
          </p>

          @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
              <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
              <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
          @endif

          @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
              <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
              <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
          @endif

          @if ($errors->any())
            <div class="alert alert-danger">
              <ul class="mb-0">
                @foreach ($errors->all() as $error)
                  <li>{{ $error }}</li>
                @endforeach
              </ul>
            </div>
          @endif

          <form method="POST" action="{{ route('contact.store') }}">
            @csrf

            <div class="row">
              <div class="col-md-6">
                <div class="mb-3">
                  <label for="name" class="form-label">
                    <i class="fas fa-user me-1"></i>{{ __('Your Name') }} <span class="text-danger">*</span>
                  </label>
                  <input type="text" 
                         class="form-control @error('name') is-invalid @enderror" 
                         id="name" 
                         name="name" 
                         value="{{ old('name', auth()->user()->name ?? '') }}" 
                         required
                         placeholder="{{ __('Enter your full name') }}">
                  @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
              </div>

              <div class="col-md-6">
                <div class="mb-3">
                  <label for="email" class="form-label">
                    <i class="fas fa-envelope me-1"></i>{{ __('Your Email') }} <span class="text-danger">*</span>
                  </label>
                  <input type="email" 
                         class="form-control @error('email') is-invalid @enderror" 
                         id="email" 
                         name="email" 
                         value="{{ old('email', auth()->user()->email ?? '') }}" 
                         required
                         placeholder="{{ __('Enter your email address') }}">
                  @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
              </div>
            </div>

            <div class="mb-3">
              <label for="subject" class="form-label">
                <i class="fas fa-tag me-1"></i>{{ __('Subject') }} <span class="text-danger">*</span>
              </label>
              <input type="text" 
                     class="form-control @error('subject') is-invalid @enderror" 
                     id="subject" 
                     name="subject" 
                     value="{{ old('subject') }}" 
                     required
                     placeholder="{{ __('What is your message about?') }}">
              @error('subject')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div class="mb-3">
              <label for="message" class="form-label">
                <i class="fas fa-comment me-1"></i>{{ __('Message') }} <span class="text-danger">*</span>
              </label>
              <textarea class="form-control @error('message') is-invalid @enderror" 
                        id="message" 
                        name="message" 
                        rows="6" 
                        required
                        placeholder="{{ __('Please describe your question, issue, or feedback in detail...') }}">{{ old('message') }}</textarea>
              @error('message')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
              <div class="form-text">{{ __('Maximum 2000 characters') }}</div>
            </div>

            <!-- CAPTCHA -->
            <div class="mb-4">
              <label for="captcha" class="form-label">
                <i class="fas fa-shield-alt me-1"></i>{{ __('Security Check') }} <span class="text-danger">*</span>
              </label>
              <div class="mt-2">
                {!! NoCaptcha::renderJs() !!}
                {!! NoCaptcha::display() !!}
                @error('g-recaptcha-response')
                  <div class="text-danger mt-2">{{ $message }}</div>
                @enderror
              </div>
            </div>

            <div class="d-flex justify-content-between align-items-center">
              <a href="{{ route('home') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i>{{ __('Back to Home') }}
              </a>
              <button type="submit" class="btn btn-info">
                <i class="fas fa-paper-plane me-1"></i>{{ __('Send Message') }}
              </button>
            </div>
          </form>
        </div>

        <div class="card-footer bg-light">
          <div class="row text-center">
            <div class="col-md-12">
              <small class="text-muted">
                <i class="fas fa-info-circle me-1"></i>
                {{ __('Your message will be sent directly to our administrators. We typically respond within 24 hours.') }}
              </small>
            </div>
          </div>
        </div>
      </div>

      <!-- Additional Info Card -->
      <!-- <div class="card mt-4">
        <div class="card-body">
          <div class="row text-center">
            <div class="col-md-4">
              <div class="mb-3">
                <i class="fas fa-clock fa-2x text-info mb-2"></i>
                <h5>{{ __('Quick Response') }}</h5>
                <p class="text-muted small">{{ __('We respond to most messages within 24 hours') }}</p>
              </div>
            </div>
            <div class="col-md-4">
              <div class="mb-3">
                <i class="fas fa-users fa-2x text-info mb-2"></i>
                <h5>{{ __('Expert Team') }}</h5>
                <p class="text-muted small">{{ __('Our experienced team is here to help you') }}</p>
              </div>
            </div>
            <div class="col-md-4">
              <div class="mb-3">
                <i class="fas fa-shield-alt fa-2x text-info mb-2"></i>
                <h5>{{ __('Secure & Private') }}</h5>
                <p class="text-muted small">{{ __('Your information is protected and confidential') }}</p>
              </div>
            </div>
          </div>
        </div>
      </div> -->
    </div>
  </div>
</div>
@endsection
