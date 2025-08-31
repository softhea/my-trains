@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8">
            <!-- Order Details -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">{{ __('Order Details') }} #{{ $order->id }}</h5>
                    <span class="badge bg-{{ $order->getStatusColor() }} fs-6">
                        {{ $order->getFormattedStatus() }}
                    </span>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>{{ __('Customer Information') }}</h6>
                            <p class="mb-1"><strong>{{ __('Name:') }}</strong> {{ $order->user->name }}</p>
                            <p class="mb-1"><strong>{{ __('Email:') }}</strong> {{ $order->user->email }}</p>
                            <p class="mb-3"><strong>{{ __('Provider:') }}</strong> 
                                @if($order->user->isGoogleUser())
                                    <span class="badge bg-danger">Google</span>
                                @elseif($order->user->isAppleUser())
                                    <span class="badge bg-dark">Apple</span>
                                @else
                                    <span class="badge bg-secondary">Email</span>
                                @endif
                            </p>
                        </div>
                        <div class="col-md-6">
                            <h6>{{ __('Order Information') }}</h6>
                            <p class="mb-1"><strong>{{ __('Order Date:') }}</strong> {{ $order->created_at->format('M d, Y \a\t g:i A') }}</p>
                            <p class="mb-1"><strong>{{ __('Last Updated:') }}</strong> {{ $order->updated_at->format('M d, Y \a\t g:i A') }}</p>
                            <p class="mb-1"><strong>{{ __('Total Amount:') }}</strong> 
                                <span class="text-success fw-bold">{{ format_currency($order->total_price, $order->product->currency) }}</span>
                            </p>
                        </div>
                    </div>

                    @if($order->note)
                        <hr>
                        <div>
                            <h6>{{ __('Customer Note') }}</h6>
                            <div class="bg-light p-3 rounded">
                                <p class="mb-0" style="white-space: pre-wrap;">{{ $order->note }}</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Product Details -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">{{ __('Product Details') }}</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            @if($order->product->images->count() > 0)
                                <img src="{{ $order->product->images->first()->url }}" 
                                     alt="{{ $order->product->name }}" 
                                     class="img-fluid rounded">
                            @else
                                <div class="bg-light rounded d-flex align-items-center justify-content-center" style="height: 200px;">
                                    <i class="fas fa-image fa-3x text-muted"></i>
                                </div>
                            @endif
                        </div>
                        <div class="col-md-8">
                            <h4>{{ $order->product->name }}</h4>
                            <p class="text-muted mb-3" style="white-space: pre-wrap;">{{ Str::limit($order->product->description, 200) }}</p>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <p class="mb-1"><strong>{{ __('Unit Price:') }}</strong> {{ format_currency($order->product->price, $order->product->currency) }}</p>
                                    <p class="mb-1"><strong>{{ __('Quantity Ordered:') }}</strong> {{ $order->quantity }}</p>
                                    <p class="mb-1"><strong>{{ __('Current Stock:') }}</strong> 
                                        <span class="badge bg-{{ $order->product->getStockStatus() === 'out_of_stock' ? 'danger' : ($order->product->getStockStatus() === 'low_stock' ? 'warning' : 'success') }}">
                                            {{ $order->product->no_of_items }} {{ __('items') }}
                                        </span>
                                    </p>
                                </div>
                                <div class="col-md-6">
                                    <p class="mb-1"><strong>{{ __('Category:') }}</strong> {{ $order->product->category->name ?? __('Uncategorized') }}</p>
                                    <p class="mb-1"><strong>{{ __('Total Price:') }}</strong> 
                                        <span class="text-success fw-bold">{{ format_currency($order->total_price, $order->product->currency) }}</span>
                                    </p>
                                </div>
                            </div>

                            <div class="mt-3">
                                <a href="{{ route('products.show', $order->product) }}" 
                                   class="btn btn-outline-primary btn-sm" 
                                   target="_blank">
                                    <i class="fas fa-external-link-alt me-1"></i>
                                    {{ __('View Product Page') }}
                                </a>
                                <a href="{{ route('admin.products.edit', $order->product) }}" 
                                   class="btn btn-outline-secondary btn-sm">
                                    <i class="fas fa-edit me-1"></i>
                                    {{ __('Edit Product') }}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <!-- Order Status Management -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">{{ __('Order Status') }}</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('seller.orders.update-status', $order) }}">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label for="status" class="form-label">{{ __('Current Status') }}</label>
                            <select name="status" id="status" class="form-select" required>
                                <option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>{{ __('Pending') }}</option>
                                <option value="processing" {{ $order->status === 'processing' ? 'selected' : '' }}>{{ __('Processing') }}</option>
                                <option value="completed" {{ $order->status === 'completed' ? 'selected' : '' }}>{{ __('Completed') }}</option>
                                <option value="cancelled" {{ $order->status === 'cancelled' ? 'selected' : '' }}>{{ __('Cancelled') }}</option>
                            </select>
                            <div class="form-text">
                                @if($order->status === 'pending')
                                    {{ __('Stock will be reduced when you change status to "Processing"') }}
                                @elseif($order->status === 'processing')
                                    {{ __('Stock has been reduced for this order') }}
                                @elseif($order->status === 'completed')
                                    {{ __('This order is completed') }}
                                @else
                                    {{ __('This order has been cancelled') }}
                                @endif
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">{{ __('Update Status') }}</button>
                    </form>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">{{ __('Quick Actions') }}</h5>
                </div>
                <div class="card-body">
                    @if($order->status === 'pending')
                        <form method="POST" action="{{ route('seller.orders.update-status', $order) }}" class="mb-2">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="status" value="processing">
                            <button type="submit" 
                                    class="btn btn-success w-100"
                                    onclick="return confirm('{{ __('Accept this order? This will reduce your product stock by :quantity items.', ['quantity' => $order->quantity]) }}')">
                                <i class="fas fa-check me-1"></i>
                                {{ __('Accept Order') }}
                            </button>
                        </form>
                        
                        <form method="POST" action="{{ route('seller.orders.update-status', $order) }}">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="status" value="cancelled">
                            <button type="submit" 
                                    class="btn btn-outline-danger w-100"
                                    onclick="return confirm('{{ __('Are you sure you want to reject this order?') }}')">
                                <i class="fas fa-times me-1"></i>
                                {{ __('Reject Order') }}
                            </button>
                        </form>
                    @elseif($order->status === 'processing')
                        <form method="POST" action="{{ route('seller.orders.update-status', $order) }}" class="mb-2">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="status" value="completed">
                            <button type="submit" 
                                    class="btn btn-primary w-100"
                                    onclick="return confirm('{{ __('Mark this order as completed?') }}')">
                                <i class="fas fa-check-circle me-1"></i>
                                {{ __('Mark as Completed') }}
                            </button>
                        </form>
                        
                        <form method="POST" action="{{ route('seller.orders.update-status', $order) }}">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="status" value="pending">
                            <button type="submit" 
                                    class="btn btn-outline-warning w-100"
                                    onclick="return confirm('{{ __('Move back to pending? This will restore the stock.') }}')">
                                <i class="fas fa-undo me-1"></i>
                                {{ __('Back to Pending') }}
                            </button>
                        </form>
                    @endif
                </div>
            </div>

            <!-- Stock Information -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">{{ __('Stock Information') }}</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span>{{ __('Current Stock:') }}</span>
                        <span class="badge bg-{{ $order->product->getStockStatus() === 'out_of_stock' ? 'danger' : ($order->product->getStockStatus() === 'low_stock' ? 'warning' : 'success') }}">
                            {{ $order->product->no_of_items }}
                        </span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span>{{ __('Order Quantity:') }}</span>
                        <span class="badge bg-info">{{ $order->quantity }}</span>
                    </div>
                    @if($order->status === 'pending')
                        <div class="d-flex justify-content-between align-items-center">
                            <span>{{ __('After Acceptance:') }}</span>
                            <span class="badge bg-secondary">{{ max(0, $order->product->no_of_items - $order->quantity) }}</span>
                        </div>
                        <small class="text-muted">{{ __('Stock will be reduced when you accept the order') }}</small>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-md-12">
            <a href="{{ route('seller.orders.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i>
                {{ __('Back to My Orders') }}
            </a>
        </div>
    </div>
</div>
@endsection
