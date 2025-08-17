@extends('layouts.app')

@section('title', __('Products'))

@section('content')
<div class="container py-4">
  <!-- Header -->
  <div class="row mb-4">
    <div class="col-md-8">
      <h1>{{ __('Products') }}</h1>
      <p class="text-muted">{{ __('Discover our amazing collection of model trains') }}</p>
    </div>
    <div class="col-md-4 text-end">
      <div class="d-flex align-items-center justify-content-end">
        <span class="text-muted me-2">{{ $products->total() }} {{ __('results') }}</span>
        @if(request()->hasAny(['search', 'category', 'stock', 'min_price', 'max_price']))
          <a href="{{ route('products.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-times me-1"></i>{{ __('Clear Filters') }}
          </a>
        @endif
      </div>
    </div>
  </div>

  <div class="row">
    <!-- Filters Sidebar -->
    <div class="col-lg-3 mb-4">
      <div class="card">
        <div class="card-header">
          <h5 class="mb-0">
            <i class="fas fa-filter me-2"></i>{{ __('Filters & Search') }}
          </h5>
        </div>
        <div class="card-body">
          <form method="GET" action="{{ route('products.index') }}" id="filterForm">
            
            <!-- Search -->
            <div class="mb-3">
              <label for="search" class="form-label">{{ __('Search Products') }}</label>
              <div class="input-group">
                <input type="text" 
                       class="form-control" 
                       id="search" 
                       name="search" 
                       value="{{ request('search') }}" 
                        placeholder="{{ __('Search by name or description...') }}">
                <button type="submit" class="btn btn-outline-primary">
                  <i class="fas fa-search"></i>
                </button>
              </div>
            </div>

            <!-- Category Filter -->
            <div class="mb-3">
              <label for="category" class="form-label">{{ __('Category') }}</label>
              <select class="form-select" id="category" name="category" onchange="this.form.submit()">
                <option value="">{{ __('All Categories') }}</option>
                @foreach($categories as $category)
                  <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                    {{ $category->name }}
                  </option>
                @endforeach
              </select>
            </div>

            <!-- Stock Filter -->
            <div class="mb-3">
              <label for="stock" class="form-label">{{ __('Availability') }}</label>
              <select class="form-select" id="stock" name="stock" onchange="this.form.submit()">
                <option value="">{{ __('All Products') }}</option>
                <option value="in_stock" {{ request('stock') === 'in_stock' ? 'selected' : '' }}>{{ __('In Stock') }}</option>
                <option value="out_of_stock" {{ request('stock') === 'out_of_stock' ? 'selected' : '' }}>{{ __('Out of Stock') }}</option>
              </select>
            </div>

            <!-- Price Range -->
            <div class="mb-3">
              <label class="form-label">{{ __('Price Range') }}</label>
              <div class="row g-2">
                <div class="col-6">
                  <input type="number" 
                         class="form-control form-control-sm" 
                         name="min_price" 
                         value="{{ request('min_price') }}" 
                          placeholder="{{ __('Min $') }}"
                         min="0"
                         step="0.01">
                </div>
                <div class="col-6">
                  <input type="number" 
                         class="form-control form-control-sm" 
                         name="max_price" 
                         value="{{ request('max_price') }}" 
                          placeholder="{{ __('Max $') }}"
                         min="0"
                         step="0.01">
                </div>
              </div>
              @if($priceRange)
                <small class="text-muted">
                  {{ __('Range:') }} ${{ number_format($priceRange->min_price, 2) }} - ${{ number_format($priceRange->max_price, 2) }}
                </small>
              @endif
            </div>

            <!-- Apply Filters Button -->
            <button type="submit" class="btn btn-primary w-100">
              <i class="fas fa-filter me-1"></i>{{ __('Apply Filters') }}
            </button>

            <!-- Keep sort parameter -->
            <input type="hidden" name="sort" value="{{ request('sort', 'latest') }}">
          </form>
        </div>
      </div>
    </div>

    <!-- Products Grid -->
    <div class="col-lg-9">
      <!-- Sort Options -->
      <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="d-flex align-items-center">
          <label for="sort" class="form-label me-2 mb-0">{{ __('Sort by:') }}</label>
          <select class="form-select form-select-sm" id="sort" style="width: auto;" onchange="updateSort(this.value)">
            <option value="latest" {{ request('sort') === 'latest' ? 'selected' : '' }}>{{ __('Newest First') }}</option>
            <option value="oldest" {{ request('sort') === 'oldest' ? 'selected' : '' }}>{{ __('Oldest First') }}</option>
            <option value="price_low" {{ request('sort') === 'price_low' ? 'selected' : '' }}>{{ __('Price: Low to High') }}</option>
            <option value="price_high" {{ request('sort') === 'price_high' ? 'selected' : '' }}>{{ __('Price: High to Low') }}</option>
            <option value="name_asc" {{ request('sort') === 'name_asc' ? 'selected' : '' }}>{{ __('Name: A to Z') }}</option>
            <option value="name_desc" {{ request('sort') === 'name_desc' ? 'selected' : '' }}>{{ __('Name: Z to A') }}</option>
          </select>
        </div>
        
        <div class="btn-group btn-group-sm" role="group">
          <input type="radio" class="btn-check" name="view" id="gridView" autocomplete="off" checked>
          <label class="btn btn-outline-secondary" for="gridView">
            <i class="fas fa-th"></i>
          </label>
          <input type="radio" class="btn-check" name="view" id="listView" autocomplete="off">
          <label class="btn btn-outline-secondary" for="listView">
            <i class="fas fa-list"></i>
          </label>
        </div>
      </div>

      <!-- Products Results -->
      @if($products->count() > 0)
        <div class="row" id="productsGrid">
          @foreach($products as $product)
            <div class="col-md-6 col-xl-4 mb-4 product-item">
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
                  
                  <!-- Stock Badge -->
                  @if($product->isOutOfStock())
                    <span class="position-absolute top-0 end-0 m-2 badge bg-danger">Out of Stock</span>
                  @elseif($product->getStockStatus() === 'low_stock')
                    <span class="position-absolute top-0 end-0 m-2 badge bg-warning">Low Stock</span>
                  @endif
                </div>
                
                <div class="card-body d-flex flex-column">
                  <h5 class="card-title">{{ $product->name }}</h5>
                  <p class="card-text text-muted small flex-grow-1">
                    {{ \Illuminate\Support\Str::limit($product->description, 80) }}
                  </p>
                  
                  <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="h5 mb-0 text-primary">{{ $product->formatted_price }}</span>
                    @if($product->category)
                      <span class="badge bg-secondary">{{ $product->category->name }}</span>
                    @endif
                  </div>
                  <small class="text-muted mb-2">{{ __('By') }}: {{ $product->user->name ?? '-' }}</small>
                  
                  <div class="d-flex justify-content-between align-items-center">
                     <small class="text-muted">
                      <i class="fas fa-box me-1"></i>
                       {{ $product->no_of_items }} {{ __('in stock') }}
                    </small>
                    <small class="text-muted">
                      {{ $product->created_at->format('M d, Y') }}
                    </small>
                  </div>
                  
                  <div class="mt-3">
                    <a href="{{ route('products.show', $product) }}" class="btn btn-primary w-100">
                      <i class="fas fa-eye me-1"></i>{{ __('View Details') }}
                    </a>
                  </div>
                </div>
              </div>
            </div>
          @endforeach
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-center">
          {{ $products->links() }}
        </div>
      @else
        <!-- No Results -->
        <div class="text-center py-5">
          <i class="fas fa-search fa-4x text-muted mb-3"></i>
          <h3>{{ __('No products found') }}</h3>
          <p class="text-muted">{{ __('Try adjusting your search criteria or filters.') }}</p>
          <a href="{{ route('products.index') }}" class="btn btn-primary">
            <i class="fas fa-refresh me-1"></i>{{ __('Show All Products') }}
          </a>
        </div>
      @endif
    </div>
  </div>
