@extends('layouts.app')

@section('title', 'Order Details')

@section('content')
<div class="container py-4">
  <div class="row">
    <div class="col-md-8">
      <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Order #{{ $order->id }}</h1>
        <div>
          <span class="badge bg-{{ $order->getStatusColor() }} fs-6">{{ $order->getFormattedStatus() }}</span>
          <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary ms-2">Back to Orders</a>
        </div>
      </div>

      <!-- Customer Information -->
      <div class="card mb-4">
        <div class="card-header">
          <h5 class="mb-0">Customer Information</h5>
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-md-6">
              <strong>Name:</strong> {{ $order->user->name }}<br>
              <strong>Email:</strong> {{ $order->user->email }}<br>
              @if($order->user->phone)
                <strong>Phone:</strong> {{ $order->user->phone }}<br>
              @endif
              @if($order->user->city)
                <strong>City:</strong> {{ $order->user->city }}
              @endif
            </div>
            <div class="col-md-6">
              <strong>Customer Since:</strong> {{ $order->user->created_at->format('M d, Y') }}<br>
              <strong>Total Orders:</strong> {{ $order->user->orders->count() }}
            </div>
          </div>
        </div>
      </div>

      <!-- Product Information -->
      <div class="card mb-4">
        <div class="card-header">
          <h5 class="mb-0">Product Information</h5>
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-md-4">
              @if($order->product->images->count() > 0)
                <img src="{{ $order->product->images->first()->url }}" 
                     class="img-fluid rounded" 
                     alt="{{ $order->product->name }}">
              @else
                <div class="bg-light rounded d-flex align-items-center justify-content-center" style="height: 200px;">
                  <i class="fas fa-image fa-3x text-muted"></i>
                </div>
              @endif
            </div>
            <div class="col-md-8">
              <h4>{{ $order->product->name }}</h4>
              <p class="text-muted">{{ $order->product->description }}</p>
              
              <div class="row">
                <div class="col-sm-6">
                  <strong>Unit Price:</strong> ${{ $order->product->price }}
                </div>
                <div class="col-sm-6">
                  <strong>Current Stock:</strong> 
                  <span class="badge bg-{{ $order->product->getStockStatus() === 'out_of_stock' ? 'danger' : ($order->product->getStockStatus() === 'low_stock' ? 'warning' : 'success') }}">
                    {{ $order->product->no_of_items }} items
                  </span>
                </div>
              </div>
              
              <div class="mt-3">
                <a href="{{ route('products.show', $order->product) }}" 
                   class="btn btn-outline-primary btn-sm" target="_blank">
                  View Product Page
                </a>
                <a href="{{ route('admin.products.edit', $order->product) }}" 
                   class="btn btn-outline-secondary btn-sm">
                  Edit Product
                </a>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Order Details -->
      <div class="card">
        <div class="card-header">
          <h5 class="mb-0">Order Details</h5>
        </div>
        <div class="card-body">
          <div class="row mb-3">
            <div class="col-md-4">
              <strong>Quantity Ordered:</strong> {{ $order->quantity }}
            </div>
            <div class="col-md-4">
              <strong>Unit Price:</strong> ${{ $order->product->price }}
            </div>
            <div class="col-md-4">
              <strong>Total Price:</strong> <span class="h5 text-primary">${{ $order->total_price }}</span>
            </div>
          </div>

          <div class="row mb-3">
            <div class="col-md-6">
              <strong>Order Date:</strong> {{ $order->created_at->format('F j, Y \a\t g:i A') }}
            </div>
            <div class="col-md-6">
              <strong>Last Updated:</strong> {{ $order->updated_at->format('F j, Y \a\t g:i A') }}
            </div>
          </div>

          @if($order->note)
            <div class="mt-3">
              <strong>Customer Note:</strong>
              <div class="bg-light p-3 rounded mt-2">
                {{ $order->note }}
              </div>
            </div>
          @endif
        </div>
      </div>
    </div>

    <div class="col-md-4">
      <!-- Status Management -->
      <div class="card mb-4">
        <div class="card-header">
          <h5 class="mb-0">Status Management</h5>
        </div>
        <div class="card-body">
          <form method="POST" action="{{ route('admin.orders.update-status', $order) }}">
            @csrf
            @method('PUT')
            <div class="mb-3">
              <label for="status" class="form-label">Current Status</label>
              <select name="status" id="status" class="form-select" required>
                <option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="processing" {{ $order->status === 'processing' ? 'selected' : '' }}>Processing</option>
                <option value="completed" {{ $order->status === 'completed' ? 'selected' : '' }}>Completed</option>
                <option value="cancelled" {{ $order->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
              </select>
            </div>
            <button type="submit" class="btn btn-primary w-100">Update Status</button>
          </form>
        </div>
      </div>

      <!-- Quick Actions -->
      <div class="card mb-4">
        <div class="card-header">
          <h5 class="mb-0">Quick Actions</h5>
        </div>
        <div class="card-body">
          @if($order->status === 'pending')
            <form method="POST" action="{{ route('admin.orders.update-status', $order) }}" class="mb-2">
              @csrf
              @method('PUT')
              <input type="hidden" name="status" value="processing">
              <button type="submit" class="btn btn-info w-100">Mark as Processing</button>
            </form>
          @endif

          @if(in_array($order->status, ['pending', 'processing']))
            <form method="POST" action="{{ route('admin.orders.update-status', $order) }}" class="mb-2">
              @csrf
              @method('PUT')
              <input type="hidden" name="status" value="completed">
              <button type="submit" class="btn btn-success w-100">Mark as Completed</button>
            </form>
          @endif

          @if($order->canBeCancelled())
            <form method="POST" action="{{ route('admin.orders.update-status', $order) }}" class="mb-2"
                  onsubmit="return confirm('Are you sure you want to cancel this order?')">
              @csrf
              @method('PUT')
              <input type="hidden" name="status" value="cancelled">
              <button type="submit" class="btn btn-warning w-100">Cancel Order</button>
            </form>
          @endif

          <hr>

          <form method="POST" action="{{ route('admin.orders.destroy', $order) }}" 
                onsubmit="return confirm('Are you sure you want to delete this order? This action cannot be undone.')">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger w-100">Delete Order</button>
          </form>
        </div>
      </div>

      <!-- Order Timeline -->
      <div class="card">
        <div class="card-header">
          <h5 class="mb-0">Order Timeline</h5>
        </div>
        <div class="card-body">
          <div class="timeline">
            <div class="timeline-item">
              <i class="fas fa-shopping-cart text-primary"></i>
              <div class="timeline-content">
                <h6>Order Placed</h6>
                <small class="text-muted">{{ $order->created_at->format('M d, Y \a\t g:i A') }}</small>
              </div>
            </div>
            
            @if($order->status === 'processing')
              <div class="timeline-item">
                <i class="fas fa-cog text-info"></i>
                <div class="timeline-content">
                  <h6>Processing</h6>
                  <small class="text-muted">Order is being processed</small>
                </div>
              </div>
            @elseif($order->status === 'completed')
              <div class="timeline-item">
                <i class="fas fa-check text-success"></i>
                <div class="timeline-content">
                  <h6>Completed</h6>
                  <small class="text-muted">Order completed</small>
                </div>
              </div>
            @elseif($order->status === 'cancelled')
              <div class="timeline-item">
                <i class="fas fa-times text-danger"></i>
                <div class="timeline-content">
                  <h6>Cancelled</h6>
                  <small class="text-muted">Order was cancelled</small>
                </div>
              </div>
            @endif
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<style>
.timeline {
  position: relative;
  padding-left: 30px;
}

.timeline-item {
  position: relative;
  margin-bottom: 20px;
}

.timeline-item i {
  position: absolute;
  left: -30px;
  top: 0;
  background: white;
  padding: 3px;
  border-radius: 50%;
  border: 2px solid currentColor;
}

.timeline-item:not(:last-child)::before {
  content: '';
  position: absolute;
  left: -22px;
  top: 20px;
  height: 30px;
  width: 2px;
  background: #dee2e6;
}

.timeline-content h6 {
  margin: 0;
  font-weight: 600;
}

.timeline-content small {
  display: block;
  margin-top: 2px;
}
</style>
@endsection
