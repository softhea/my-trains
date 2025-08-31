@extends('layouts.app')

@section('title', __('Profile Settings'))

@section('content')
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Page Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h2 mb-0">
                    <i class="fas fa-user-edit me-2 text-primary"></i>
                    {{ __('Profile Settings') }}
                </h1>
                @if($user->image_url)
                    <img src="{{ $user->image_url }}" 
                         alt="{{ __('Profile') }}" 
                         class="rounded-circle border" 
                         style="width: 60px; height: 60px; object-fit: cover;">
                @endif
            </div>

            <!-- Current Profile Summary -->
            <div class="card mb-4 bg-light">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            @if($user->image_url)
                                <img src="{{ $user->image_url }}" 
                                     alt="{{ __('Profile') }}" 
                                     class="rounded-circle border" 
                                     style="width: 80px; height: 80px; object-fit: cover;">
                            @else
                                <div class="bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center" 
                                     style="width: 80px; height: 80px; font-size: 2rem;">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                            @endif
                        </div>
                        <div class="col">
                            <h4 class="mb-1">{{ $user->name }}</h4>
                            <p class="text-muted mb-1">{{ $user->email }}</p>
                            @if($user->phone || $user->city)
                                <div class="d-flex gap-3 text-muted small">
                                    @if($user->phone)
                                        <span><i class="fas fa-phone me-1"></i>{{ $user->phone }}</span>
                                    @endif
                                    @if($user->city)
                                        <span><i class="fas fa-map-marker-alt me-1"></i>{{ $user->city }}</span>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Success Message -->
            @if (session('status') === 'profile-updated')
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    {{ __('Your profile has been updated successfully!') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- Update Profile Information -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-user me-2"></i>
                        {{ __('Profile Information') }}
                    </h5>
                </div>
                <div class="card-body">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <!-- Update Password -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-lock me-2"></i>
                        {{ __('Update Password') }}
                    </h5>
                </div>
                <div class="card-body">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <!-- Delete Account -->
            <div class="card border-danger mb-4">
                <div class="card-header bg-danger text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Danger Zone
                    </h5>
                </div>
                <div class="card-body">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
