@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3">{{ __('My Product Orders') }}</h1>
                <div>
                    <span class="badge bg-info me-2">{{ __('Total Orders: :count', ['count' => $orders->total()]) }}</span>
                    <span class="badge bg-warning me-2">{{ __('Pending: :count', ['count' => $orders->where('status', 'pending')->count()]) }}</span>
                </div>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">{{ __('Orders for Your Products') }}</h5>
                    <small class="text-muted">{{ __('Manage orders placed by customers for your products') }}</small>
                </div>
                <div class="card-body p-0">
                    @if($orders->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>{{ __('Order #') }}</th>
                                        <th>{{ __('Product') }}</th>
                                        <th>{{ __('Customer') }}</th>
                                        <th>{{ __('Quantity') }}</th>
                                        <th>{{ __('Total') }}</th>
                                        <th>{{ __('Status') }}</th>
                                        <th>{{ __('Date') }}</th>
                                        <th>{{ __('Actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($orders as $order)
                                        <tr>
                                            <td>
                                                <strong>#{{ $order->id }}</strong>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    @if($order->product->images->count() > 0)
                                                        <img src="{{ $order->product->images->first()->url }}" 
                                                             alt="{{ $order->product->name }}" 
                                                             class="rounded me-2"
                                                             style="width: 40px; height: 40px; object-fit: cover;">
                                                    @endif
                                                    <div>
                                                        <strong>{{ $order->product->name }}</strong>
                                                        <br>
                                                        <small class="text-muted">{{ __('Stock: :count', ['count' => $order->product->no_of_items]) }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div>
                                                    <strong>{{ $order->user->name }}</strong>
                                                    <br>
                                                    <small class="text-muted">{{ $order->user->email }}</small>
                                                </div>
                                            </td>
                                            <td>{{ $order->quantity }}</td>
                                            <td>
                                                <strong>{{ format_currency($order->total_price, $order->product->currency) }}</strong>
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ $order->getStatusColor() }}">
                                                    {{ $order->getFormattedStatus() }}
                                                </span>
                                            </td>
                                            <td>
                                                <small>{{ $order->created_at->format('M d, Y') }}</small>
                                                <br>
                                                <small class="text-muted">{{ $order->created_at->format('H:i') }}</small>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <!-- View Order Details -->
                                                    <a href="{{ route('seller.orders.show', $order) }}" 
                                                       class="btn btn-outline-info btn-sm"
                                                       title="{{ __('View Details') }}">
                                                        <i class="fas fa-eye"></i>
                                                    </a>

                                                    <!-- Quick Status Actions -->
                                                    @if($order->status === 'pending')
                                                        <!-- Accept Order (Pending -> Processing) -->
                                                        <button type="button" 
                                                                class="btn btn-outline-success btn-sm" 
                                                                onclick="updateOrderStatus({{ $order->id }}, 'processing')"
                                                                title="{{ __('Accept Order') }}">
                                                            <i class="fas fa-check"></i>
                                                        </button>
                                                        
                                                        <!-- Reject Order (Pending -> Cancelled) -->
                                                        <button type="button" 
                                                                class="btn btn-outline-danger btn-sm" 
                                                                onclick="updateOrderStatus({{ $order->id }}, 'cancelled')"
                                                                title="{{ __('Reject Order') }}">
                                                            <i class="fas fa-times"></i>
                                                        </button>
                                                    @elseif($order->status === 'processing')
                                                        <!-- Complete Order -->
                                                        <button type="button" 
                                                                class="btn btn-outline-primary btn-sm" 
                                                                onclick="updateOrderStatus({{ $order->id }}, 'completed')"
                                                                title="{{ __('Mark as Completed') }}">
                                                            <i class="fas fa-check-circle"></i>
                                                        </button>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">{{ __('No Orders Yet') }}</h5>
                            <p class="text-muted">{{ __('Orders for your products will appear here.') }}</p>
                            <a href="{{ route('admin.products.index') }}" class="btn btn-primary">
                                <i class="fas fa-plus me-1"></i>
                                {{ __('Add Your First Product') }}
                            </a>
                        </div>
                    @endif
                </div>
                
                @if($orders->count() > 0)
                    <div class="card-footer">
                        {{ $orders->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Status Update Forms (Hidden) -->
@foreach($orders as $order)
    <form id="statusForm{{ $order->id }}" method="POST" action="{{ route('seller.orders.update-status', $order) }}" style="display: none;">
        @csrf
        @method('PUT')
        <input type="hidden" name="status" id="statusInput{{ $order->id }}">
    </form>
@endforeach

<script>
function updateOrderStatus(orderId, newStatus) {
    const statusMessages = {
        'processing': '{{ __("Are you sure you want to accept this order? This will reduce your product stock.") }}',
        'completed': '{{ __("Are you sure you want to mark this order as completed?") }}',
        'cancelled': '{{ __("Are you sure you want to reject this order?") }}'
    };

    if (confirm(statusMessages[newStatus])) {
        document.getElementById('statusInput' + orderId).value = newStatus;
        document.getElementById('statusForm' + orderId).submit();
    }
}
</script>
@endsection
