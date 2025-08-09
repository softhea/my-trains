@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-info text-white">
                    <h4 class="mb-0">
                        <i class="fas fa-user me-2"></i>
                        User Details: {{ $user->name }}
                        @if($user->is_protected)
                            <span class="badge bg-warning text-dark ms-2">
                                <i class="fas fa-shield-alt me-1"></i>Protected
                            </span>
                        @endif
                    </h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Profile Section -->
                        <div class="col-md-4 text-center mb-4">
                            @if($user->image_url)
                                <img src="{{ $user->image_url }}" 
                                     alt="Profile" 
                                     class="rounded-circle border border-3 border-light shadow" 
                                     style="width: 150px; height: 150px; object-fit: cover;">
                            @else
                                <div class="bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center mx-auto border border-3 border-light shadow" 
                                     style="width: 150px; height: 150px; font-size: 3rem; font-weight: bold;">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                            @endif
                            
                            <h5 class="mt-3 mb-1">{{ $user->name }}</h5>
                            <span class="badge bg-{{ $user->role->name === 'superadmin' ? 'danger' : ($user->role->name === 'admin' ? 'warning' : 'primary') }} fs-6">
                                {{ $user->role->display_name ?? 'No Role' }}
                            </span>
                            
                            @if($user->email_verified_at)
                                <div class="mt-2">
                                    <span class="badge bg-success">
                                        <i class="fas fa-check me-1"></i>Email Verified
                                    </span>
                                </div>
                            @else
                                <div class="mt-2">
                                    <span class="badge bg-secondary">
                                        <i class="fas fa-clock me-1"></i>Email Pending
                                    </span>
                                </div>
                            @endif
                        </div>

                        <!-- Details Section -->
                        <div class="col-md-8">
                            <h6 class="text-muted mb-3">Contact Information</h6>
                            
                            <div class="row mb-3">
                                <div class="col-sm-4">
                                    <strong><i class="fas fa-envelope me-2 text-muted"></i>Email:</strong>
                                </div>
                                <div class="col-sm-8">
                                    <a href="mailto:{{ $user->email }}" class="text-decoration-none">
                                        {{ $user->email }}
                                    </a>
                                </div>
                            </div>

                            @if($user->phone)
                                <div class="row mb-3">
                                    <div class="col-sm-4">
                                        <strong><i class="fas fa-phone me-2 text-muted"></i>Phone:</strong>
                                    </div>
                                    <div class="col-sm-8">
                                        <a href="tel:{{ $user->phone }}" class="text-decoration-none">
                                            {{ $user->phone }}
                                        </a>
                                    </div>
                                </div>
                            @endif

                            @if($user->city)
                                <div class="row mb-3">
                                    <div class="col-sm-4">
                                        <strong><i class="fas fa-map-marker-alt me-2 text-muted"></i>City:</strong>
                                    </div>
                                    <div class="col-sm-8">
                                        {{ $user->city }}
                                    </div>
                                </div>
                            @endif

                            <hr>

                            <h6 class="text-muted mb-3">Account Information</h6>

                            <div class="row mb-3">
                                <div class="col-sm-4">
                                    <strong><i class="fas fa-calendar-plus me-2 text-muted"></i>Created:</strong>
                                </div>
                                <div class="col-sm-8">
                                    {{ $user->created_at->format('F j, Y \a\t g:i A') }}
                                    <small class="text-muted">({{ $user->created_at->diffForHumans() }})</small>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-sm-4">
                                    <strong><i class="fas fa-calendar-check me-2 text-muted"></i>Last Updated:</strong>
                                </div>
                                <div class="col-sm-8">
                                    {{ $user->updated_at->format('F j, Y \a\t g:i A') }}
                                    <small class="text-muted">({{ $user->updated_at->diffForHumans() }})</small>
                                </div>
                            </div>

                            @if($user->email_verified_at)
                                <div class="row mb-3">
                                    <div class="col-sm-4">
                                        <strong><i class="fas fa-check-circle me-2 text-muted"></i>Email Verified:</strong>
                                    </div>
                                    <div class="col-sm-8">
                                        {{ $user->email_verified_at->format('F j, Y \a\t g:i A') }}
                                        <small class="text-muted">({{ $user->email_verified_at->diffForHumans() }})</small>
                                    </div>
                                </div>
                            @endif

                            @if($user->role)
                                <hr>
                                <h6 class="text-muted mb-3">Role & Permissions</h6>
                                
                                <div class="row mb-3">
                                    <div class="col-sm-4">
                                        <strong><i class="fas fa-user-tag me-2 text-muted"></i>Role:</strong>
                                    </div>
                                    <div class="col-sm-8">
                                        <span class="badge bg-{{ $user->role->name === 'superadmin' ? 'danger' : ($user->role->name === 'admin' ? 'warning' : 'primary') }}">
                                            {{ $user->role->display_name }}
                                        </span>
                                        <br>
                                        <small class="text-muted">{{ $user->role->description }}</small>
                                    </div>
                                </div>

                                @if($user->role->permissions->count() > 0)
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <strong><i class="fas fa-key me-2 text-muted"></i>Permissions:</strong>
                                        </div>
                                        <div class="col-sm-8">
                                            <div class="d-flex flex-wrap gap-1">
                                                @foreach($user->role->permissions as $permission)
                                                    <span class="badge bg-light text-dark border">
                                                        {{ $permission->display_name }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @endif
                        </div>
                    </div>

                    <hr>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-1"></i>
                            Back to Users
                        </a>
                        
                        <div class="btn-group">
                            @if(Auth::user()->hasPermission('users.edit'))
                                <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-warning">
                                    <i class="fas fa-edit me-1"></i>
                                    Edit User
                                </a>
                            @endif
                            
                            @if(Auth::user()->hasPermission('users.delete') && $user->canBeDeleted())
                                <button type="button" 
                                        class="btn btn-danger" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#deleteModal">
                                    <i class="fas fa-trash me-1"></i>
                                    Delete User
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@if(Auth::user()->hasPermission('users.delete') && $user->canBeDeleted())
    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="deleteModalLabel">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Confirm User Deletion
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p><strong>Are you sure you want to delete this user?</strong></p>
                    <p class="text-danger">This action cannot be undone and will permanently delete the user account and associated data.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>
                        Cancel
                    </button>
                    <form method="POST" action="{{ route('admin.users.destroy', $user) }}" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash me-1"></i>
                            Delete User
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endif
@endsection
