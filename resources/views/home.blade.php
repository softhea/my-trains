@extends('layouts.app')

@section('title', __('Home'))

@section('content')
<style>
  /* Reduce top spacing for the latest products on homepage */
  #latest-products { padding-top: 1.25rem !important; }
  @media (min-width: 992px) {
    #latest-products { padding-top: 2rem !important; }
  }
  /* Collapse the empty hero area */
  #hero { display: none; }
  /* Slightly tighten popular section gap to match */
  #popular-products { padding-top: 2.5rem; }
  @media (min-width: 992px) {
    #popular-products { padding-top: 3rem; }
  }
  </style>

<section id="latest-products" class="container pb-5">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h2>{{ __('Latest Products') }}</h2>
    <a href="{{ route('products.index') }}" class="btn btn-outline-primary">
      <i class="fas fa-shopping-bag me-1"></i>{{ __('View All Products') }}
    </a>
  </div>
  <div class="row">
    @foreach ($latestProducts as $product)
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
               <span class="position-absolute top-0 end-0 m-2 badge bg-warning">{{ __('Low Stock') }}</span>
            @endif
          </div>
          <div class="card-body d-flex flex-column">
            <h5 class="card-title">{{ $product->name }}</h5>
            <p class="card-text text-muted flex-grow-1">{{ \Illuminate\Support\Str::limit($product->description, 80) }}</p>
            <div class="d-flex justify-content-between align-items-center mb-2">
              <span class="h5 mb-0 text-primary">{{ $product->formatted_price }}</span>
              @if($product->category)
                <span class="badge bg-secondary">{{ $product->category->name }}</span>
              @endif
            </div>
            <small class="text-muted mb-2">{{ __('By') }}: {{ $product->user->name ?? '-' }}</small>
             <small class="text-muted mb-3">
              <i class="fas fa-box me-1"></i>{{ $product->no_of_items }} in stock
            </small>
            <div class="d-flex gap-2">
              <a href="{{ route('products.show', $product) }}" class="btn btn-primary flex-grow-1">
                <i class="fas fa-eye me-1"></i>{{ __('View Details') }}
              </a>
              @auth
                @if($product->user_id === Auth::id())
                  <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-outline-secondary">
                    <i class="fas fa-edit"></i>
                  </a>
                @endif
              @endauth
            </div>
          </div>
        </div>
      </div>
    @endforeach
  </div>
</section>

@if($bundles->count() > 0)
<section id="bundles" class="container pb-5">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-box-open text-warning me-2"></i>{{ __('Special Bundles') }}</h2>
    <a href="{{ route('products.index') }}" class="btn btn-outline-warning">
      <i class="fas fa-shopping-bag me-1"></i>{{ __('View All') }}
    </a>
  </div>
  <div class="row">
    @foreach ($bundles as $bundle)
      <div class="col-md-4 mb-4">
        <div class="card h-100 border-warning">
          <div class="position-relative">
            @php
              $bundleImage = $bundle->images->first() ?? $bundle->products->first()?->images->first();
            @endphp
            @if($bundleImage)
              <img src="{{ $bundleImage->url }}" 
                   class="card-img-top" 
                   alt="{{ $bundle->name }}"
                   style="height: 200px; object-fit: cover;">
            @else
              <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 200px;">
                <i class="fas fa-box-open fa-3x text-muted"></i>
              </div>
            @endif
            <span class="position-absolute top-0 start-0 m-2 badge bg-warning text-dark">
              <i class="fas fa-box-open me-1"></i>{{ __('Bundle') }}
            </span>
            @if($bundle->has_meaningful_savings)
              <span class="position-absolute top-0 end-0 m-2 badge bg-success">
                {{ __('Save') }} {{ $bundle->savings_percentage }}%
              </span>
            @endif
          </div>
          <div class="card-body d-flex flex-column">
            <h5 class="card-title">{{ $bundle->name }}</h5>
            <p class="card-text text-muted flex-grow-1">
              {{ $bundle->products->count() }} {{ __('products') }}
              @if($bundle->description)
                - {{ \Illuminate\Support\Str::limit($bundle->description, 60) }}
              @endif
            </p>
            <div class="d-flex justify-content-between align-items-center mb-2">
              <div>
                @if($bundle->has_meaningful_savings)
                  <span class="text-decoration-line-through text-muted small">{{ format_currency($bundle->total_products_value, $bundle->currency) }}</span>
                @endif
                <span class="h5 mb-0 text-success {{ $bundle->has_meaningful_savings ? 'ms-1' : '' }}">{{ $bundle->formatted_price }}</span>
              </div>
            </div>
            <small class="text-muted mb-3">{{ __('By') }}: {{ $bundle->user->name ?? '-' }}</small>
            <div class="d-flex gap-2">
              <a href="{{ route('bundles.show', $bundle) }}" class="btn btn-warning flex-grow-1">
                <i class="fas fa-eye me-1"></i>{{ __('View Bundle') }}
              </a>
              @auth
                @if($bundle->user_id === Auth::id())
                  <a href="{{ route('admin.bundles.edit', $bundle) }}" class="btn btn-outline-secondary">
                    <i class="fas fa-edit"></i>
                  </a>
                @endif
              @endauth
            </div>
          </div>
        </div>
      </div>
    @endforeach
  </div>
</section>
@endif

<section id="popular-products" class="container pb-5">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h2>{{ __('Most Popular Products') }}</h2>
    <a href="{{ route('products.index') }}" class="btn btn-outline-primary">
      <i class="fas fa-shopping-bag me-1"></i>{{ __('View All Products') }}
    </a>
  </div>
  <div class="row">
    @forelse ($popularProducts as $product)
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
          </div>
          <div class="card-body d-flex flex-column">
            <h5 class="card-title">{{ $product->name }}</h5>
            <p class="card-text text-muted flex-grow-1">{{ \Illuminate\Support\Str::limit($product->description, 80) }}</p>
            <div class="d-flex justify-content-between align-items-center mb-2">
              <span class="h5 mb-0 text-primary">{{ $product->formatted_price }}</span>
              @if($product->category)
                <span class="badge bg-secondary">{{ $product->category->name }}</span>
              @endif
            </div>
            <small class="text-muted mb-2">{{ __('By') }}: {{ $product->user->name ?? '-' }}</small>
            <small class="text-muted mb-3">
              <i class="fas fa-eye me-1"></i>{{ $product->views_count }} {{ __('views') }}
            </small>
            <div class="d-flex gap-2">
              <a href="{{ route('products.show', $product) }}" class="btn btn-primary flex-grow-1">
                <i class="fas fa-eye me-1"></i>{{ __('View Details') }}
              </a>
              @auth
                @if($product->user_id === Auth::id())
                  <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-outline-secondary">
                    <i class="fas fa-edit"></i>
                  </a>
                @endif
              @endauth
            </div>
          </div>
        </div>
      </div>
    @empty
      <div class="col-12 text-center text-muted">{{ __('No products found') }}</div>
    @endforelse
  </div>
</section>
@endsection