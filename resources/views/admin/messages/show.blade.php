@extends('layouts.app')

@section('title', __('Message Details'))

@section('content')
<div class="container py-5">
  <div class="row">
    <div class="col-md-12">
      <!-- Header -->
      <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>{{ __('Message Details') }}</h1>
        <a href="{{ route('admin.messages.index') }}" class="btn btn-outline-secondary">
          <i class="fas fa-arrow-left me-1"></i>{{ __('Back to Messages') }}
        </a>
      </div>

      <!-- Message Info Card -->
      <div class="card mb-4">
        <div class="card-header">
          <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0">{{ __('Message Information') }}</h5>
            <div class="d-flex gap-2">
              <form method="POST" action="{{ route('admin.messages.toggle-read', $message) }}" class="d-inline">
                @csrf
                @method('PATCH')
                <button type="submit" class="btn btn-sm btn-outline-{{ $message->isUnread() ? 'success' : 'warning' }}">
                  <i class="fas fa-envelope{{ $message->isUnread() ? '-open' : '' }} me-1"></i>
                  {{ $message->isUnread() ? __('Mark as Read') : __('Mark as Unread') }}
                </button>
              </form>
              <button type="button" class="btn btn-sm btn-outline-danger" onclick="confirmDelete({{ $message->id }})">
                <i class="fas fa-trash me-1"></i>{{ __('Delete') }}
              </button>
            </div>
          </div>
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-md-6">
              <h6>{{ __('From') }}:</h6>
              <div class="d-flex align-items-center mb-3">
                <div class="bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center me-3" 
                     style="width: 40px; height: 40px; font-size: 1rem; font-weight: bold;">
                  {{ strtoupper(substr($message->sender->name, 0, 1)) }}
                </div>
                <div>
                  <strong>{{ $message->sender->name }}</strong>
                  <br>
                  <small class="text-muted">{{ $message->sender->email }}</small>
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <h6>{{ __('To') }}:</h6>
              <div class="d-flex align-items-center mb-3">
                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" 
                     style="width: 40px; height: 40px; font-size: 1rem; font-weight: bold;">
                  {{ strtoupper(substr($message->receiver->name, 0, 1)) }}
                </div>
                <div>
                  <strong>{{ $message->receiver->name }}</strong>
                  <br>
                  <small class="text-muted">{{ $message->receiver->email }}</small>
                </div>
              </div>
            </div>
          </div>

          @if($message->product)
            <div class="row">
              <div class="col-12">
                <h6>{{ __('Related Product') }}:</h6>
                <div class="card bg-light">
                  <div class="card-body py-2">
                    <div class="d-flex align-items-center">
                      @if($message->product->images->count() > 0)
                        <img src="{{ $message->product->images->first()->url }}" 
                             class="rounded me-3" 
                             style="width: 50px; height: 50px; object-fit: cover;">
                      @endif
                      <div>
                        <strong>{{ $message->product->name }}</strong>
                        <br>
                        <small class="text-muted">{{ $message->product->formatted_price }}</small>
                      </div>
                      <div class="ms-auto">
                        <a href="{{ route('products.show', $message->product) }}" 
                           class="btn btn-sm btn-outline-primary" target="_blank">
                          <i class="fas fa-external-link-alt me-1"></i>{{ __('View Product') }}
                        </a>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          @endif

          <div class="row mt-3">
            <div class="col-md-6">
              <small class="text-muted">
                <strong>{{ __('Sent') }}:</strong> {{ $message->created_at->format('M d, Y \a\t g:i A') }}
              </small>
            </div>
            <div class="col-md-6">
              <small class="text-muted">
                <strong>{{ __('Status') }}:</strong> 
                @if($message->isUnread())
                  <span class="badge bg-warning">{{ __('Unread') }}</span>
                @else
                  <span class="badge bg-success">{{ __('Read') }} {{ $message->read_at->format('M d, Y \a\t g:i A') }}</span>
                @endif
              </small>
            </div>
          </div>
        </div>
      </div>

      <!-- Conversation -->
      <div class="card">
        <div class="card-header">
          <h5 class="mb-0">{{ __('Full Conversation') }}</h5>
        </div>
        <div class="card-body" style="max-height: 600px; overflow-y: auto;">
          @if($conversation->count() > 0)
            @foreach($conversation as $msg)
              <div class="d-flex mb-4 {{ $msg->id === $message->id ? 'border rounded p-2 bg-light' : '' }}">
                <div class="flex-shrink-0 me-3">
                  <div class="bg-{{ $msg->sender_id === $message->sender_id ? 'secondary' : 'primary' }} text-white rounded-circle d-flex align-items-center justify-content-center" 
                       style="width: 40px; height: 40px; font-size: 1rem; font-weight: bold;">
                    {{ strtoupper(substr($msg->sender->name, 0, 1)) }}
                  </div>
                </div>
                <div class="flex-grow-1">
                  <div class="d-flex justify-content-between align-items-start mb-2">
                    <h6 class="mb-0">{{ $msg->sender->name }}</h6>
                    <small class="text-muted">{{ $msg->created_at->format('M d, Y g:i A') }}</small>
                  </div>
                  
                  @if($msg->product && $msg->id === $message->id)
                    <div class="mb-2 pb-2 border-bottom">
                      <small class="text-info">
                        <i class="fas fa-box me-1"></i>{{ __('About:') }} {{ $msg->product->name }}
                      </small>
                    </div>
                  @endif
                  
                  <div class="fw-bold mb-1">{{ $msg->subject }}</div>
                  <div class="mb-2" style="white-space: pre-wrap;">{{ $msg->message }}</div>
                  
                  @if($msg->isUnread() && $msg->id !== $message->id)
                    <small class="badge bg-warning">{{ __('Unread') }}</small>
                  @endif
                </div>
              </div>
              @if(!$loop->last)
                <hr class="my-3">
              @endif
            @endforeach
          @else
            <div class="text-center py-4">
              <i class="fas fa-comments fa-3x text-muted mb-3"></i>
              <p class="text-muted">{{ __('No conversation history found.') }}</p>
            </div>
          @endif
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">{{ __('Confirm Delete') }}</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <p>{{ __('Are you sure you want to delete this message? This action cannot be undone.') }}</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
        <form id="deleteForm" method="POST" style="display: inline;">
          @csrf
          @method('DELETE')
          <button type="submit" class="btn btn-danger">{{ __('Delete') }}</button>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
function confirmDelete(messageId) {
  const form = document.getElementById('deleteForm');
  form.action = `/admin/messages/${messageId}`;
  const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
  modal.show();
}
</script>
@endsection
