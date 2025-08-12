@extends('layouts.app')

@section('title', __('My Products'))

@section('content')
<div class="container py-5">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h1>{{ __('My Products') }}</h1>
    <a href="{{ route('admin.products.create') }}" class="btn btn-primary">
      <i class="fas fa-plus me-1"></i>{{ __('Add Product') }}
    </a>
  </div>

  @if($products->count() > 0)
    <div class="row">
      @foreach($products as $product)
        <div class="col-md-6 col-xl-4 mb-4">
          <div class="card h-100 product-card">
            <div class="position-relative">
              @if($product->images->count() > 0)
                <img src="{{ $product->images->first()->url }}" 
                     class="card-img-top" 
                     alt="{{ $product->name }}"
                     style="height: 200px; object-fit: cover;">
              @else
                <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 200px;">
                  <i class="fas fa-image fa-3x text-muted"></i>
                </div>
              @endif
              @if($product->getStockStatus() === 'low_stock')
                <span class="position-absolute top-0 end-0 m-2 badge bg-warning">{{ __('Low Stock') }}</span>
              @endif
            </div>
            <div class="card-body d-flex flex-column">
              <h5 class="card-title">{{ $product->name }}</h5>
              <p class="card-text text-muted small flex-grow-1">{{ \Illuminate\Support\Str::limit($product->description, 80) }}</p>
              <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="h5 mb-0 text-primary">${{ number_format($product->price, 2) }}</span>
                @if($product->category)
                  <span class="badge bg-secondary">{{ $product->category->name }}</span>
                @endif
              </div>
              <div class="d-flex justify-content-between align-items-center">
                <small class="text-muted">
                  <i class="fas fa-box me-1"></i>{{ $product->no_of_items }} {{ __('in stock') }}
                </small>
                <small class="text-muted">
                  {{ $product->created_at->format('M d, Y') }}
                </small>
              </div>
              <div class="mt-3 d-flex gap-2">
                <a href="{{ route('products.show', $product) }}" class="btn btn-outline-primary w-50">
                  <i class="fas fa-eye me-1"></i>{{ __('View') }}
                </a>
                <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-outline-secondary w-50">
                  <i class="fas fa-edit me-1"></i>{{ __('Edit') }}
                </a>
              </div>
            </div>
          </div>
        </div>
      @endforeach
    </div>

    <div class="d-flex justify-content-center">
      {{ $products->links() }}
    </div>
  @else
    <div class="text-center py-5">
      <i class="fas fa-box-open fa-4x text-muted mb-3"></i>
      <h3>{{ __('No products yet') }}</h3>
      <p class="text-muted">{{ __('Start by adding your first product.') }}</p>
      <a href="{{ route('admin.products.create') }}" class="btn btn-primary">
        <i class="fas fa-plus me-1"></i>{{ __('Add Product') }}
      </a>
    </div>
  @endif
</div>
@endsection


