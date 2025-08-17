@extends('layouts.app')

@section('title', __('Manage Messages'))

@section('content')
<div class="container-fluid py-4">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h1>{{ __('Manage Messages') }}</h1>
  </div>

  <!-- Statistics Cards -->
  <div class="row mb-4">
    <div class="col-md-3">
      <div class="card bg-primary text-white">
        <div class="card-body">
          <div class="d-flex justify-content-between">
            <div>
              <h6 class="card-title">{{ __('Total Messages') }}</h6>
              <h3 class="mb-0">{{ $stats['total'] }}</h3>
            </div>
            <div class="align-self-center">
              <i class="fas fa-envelope fa-2x"></i>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card bg-warning text-white">
        <div class="card-body">
          <div class="d-flex justify-content-between">
            <div>
              <h6 class="card-title">{{ __('Unread Messages') }}</h6>
              <h3 class="mb-0">{{ $stats['unread'] }}</h3>
            </div>
            <div class="align-self-center">
              <i class="fas fa-envelope-open fa-2x"></i>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card bg-info text-white">
        <div class="card-body">
          <div class="d-flex justify-content-between">
            <div>
              <h6 class="card-title">{{ __('Today') }}</h6>
              <h3 class="mb-0">{{ $stats['today'] }}</h3>
            </div>
            <div class="align-self-center">
              <i class="fas fa-calendar-day fa-2x"></i>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card bg-success text-white">
        <div class="card-body">
          <div class="d-flex justify-content-between">
            <div>
              <h6 class="card-title">{{ __('This Week') }}</h6>
              <h3 class="mb-0">{{ $stats['this_week'] }}</h3>
            </div>
            <div class="align-self-center">
              <i class="fas fa-calendar-week fa-2x"></i>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Filters -->
  <div class="card mb-4">
    <div class="card-body">
      <form method="GET" class="row g-3">
        <div class="col-md-4">
          <label for="search" class="form-label">{{ __('Search') }}</label>
          <input type="text" class="form-control" id="search" name="search" 
                 value="{{ request('search') }}" placeholder="{{ __('Search messages, users...') }}">
        </div>
        <div class="col-md-3">
          <label for="status" class="form-label">{{ __('Status') }}</label>
          <select class="form-select" id="status" name="status">
            <option value="">{{ __('All Messages') }}</option>
            <option value="unread" {{ request('status') === 'unread' ? 'selected' : '' }}>{{ __('Unread') }}</option>
            <option value="read" {{ request('status') === 'read' ? 'selected' : '' }}>{{ __('Read') }}</option>
          </select>
        </div>
        <div class="col-md-3">
          <label for="user" class="form-label">{{ __('User') }}</label>
          <select class="form-select" id="user" name="user">
            <option value="">{{ __('All Users') }}</option>
            @foreach($users as $user)
              <option value="{{ $user->id }}" {{ request('user') == $user->id ? 'selected' : '' }}>
                {{ $user->name }}
              </option>
            @endforeach
          </select>
        </div>
        <div class="col-md-2 d-flex align-items-end">
          <button type="submit" class="btn btn-primary w-100">{{ __('Filter') }}</button>
        </div>
      </form>
    </div>
  </div>

  @if($messages->count() > 0)
    <!-- Bulk Actions -->
    <div class="card mb-4">
      <div class="card-body">
        <form id="bulkForm" method="POST" action="{{ route('admin.messages.bulk-action') }}">
          @csrf
          <div class="row align-items-end">
            <div class="col-md-3">
              <label for="bulkAction" class="form-label">{{ __('Bulk Actions') }}</label>
              <select class="form-select" id="bulkAction" name="action" required>
                <option value="">{{ __('Select Action') }}</option>
                <option value="mark_read">{{ __('Mark as Read') }}</option>
                <option value="mark_unread">{{ __('Mark as Unread') }}</option>
                <option value="delete">{{ __('Delete') }}</option>
              </select>
            </div>
            <div class="col-md-2">
              <button type="submit" class="btn btn-warning w-100" disabled id="bulkSubmit">
                {{ __('Apply') }}
              </button>
            </div>
            <div class="col-md-7">
              <small class="text-muted">{{ __('Select messages below to perform bulk actions') }}</small>
            </div>
          </div>
        </form>
      </div>
    </div>

    <!-- Messages Table -->
    <div class="card">
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-hover">
            <thead class="table-light">
              <tr>
                <th width="50">
                  <input type="checkbox" id="selectAll" class="form-check-input">
                </th>
                <th>{{ __('Conversation') }}</th>
                <th>{{ __('Subject') }}</th>
                <th>{{ __('Product') }}</th>
                <th>{{ __('Status') }}</th>
                <th>{{ __('Date') }}</th>
                <th width="120">{{ __('Actions') }}</th>
              </tr>
            </thead>
            <tbody>
              @foreach($messages as $message)
                <tr class="{{ $message->isUnread() ? 'table-warning' : '' }}">
                  <td>
                    <input type="checkbox" name="message_ids[]" value="{{ $message->id }}" 
                           class="form-check-input message-checkbox">
                  </td>
                  <td>
                    <div class="d-flex align-items-center">
                      <div>
                        <strong>{{ $message->sender->name }}</strong>
                        <i class="fas fa-arrow-right mx-1 text-muted"></i>
                        <strong>{{ $message->receiver->name }}</strong>
                        <br>
                        <small class="text-muted">
                          {{ $message->sender->email }} → {{ $message->receiver->email }}
                        </small>
                      </div>
                    </div>
                  </td>
                  <td>
                    <div>
                      <strong>{{ $message->subject }}</strong>
                      <br>
                      <small class="text-muted">
                        {{ \Illuminate\Support\Str::limit($message->message, 60) }}
                      </small>
                    </div>
                  </td>
                  <td>
                    @if($message->product)
                      <a href="{{ route('products.show', $message->product) }}" 
                         class="text-decoration-none" target="_blank">
                        <small class="text-info">
                          <i class="fas fa-box me-1"></i>{{ $message->product->name }}
                        </small>
                      </a>
                    @else
                      <small class="text-muted">—</small>
                    @endif
                  </td>
                  <td>
                    @if($message->isUnread())
                      <span class="badge bg-warning">{{ __('Unread') }}</span>
                    @else
                      <span class="badge bg-success">{{ __('Read') }}</span>
                    @endif
                  </td>
                  <td>
                    <small>
                      {{ $message->created_at->format('M d, Y') }}
                      <br>
                      {{ $message->created_at->format('g:i A') }}
                    </small>
                  </td>
                  <td>
                    <div class="btn-group btn-group-sm">
                      <a href="{{ route('admin.messages.show', $message) }}" 
                         class="btn btn-outline-primary" title="{{ __('View') }}">
                        <i class="fas fa-eye"></i>
                      </a>
                      <form method="POST" action="{{ route('admin.messages.toggle-read', $message) }}" class="d-inline">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn btn-outline-{{ $message->isUnread() ? 'success' : 'warning' }}" 
                                title="{{ $message->isUnread() ? __('Mark as Read') : __('Mark as Unread') }}">
                          <i class="fas fa-envelope{{ $message->isUnread() ? '-open' : '' }}"></i>
                        </button>
                      </form>
                      <button type="button" class="btn btn-outline-danger" 
                              onclick="confirmDelete({{ $message->id }})" title="{{ __('Delete') }}">
                        <i class="fas fa-trash"></i>
                      </button>
                    </div>
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Pagination -->
    <div class="d-flex justify-content-center mt-4">
      {{ $messages->appends(request()->query())->links() }}
    </div>
  @else
    <div class="text-center py-5">
      <i class="fas fa-envelope fa-4x text-muted mb-3"></i>
      <h3>{{ __('No messages found') }}</h3>
      <p class="text-muted">{{ __('No messages match your current filters.') }}</p>
    </div>
  @endif
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
// Bulk actions handling
document.addEventListener('DOMContentLoaded', function() {
  const selectAll = document.getElementById('selectAll');
  const messageCheckboxes = document.querySelectorAll('.message-checkbox');
  const bulkForm = document.getElementById('bulkForm');
  const bulkSubmit = document.getElementById('bulkSubmit');
  const bulkAction = document.getElementById('bulkAction');

  // Select all functionality
  selectAll.addEventListener('change', function() {
    messageCheckboxes.forEach(checkbox => {
      checkbox.checked = this.checked;
    });
    updateBulkSubmit();
  });

  // Individual checkbox handling
  messageCheckboxes.forEach(checkbox => {
    checkbox.addEventListener('change', updateBulkSubmit);
  });

  // Update bulk submit button state
  function updateBulkSubmit() {
    const checkedBoxes = document.querySelectorAll('.message-checkbox:checked');
    bulkSubmit.disabled = checkedBoxes.length === 0 || !bulkAction.value;
  }

  // Bulk action selection
  bulkAction.addEventListener('change', updateBulkSubmit);

  // Bulk form submission
  bulkForm.addEventListener('submit', function(e) {
    const checkedBoxes = document.querySelectorAll('.message-checkbox:checked');
    if (checkedBoxes.length === 0) {
      e.preventDefault();
      alert('{{ __("Please select at least one message.") }}');
      return;
    }

    const action = bulkAction.value;
    if (action === 'delete') {
      if (!confirm('{{ __("Are you sure you want to delete the selected messages?") }}')) {
        e.preventDefault();
        return;
      }
    }

    // Add checked message IDs to form
    checkedBoxes.forEach(checkbox => {
      const input = document.createElement('input');
      input.type = 'hidden';
      input.name = 'message_ids[]';
      input.value = checkbox.value;
      bulkForm.appendChild(input);
    });
  });
});

// Delete confirmation
function confirmDelete(messageId) {
  const form = document.getElementById('deleteForm');
  form.action = `/admin/messages/${messageId}`;
  const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
  modal.show();
}
</script>
@endsection
