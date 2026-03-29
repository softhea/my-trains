@extends('layouts.app')

@section('title', $bundle->name)

@section('content')
<div class="container py-5">
  <nav aria-label="breadcrumb" class="mb-4">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('Home') }}</a></li>
      <li class="breadcrumb-item"><a href="{{ route('products.index') }}">{{ __('Products') }}</a></li>
      <li class="breadcrumb-item active">{{ $bundle->name }}</li>
    </ol>
  </nav>

  <div class="row">
    <div class="col-md-6">
      @php
        $bundleImages = $bundle->images->count() > 0 ? $bundle->images : collect();
        if ($bundleImages->isEmpty()) {
          foreach ($bundle->products as $product) {
            if ($product->images->count() > 0) {
              $bundleImages = $bundleImages->merge($product->images->take(1));
            }
          }
        }
      @endphp

      @if($bundleImages->count() > 0)
        <div id="bundleCarousel" class="carousel slide" data-bs-ride="carousel">
          <div class="carousel-inner">
            @foreach($bundleImages as $image)
              <div class="carousel-item @if($loop->first) active @endif">
                <img src="{{ $image->url }}" class="d-block w-100" alt="{{ $bundle->name }}"
                     style="max-height: 400px; object-fit: cover;">
              </div>
            @endforeach
          </div>
          @if($bundleImages->count() > 1)
            <button class="carousel-control-prev" type="button" data-bs-target="#bundleCarousel" data-bs-slide="prev">
              <span class="carousel-control-prev-icon"></span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#bundleCarousel" data-bs-slide="next">
              <span class="carousel-control-next-icon"></span>
            </button>
          @endif
        </div>
      @else
        <div class="bg-light d-flex align-items-center justify-content-center" style="height: 400px;">
          <div class="text-center text-muted">
            <i class="fas fa-box-open fa-3x mb-3"></i>
            <p>{{ __('No images available') }}</p>
          </div>
        </div>
      @endif
    </div>

    <div class="col-md-6">
      <h1>{{ $bundle->name }}</h1>
      <p class="text-muted mb-1">{{ __('Sold by') }}: {{ $bundle->user->name ?? '-' }}</p>
      
      <div class="my-3">
        @if($bundle->has_meaningful_savings)
          <span class="text-decoration-line-through text-muted h5">{{ format_currency($bundle->total_products_value, $bundle->currency) }}</span>
          <span class="h2 text-success ms-2">{{ $bundle->formatted_price }}</span>
          <span class="badge bg-success ms-2">{{ __('Save') }} {{ format_currency($bundle->savings, $bundle->currency) }} ({{ $bundle->savings_percentage }}%)</span>
        @else
          <span class="h2 text-success">{{ $bundle->formatted_price }}</span>
        @endif
      </div>

      @if($bundle->description)
        <p class="mb-4">{{ $bundle->description }}</p>
      @endif

      <div class="mb-4">
        @if(!$bundle->is_active)
          <div class="alert alert-warning">
            <strong>{{ __('Unavailable') }}</strong> {{ __('This bundle is currently not available for purchase.') }}
          </div>
        @elseif(!$bundle->hasStock())
          <div class="alert alert-warning">
            <strong>{{ __('Out of Stock') }}</strong> {{ __('One or more products in this bundle are out of stock.') }}
          </div>
        @else
          @auth
            @if($bundle->user && $bundle->user->id === Auth::id())
              <div class="mb-3">
                <a href="{{ route('admin.bundles.edit', $bundle) }}" class="btn btn-warning">
                  <i class="fas fa-edit me-1"></i>{{ __('Edit Bundle') }}
                </a>
              </div>
            @else
              <form action="{{ route('bundle.order.store') }}" method="POST" class="order-form">
                @csrf
                <input type="hidden" name="bundle_id" value="{{ $bundle->id }}">
                <div class="mb-3">
                  <label>{{ __('Note (optional)') }}</label>
                  <textarea name="note" class="form-control" rows="2"></textarea>
                </div>
                <button type="submit" class="btn btn-success btn-lg w-100 order-submit-btn">
                  <i class="fas fa-shopping-cart me-2"></i>{{ __('Order Bundle') }} - {{ $bundle->formatted_price }}
                </button>
              </form>
            @endif
          @else
            <p><a href="{{ route('login') }}">{{ __('Login') }}</a> {{ __('to place an order.') }}</p>
          @endauth
        @endif
      </div>
    </div>
  </div>

  <div class="mt-5">
    <h3 class="mb-4">{{ __('Products in this Bundle') }}</h3>
    <div class="row">
      @foreach($bundle->products as $product)
        <div class="col-md-4 mb-4">
          <div class="card h-100">
            @if($product->images->count() > 0)
              <img src="{{ $product->images->first()->url }}" class="card-img-top" alt="{{ $product->name }}"
                   style="height: 150px; object-fit: cover;">
            @else
              <div class="bg-light d-flex align-items-center justify-content-center" style="height: 150px;">
                <i class="fas fa-image fa-2x text-muted"></i>
              </div>
            @endif
            <div class="card-body">
              <h5 class="card-title">{{ $product->name }}</h5>
              <p class="card-text text-muted small">{{ \Illuminate\Support\Str::limit($product->description, 60) }}</p>
              <div class="d-flex justify-content-between align-items-center">
                @if($product->price > 0)
                  <span>{{ $product->formatted_price }}</span>
                @else
                  <span class="text-muted">{{ __('Bundle only') }}</span>
                @endif
                <span class="badge bg-secondary">x{{ $product->pivot->quantity }}</span>
              </div>
              @if(!$product->hasStock($product->pivot->quantity))
                <span class="badge bg-danger mt-2">{{ __('Out of Stock') }}</span>
              @endif
            </div>
            <div class="card-footer bg-transparent">
              <a href="{{ route('products.show', $product) }}" class="btn btn-outline-primary btn-sm w-100">
                {{ __('View Product') }}
              </a>
            </div>
          </div>
        </div>
      @endforeach
    </div>
  </div>
</div>
@endsection
