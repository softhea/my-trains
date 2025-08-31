@extends('layouts.app')

@section('title', __('Order Details'))

@section('content')
<div class="container py-5">
  <div class="row">
    <div class="col-md-8">
      <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>{{ __('Order #:id', ['id' => $order->id]) }}</h1>
        <span class="badge bg-{{ $order->getStatusColor() }}" style="font-size: 1rem; padding: 0.5rem 1rem;">{{ $order->getFormattedStatus() }}</span>
      </div>

      <div class="card">
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
              <h3>{{ $order->product->name }}</h3>
              <p class="text-muted mb-1">{{ __('By') }}: {{ $order->product->user->name ?? ($order->seller->name ?? '-') }}</p>
              <p class="text-muted">{{ $order->product->description }}</p>
              
              <div class="row mb-3">
                <div class="col-sm-6">
                  <strong>{{ __('Unit Price') }}:</strong> ${{ $order->product->price }}
                </div>
                <div class="col-sm-6">
                  <strong>{{ __('Quantity') }}:</strong> {{ $order->quantity }}
                </div>
              </div>
              
              <div class="row mb-3">
                <div class="col-sm-6">
                  <strong>{{ __('Total Price') }}:</strong> ${{ $order->total_price }}
                </div>
                <div class="col-sm-6">
                  <strong>{{ __('Order Date') }}:</strong> {{ $order->created_at->format('M d, Y \a\t g:i A') }}
                </div>
              </div>
              
              @if($order->note)
                <div class="mb-3">
                  <strong>{{ __('Note') }}:</strong>
                  <p class="mt-1">{{ $order->note }}</p>
                </div>
              @endif
            </div>
          </div>
        </div>
      </div>
    </div>
    
    <div class="col-md-4">
      <div class="card">
        <div class="card-body">
          <h5 class="card-title">{{ __('Order Actions') }}</h5>
          
          @if($order->canBeCancelled())
            <form method="POST" action="{{ route('orders.cancel', $order) }}" 
                  onsubmit="return confirm('{{ __('Are you sure you want to cancel this order? This action cannot be undone.') }}')">
              @csrf
              <button type="submit" class="btn btn-danger w-100 mb-3">{{ __('Cancel Order') }}</button>
            </form>
          @endif
          
          <a href="{{ route('products.show', $order->product) }}" class="btn btn-outline-primary w-100 mb-2">
            {{ __('View Product') }}
          </a>
          
          <a href="{{ route('orders.index') }}" class="btn btn-outline-secondary w-100">
            {{ __('Back to Orders') }}
          </a>
        </div>
      </div>
      
      <div class="card mt-3">
        <div class="card-body">
          <h5 class="card-title">{{ __('Order Timeline') }}</h5>
          <div class="timeline">
            <div class="timeline-item">
              <i class="fas fa-shopping-cart text-primary"></i>
              <div class="timeline-content">
                <h6>{{ __('Order Placed') }}</h6>
                <small class="text-muted">{{ $order->created_at->format('M d, Y \a\t g:i A') }}</small>
              </div>
            </div>
            
            @if($order->status === 'processing')
              <div class="timeline-item">
                <i class="fas fa-cog text-info"></i>
                <div class="timeline-content">
                  <h6>{{ __('Processing') }}</h6>
                  <small class="text-muted">{{ __('Order is being processed') }}</small>
                </div>
              </div>
            @elseif($order->status === 'completed')
              <div class="timeline-item">
                <i class="fas fa-check text-success"></i>
                <div class="timeline-content">
                  <h6>{{ __('Completed') }}</h6>
                  <small class="text-muted">{{ __('Order completed') }}</small>
                </div>
              </div>
            @elseif($order->status === 'cancelled')
              <div class="timeline-item">
                <i class="fas fa-times text-danger"></i>
                <div class="timeline-content">
                  <h6>{{ __('Cancelled') }}</h6>
                  <small class="text-muted">{{ __('Order was cancelled') }}</small>
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
