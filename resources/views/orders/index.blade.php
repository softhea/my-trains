@extends('layouts.app')

@section('title', 'My Orders')

@section('content')
<div class="container py-5">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h1>My Orders</h1>
    <a href="{{ route('home') }}" class="btn btn-primary">Continue Shopping</a>
  </div>

  @if($orders->count() > 0)
    <div class="row">
      @foreach($orders as $order)
        <div class="col-md-6 col-lg-4 mb-4">
          <div class="card">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-start mb-3">
                <h5 class="card-title">Order #{{ $order->id }}</h5>
                <span class="badge bg-{{ $order->getStatusColor() }}">{{ $order->getFormattedStatus() }}</span>
              </div>
              
              <div class="row mb-3">
                <div class="col-4">
                  @if($order->product->images->count() > 0)
                    <img src="{{ $order->product->images->first()->url }}" 
                         class="img-fluid rounded" 
                         style="height: 80px; object-fit: cover;">
                  @else
                    <div class="bg-light rounded d-flex align-items-center justify-content-center" style="height: 80px;">
                      <i class="fas fa-image text-muted"></i>
                    </div>
                  @endif
                </div>
                <div class="col-8">
                  <h6 class="mb-1">{{ $order->product->name }}</h6>
                  <p class="text-muted small mb-1">Quantity: {{ $order->quantity }}</p>
                  <p class="text-muted small mb-0">Total: ${{ $order->total_price }}</p>
                </div>
              </div>
              
              <div class="d-flex justify-content-between align-items-center">
                <small class="text-muted">{{ $order->created_at->format('M d, Y') }}</small>
                <div>
                  <a href="{{ route('orders.show', $order) }}" class="btn btn-sm btn-outline-primary">View</a>
                  @if($order->canBeCancelled())
                    <form method="POST" action="{{ route('orders.cancel', $order) }}" class="d-inline" 
                          onsubmit="return confirm('Are you sure you want to cancel this order?')">
                      @csrf
                      <button type="submit" class="btn btn-sm btn-outline-danger">Cancel</button>
                    </form>
                  @endif
                </div>
              </div>
            </div>
          </div>
        </div>
      @endforeach
    </div>

    <div class="d-flex justify-content-center">
      {{ $orders->links() }}
    </div>
  @else
    <div class="text-center py-5">
      <i class="fas fa-shopping-cart fa-4x text-muted mb-3"></i>
      <h3>No orders yet</h3>
      <p class="text-muted">You haven't placed any orders yet. Start shopping to see your orders here.</p>
      <a href="{{ route('home') }}" class="btn btn-primary">Start Shopping</a>
    </div>
  @endif
</div>
@endsection
