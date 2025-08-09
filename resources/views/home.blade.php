@extends('layouts.app')

@section('title', 'Home')

@section('content')
<section id="hero">
  <!-- Hero banner from Majestic -->
</section>

<section id="products" class="container pb-5">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Featured Trains</h2>
    <a href="{{ route('products.index') }}" class="btn btn-outline-primary">
      <i class="fas fa-shopping-bag me-1"></i>View All Products
    </a>
  </div>
  <div class="row">
    @foreach ($products as $product)
      <div class="col-md-4 mb-4">
        <div class="card h-100">
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
              <span class="position-absolute top-0 end-0 m-2 badge bg-warning">Low Stock</span>
            @endif
          </div>
          <div class="card-body d-flex flex-column">
            <h5 class="card-title">{{ $product->name }}</h5>
            <p class="card-text text-muted flex-grow-1">{{ \Illuminate\Support\Str::limit($product->description, 80) }}</p>
            <div class="d-flex justify-content-between align-items-center mb-2">
              <span class="h5 mb-0 text-primary">${{ number_format($product->price, 2) }}</span>
              @if($product->category)
                <span class="badge bg-secondary">{{ $product->category->name }}</span>
              @endif
            </div>
            <small class="text-muted mb-3">
              <i class="fas fa-box me-1"></i>{{ $product->no_of_items }} in stock
            </small>
            <a href="{{ route('products.show', $product) }}" class="btn btn-primary">
              <i class="fas fa-eye me-1"></i>View Details
            </a>
          </div>
        </div>
      </div>
    @endforeach
  </div>
</section>
@endsection