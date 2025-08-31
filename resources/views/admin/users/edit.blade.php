@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-warning text-dark">
                    <h4 class="mb-0">
                        <i class="fas fa-user-edit me-2"></i>
                        Edit User: {{ $user->name }}
                        @if($user->is_protected)
                            <span class="badge bg-danger ms-2">
                                <i class="fas fa-shield-alt me-1"></i>Protected
                            </span>
                        @endif
                    </h4>
                </div>
                <div class="card-body">
                    <!-- Current User Summary -->
                    <div class="alert alert-info mb-4">
                        <div class="d-flex align-items-center">
                            @if($user->image_url)
                                <img src="{{ $user->image_url }}" 
                                     alt="Profile" 
                                     class="rounded-circle me-3" 
                                     style="width: 60px; height: 60px; object-fit: cover;">
                            @else
                                <div class="bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center me-3" 
                                     style="width: 60px; height: 60px; font-size: 1.5rem; font-weight: bold;">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                            @endif
                            <div>
                                <h6 class="mb-1">{{ $user->name }}</h6>
                                <small class="text-muted">{{ $user->email }}</small>
                                <br>
                                <span class="badge bg-{{ $user->role->name === 'superadmin' ? 'danger' : ($user->role->name === 'admin' ? 'warning' : 'primary') }}">
                                    {{ $user->role->display_name ?? 'No Role' }}
                                </span>
                                
                                @if($user->auth_provider === 'Google')
                                    <span class="badge bg-danger ms-1">
                                        <i class="fab fa-google me-1"></i>Google
                                    </span>
                                @elseif($user->auth_provider === 'Apple')
                                    <span class="badge bg-dark ms-1">
                                        <i class="fab fa-apple me-1"></i>Apple
                                    </span>
                                @else
                                    <span class="badge bg-secondary ms-1">
                                        <i class="fas fa-envelope me-1"></i>Email
                                    </span>
                                @endif
                                
                                @if($user->city)
                                    <br><small class="text-muted"><i class="fas fa-map-marker-alt me-1"></i>{{ $user->city }}</small>
                                @endif
                                <br>
                                @if($user->email_verified_at)
                                    <small class="badge bg-success mt-1">
                                        <i class="fas fa-check me-1"></i>Email Verified
                                    </small>
                                    <small class="text-muted">{{ $user->email_verified_at->format('M d, Y') }}</small>
                                @else
                                    <small class="badge bg-warning mt-1">
                                        <i class="fas fa-clock me-1"></i>Email Unverified
                                    </small>
                                @endif
                            </div>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('admin.users.update', $user) }}">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label">
                                        <i class="fas fa-user me-1"></i>
                                        Full Name
                                    </label>
                                    <input type="text" 
                                           class="form-control @error('name') is-invalid @enderror" 
                                           id="name" 
                                           name="name" 
                                           value="{{ old('name', $user->name) }}" 
                                           required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="email" class="form-label">
                                        <i class="fas fa-envelope me-1"></i>
                                        Email Address
                                    </label>
                                    <input type="email" 
                                           class="form-control @error('email') is-invalid @enderror" 
                                           id="email" 
                                           name="email" 
                                           value="{{ old('email', $user->email) }}" 
                                           required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="password" class="form-label">
                                        <i class="fas fa-lock me-1"></i>
                                        New Password
                                    </label>
                                    <input type="password" 
                                           class="form-control @error('password') is-invalid @enderror" 
                                           id="password" 
                                           name="password">
                                    <div class="form-text">Leave blank to keep current password</div>
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="password_confirmation" class="form-label">
                                        <i class="fas fa-lock me-1"></i>
                                        Confirm New Password
                                    </label>
                                    <input type="password" 
                                           class="form-control" 
                                           id="password_confirmation" 
                                           name="password_confirmation">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="phone" class="form-label">
                                        <i class="fas fa-phone me-1"></i>
                                        Phone Number
                                    </label>
                                    <input type="text" 
                                           class="form-control @error('phone') is-invalid @enderror" 
                                           id="phone" 
                                           name="phone" 
                                           value="{{ old('phone', $user->phone) }}">
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="city" class="form-label">
                                        <i class="fas fa-map-marker-alt me-1"></i>
                                        City
                                    </label>
                                    <input type="text" 
                                           class="form-control @error('city') is-invalid @enderror" 
                                           id="city" 
                                           name="city" 
                                           value="{{ old('city', $user->city) }}">
                                    @error('city')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="role_id" class="form-label">
                                <i class="fas fa-user-tag me-1"></i>
                                Role
                            </label>
                            <select class="form-select @error('role_id') is-invalid @enderror" 
                                    id="role_id" 
                                    name="role_id" 
                                    required
                                    @if($user->is_protected && !auth()->user()->isSuperAdmin()) disabled @endif>
                                @foreach($roles as $role)
                                    <option value="{{ $role->id }}" 
                                            {{ old('role_id', $user->role_id) == $role->id ? 'selected' : '' }}>
                                        {{ $role->display_name }}
                                        @if(!$role->is_deletable)
                                            <small>(System Role)</small>
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                            @if($user->is_protected && !auth()->user()->isSuperAdmin())
                                <div class="form-text text-warning">
                                    <i class="fas fa-exclamation-triangle me-1"></i>
                                    Role cannot be changed for protected users.
                                </div>
                            @endif
                            @error('role_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Email Verification Section -->
                        @if(Auth::user()->hasPermission('users.edit') && (!$user->is_protected || auth()->user()->isSuperAdmin()))
                            <div class="mb-3">
                                <label class="form-label">
                                    <i class="fas fa-envelope-open-text me-1"></i>
                                    Email Verification Status
                                </label>
                                <div class="card">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                @if($user->email_verified_at)
                                                    <span class="badge bg-success me-2">
                                                        <i class="fas fa-check me-1"></i>Verified
                                                    </span>
                                                    <small class="text-muted">
                                                        Verified on {{ $user->email_verified_at->format('M d, Y \a\t g:i A') }}
                                                    </small>
                                                @else
                                                    <span class="badge bg-warning me-2">
                                                        <i class="fas fa-clock me-1"></i>Unverified
                                                    </span>
                                                    <small class="text-danger">
                                                        This user cannot add products or place orders until verified.
                                                    </small>
                                                @endif
                                            </div>
                                            <form method="POST" action="{{ route('admin.users.toggle-verification', $user) }}" class="d-inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" 
                                                        class="btn btn-sm {{ $user->email_verified_at ? 'btn-outline-warning' : 'btn-outline-success' }}"
                                                        onclick="return confirm('{{ $user->email_verified_at ? 'Are you sure you want to mark this user as unverified?' : 'Are you sure you want to mark this user as verified?' }}')">
                                                    @if($user->email_verified_at)
                                                        <i class="fas fa-times me-1"></i>Mark as Unverified
                                                    @else
                                                        <i class="fas fa-check me-1"></i>Mark as Verified
                                                    @endif
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-1"></i>
                                Back to Users
                            </a>
                            <button type="submit" class="btn btn-warning">
                                <i class="fas fa-save me-1"></i>
                                Update User
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
