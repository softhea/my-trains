@extends('layouts.app')

@section('title', 'Order Management')

@section('content')
<div class="container-fluid py-4">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Order Management</h1>
  </div>

  @if($orders->count() > 0)
    <div class="card">
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-hover">
            <thead class="table-light">
              <tr>
                <th>Order ID</th>
                <th>Customer</th>
                <th>Seller</th>
                <th>Product</th>
                <th>Quantity</th>
                <th>Total</th>
                <th>Status</th>
                <th>Date</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              @foreach($orders as $order)
                <tr>
                  <td>
                    <strong>#{{ $order->id }}</strong>
                  </td>
                  <td>
                    <div>
                      <strong>{{ $order->user->name }}</strong>
                      <br>
                      <small class="text-muted">{{ $order->user->email }}</small>
                    </div>
                  </td>
                  <td>
                    <div>
                      <strong>{{ $order->seller->name ?? '—' }}</strong>
                      <br>
                      <small class="text-muted">{{ $order->seller->email ?? '' }}</small>
                    </div>
                  </td>
                  <td>
                    <div class="d-flex align-items-center">
                      @if($order->product->images->count() > 0)
                        <img src="{{ $order->product->images->first()->url }}" 
                             class="rounded me-2" 
                             style="width: 40px; height: 40px; object-fit: cover;">
                      @endif
                      <div>
                        <strong>{{ $order->product->name }}</strong>
                        <br>
                        <small class="text-muted">${{ $order->product->price }} each</small>
                      </div>
                    </div>
                  </td>
                  <td>{{ $order->quantity }}</td>
                  <td><strong>${{ $order->total_price }}</strong></td>
                  <td>
                    <span class="badge bg-{{ $order->getStatusColor() }}">
                      {{ $order->getFormattedStatus() }}
                    </span>
                  </td>
                  <td>
                    {{ $order->created_at->format('M d, Y') }}
                    <br>
                    <small class="text-muted">{{ $order->created_at->format('g:i A') }}</small>
                  </td>
                  <td>
                    <div class="btn-group btn-group-sm">
                      <a href="{{ route('admin.orders.show', $order) }}" 
                         class="btn btn-outline-primary" title="View Details">
                        <i class="fas fa-eye"></i>
                      </a>
                      @if($order->canBeCancelled())
                        <button type="button" 
                                class="btn btn-outline-warning" 
                                data-bs-toggle="modal" 
                                data-bs-target="#statusModal{{ $order->id }}"
                                title="Update Status">
                          <i class="fas fa-edit"></i>
                        </button>
                      @endif
                      <button type="button" 
                              class="btn btn-outline-danger" 
                              onclick="confirmDelete({{ $order->id }})"
                              title="Delete Order">
                        <i class="fas fa-trash"></i>
                      </button>
                    </div>
                  </td>
                </tr>

                <!-- Status Update Modal -->
                <div class="modal fade" id="statusModal{{ $order->id }}" tabindex="-1">
                  <div class="modal-dialog">
                    <div class="modal-content">
                      <div class="modal-header">
                        <h5 class="modal-title">Update Order Status</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                      </div>
                      <form method="POST" action="{{ route('admin.orders.update-status', $order) }}">
                        @csrf
                        @method('PUT')
                        <div class="modal-body">
                          <p><strong>Order #{{ $order->id }}</strong> - {{ $order->product->name }}</p>
                          <div class="mb-3">
                            <label for="status{{ $order->id }}" class="form-label">Status</label>
                            <select name="status" id="status{{ $order->id }}" class="form-select" required>
                              <option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>Pending</option>
                              <option value="processing" {{ $order->status === 'processing' ? 'selected' : '' }}>Processing</option>
                              <option value="completed" {{ $order->status === 'completed' ? 'selected' : '' }}>Completed</option>
                              <option value="cancelled" {{ $order->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </select>
                          </div>
                        </div>
                        <div class="modal-footer">
                          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                          <button type="submit" class="btn btn-primary">Update Status</button>
                        </div>
                      </form>
                    </div>
                  </div>
                </div>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <div class="d-flex justify-content-center mt-4">
      {{ $orders->links() }}
    </div>
  @else
    <div class="text-center py-5">
      <i class="fas fa-clipboard-list fa-4x text-muted mb-3"></i>
      <h3>No orders found</h3>
      <p class="text-muted">When customers place orders, they will appear here.</p>
    </div>
  @endif
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Confirm Delete</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <p>Are you sure you want to delete this order? This action cannot be undone.</p>
        <p class="text-warning"><strong>Note:</strong> If the order is not cancelled, the stock will be restored to the product.</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <form id="deleteForm" method="POST" style="display: inline;">
          @csrf
          @method('DELETE')
          <button type="submit" class="btn btn-danger">Delete Order</button>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
function confirmDelete(orderId) {
  const form = document.getElementById('deleteForm');
  form.action = `/admin/orders/${orderId}`;
  const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
  modal.show();
}
</script>
@endsection