</div>

<style>
.product-card {
  transition: all 0.3s ease;
  border: 1px solid #e0e6ed;
}

.product-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 8px 25px rgba(0,0,0,0.1);
  border-color: #007bff;
}

.product-item.list-view {
  flex: 0 0 100%;
  max-width: 100%;
}

.product-item.list-view .card {
  flex-direction: row;
}

.product-item.list-view .card-img-top {
  width: 200px;
  height: 150px;
  border-radius: 0.375rem 0 0 0.375rem;
}

.product-item.list-view .card-body {
  flex: 1;
}

@media (max-width: 768px) {
  .product-item.list-view .card {
    flex-direction: column;
  }
  
  .product-item.list-view .card-img-top {
    width: 100%;
    height: 200px;
    border-radius: 0.375rem 0.375rem 0 0;
  }
}
</style>

<script>
function updateSort(sortValue) {
  const url = new URL(window.location);
  url.searchParams.set('sort', sortValue);
  window.location.href = url.toString();
}

// View toggle functionality
document.getElementById('listView').addEventListener('change', function() {
  if (this.checked) {
    document.querySelectorAll('.product-item').forEach(item => {
      item.classList.add('list-view');
    });
  }
});

document.getElementById('gridView').addEventListener('change', function() {
  if (this.checked) {
    document.querySelectorAll('.product-item').forEach(item => {
      item.classList.remove('list-view');
    });
  }
});

// Auto-submit price filters on change
document.querySelector('input[name="min_price"]').addEventListener('change', function() {
  if (this.value) {
    document.getElementById('filterForm').submit();
  }
});

document.querySelector('input[name="max_price"]').addEventListener('change', function() {
  if (this.value) {
    document.getElementById('filterForm').submit();
  }
});
</script>
@endsection
