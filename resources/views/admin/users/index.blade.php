@extends('layouts.app')

@section('content')
<div class="container-fluid mt-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="fas fa-users me-2"></i>Manage Users</h2>
                @if(Auth::user()->hasPermission('users.create'))
                    <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i>
                        Add New User
                    </a>
                @endif
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>Profile</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Provider</th>
                                    <th>Role</th>
                                    <th>Phone</th>
                                    <th>City</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($users as $user)
                                    <tr>
                                        <td>
                                            @if($user->image_url)
                                                <img src="{{ $user->image_url }}" 
                                                     alt="Profile" 
                                                     class="rounded-circle" 
                                                     style="width: 40px; height: 40px; object-fit: cover;">
                                            @else
                                                <div class="bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center" 
                                                     style="width: 40px; height: 40px; font-size: 1rem; font-weight: bold;">
                                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                                </div>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="fw-bold">{{ $user->name }}</div>
                                            @if($user->is_protected)
                                                <small class="badge bg-warning text-dark">
                                                    <i class="fas fa-shield-alt me-1"></i>Protected
                                                </small>
                                            @endif
                                        </td>
                                        <td>{{ $user->email }}</td>
                                        <td>
                                            @if($user->auth_provider === 'Google')
                                                <span class="badge bg-danger">
                                                    <i class="fab fa-google me-1"></i>Google
                                                </span>
                                            @elseif($user->auth_provider === 'Apple')
                                                <span class="badge bg-dark">
                                                    <i class="fab fa-apple me-1"></i>Apple
                                                </span>
                                            @else
                                                <span class="badge bg-secondary">
                                                    <i class="fas fa-envelope me-1"></i>Email
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $user->role->name === 'superadmin' ? 'danger' : ($user->role->name === 'admin' ? 'warning' : 'primary') }}">
                                                {{ $user->role->display_name ?? 'No Role' }}
                                            </span>
                                        </td>
                                        <td>{{ $user->phone ?? '-' }}</td>
                                        <td>{{ $user->city ?? '-' }}</td>
                                        <td>
                                            @if($user->email_verified_at)
                                                <span class="badge bg-success">
                                                    <i class="fas fa-check me-1"></i>Verified
                                                </span>
                                                <br>
                                                <small class="text-muted">{{ $user->email_verified_at->format('M d, Y') }}</small>
                                            @else
                                                <span class="badge bg-warning">
                                                    <i class="fas fa-clock me-1"></i>Unverified
                                                </span>
                                            @endif
                                            
                                            @if(Auth::user()->hasPermission('users.edit') && (!$user->is_protected || auth()->user()->isSuperAdmin()))
                                                <br>
                                                <form method="POST" action="{{ route('admin.users.toggle-verification', $user) }}" class="d-inline mt-1">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" 
                                                            class="btn btn-xs {{ $user->email_verified_at ? 'btn-outline-warning' : 'btn-outline-success' }}"
                                                            style="font-size: 0.7rem; padding: 2px 6px;"
                                                            onclick="return confirm('{{ $user->email_verified_at ? 'Are you sure you want to unverify this user?' : 'Are you sure you want to verify this user?' }}')">
                                                        @if($user->email_verified_at)
                                                            <i class="fas fa-times me-1"></i>Unverify
                                                        @else
                                                            <i class="fas fa-check me-1"></i>Verify
                                                        @endif
                                                    </button>
                                                </form>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group" aria-label="User actions">
                                                @if(Auth::user()->hasPermission('users.view'))
                                                    <a href="{{ route('admin.users.show', $user) }}" 
                                                       class="btn btn-outline-info btn-sm"
                                                       data-bs-toggle="tooltip" 
                                                       title="View user details">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                @endif
                                                
                                                @if(Auth::user()->hasPermission('users.edit'))
                                                    <a href="{{ route('admin.users.edit', $user) }}" 
                                                       class="btn btn-outline-primary btn-sm"
                                                       data-bs-toggle="tooltip" 
                                                       title="Edit user">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                @endif
                                                
                                                @if(Auth::user()->hasPermission('users.delete') && $user->canBeDeleted())
                                                    <button type="button" 
                                                            class="btn btn-outline-danger btn-sm" 
                                                            data-bs-toggle="modal" 
                                                            data-bs-target="#deleteModal{{ $user->id }}"
                                                            title="Delete user">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>

                                    @if(Auth::user()->hasPermission('users.delete') && $user->canBeDeleted())
                                        <!-- Delete Confirmation Modal -->
                                        <div class="modal fade" id="deleteModal{{ $user->id }}" tabindex="-1" aria-labelledby="deleteModalLabel{{ $user->id }}" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header bg-danger text-white">
                                                        <h5 class="modal-title" id="deleteModalLabel{{ $user->id }}">
                                                            <i class="fas fa-exclamation-triangle me-2"></i>
                                                            Confirm User Deletion
                                                        </h5>
                                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="d-flex align-items-start">
                                                            @if($user->image_url)
                                                                <img src="{{ $user->image_url }}" 
                                                                     alt="Profile" 
                                                                     class="me-3" 
                                                                     style="width: 60px; height: 60px; object-fit: cover; border-radius: 50%;">
                                                            @else
                                                                <div class="bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center me-3" 
                                                                     style="width: 60px; height: 60px; font-size: 1.5rem; font-weight: bold;">
                                                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                                                </div>
                                                            @endif
                                                            <div>
                                                                <h6 class="mb-2">{{ $user->name }}</h6>
                                                                <p class="mb-2 text-muted">{{ $user->email }}</p>
                                                                <p class="mb-0">
                                                                    <strong>Are you sure you want to delete this user?</strong><br>
                                                                    <small class="text-danger">This action cannot be undone and will permanently delete the user account and associated data.</small>
                                                                </p>
                                                            </div>
                                                        </div>
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
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center py-4">
                                            <i class="fas fa-users fa-3x text-muted mb-3"></i>
                                            <p class="text-muted">No users found.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($users->hasPages())
                        <div class="d-flex justify-content-center mt-4">
                            {{ $users->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
