@props([
    'action' => '',
    'categories' => collect(),
    'priceRange' => null,
    'compact' => false
])

<div class="card">
  <div class="card-header">
    <h5 class="mb-0">
      <i class="fas fa-search me-2"></i>Search Products
    </h5>
  </div>
  <div class="card-body">
    <form method="GET" action="{{ $action }}" class="product-search-form">
      
      <!-- Search Input -->
      <div class="mb-3">
        <div class="input-group">
          <input type="text" 
                 class="form-control" 
                 name="search" 
                 value="{{ request('search') }}" 
                 placeholder="Search products..."
                 autocomplete="off">
          <button type="submit" class="btn btn-primary">
            <i class="fas fa-search"></i>
          </button>
        </div>
      </div>

      @unless($compact)
        <!-- Quick Filters Row -->
        <div class="row g-2 mb-3">
          <div class="col-md-6">
            <select class="form-select form-select-sm" name="category" onchange="this.form.submit()">
              <option value="">All Categories</option>
              @foreach($categories as $category)
                <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                  {{ $category->name }}
                </option>
              @endforeach
            </select>
          </div>
          <div class="col-md-6">
            <select class="form-select form-select-sm" name="stock" onchange="this.form.submit()">
              <option value="">All Stock</option>
              <option value="in_stock" {{ request('stock') === 'in_stock' ? 'selected' : '' }}>In Stock</option>
              <option value="out_of_stock" {{ request('stock') === 'out_of_stock' ? 'selected' : '' }}>Out of Stock</option>
            </select>
          </div>
        </div>

        <!-- Price Range -->
        <div class="row g-2 mb-3">
          <div class="col-6">
            <input type="number" 
                   class="form-control form-control-sm" 
                   name="min_price" 
                   value="{{ request('min_price') }}" 
                   placeholder="Min Price"
                   step="0.01"
                   min="0">
          </div>
          <div class="col-6">
            <input type="number" 
                   class="form-control form-control-sm" 
                   name="max_price" 
                   value="{{ request('max_price') }}" 
                   placeholder="Max Price"
                   step="0.01"
                   min="0">
          </div>
        </div>
      @endunless

      <!-- Hidden sort parameter -->
      <input type="hidden" name="sort" value="{{ request('sort', 'latest') }}">
      
      <!-- Clear Filters -->
      @if(request()->hasAny(['search', 'category', 'stock', 'min_price', 'max_price']))
        <div class="text-center">
          <a href="{{ $action }}" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-times me-1"></i>Clear All
          </a>
        </div>
      @endif
    </form>
  </div>
</div>
