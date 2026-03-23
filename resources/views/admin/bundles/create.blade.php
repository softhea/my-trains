@extends('layouts.app')

@section('title', __('Create Bundle'))

@section('content')
<div class="container py-5">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h1>{{ __('Create New Bundle') }}</h1>
    <a href="{{ route('admin.bundles.index') }}" class="btn btn-secondary">
      <i class="fas fa-arrow-left me-1"></i> {{ __('Back to Bundles') }}
    </a>
  </div>

  @if ($errors->any())
    <div class="alert alert-danger">
      <ul class="mb-0">
        @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  @if($products->count() < 2)
    <div class="alert alert-warning">
      <h4>{{ __('Not enough products') }}</h4>
      <p>{{ __('You need at least 2 products available for bundles to create a bundle.') }}</p>
      <p>{{ __('Make sure your products have availability set to "Bundle only" or "Both standalone and bundle".') }}</p>
      <a href="{{ route('admin.products.index') }}" class="btn btn-primary">{{ __('Manage Products') }}</a>
    </div>
  @else
    <form action="{{ route('admin.bundles.store') }}" method="POST" enctype="multipart/form-data" id="bundleForm">
      @csrf

      <div class="row">
        <div class="col-md-8">
          <div class="card mb-4">
            <div class="card-header">
              <h5 class="mb-0">{{ __('Bundle Details') }}</h5>
            </div>
            <div class="card-body">
              <div class="mb-3">
                <label for="name" class="form-label">{{ __('Bundle Name') }} *</label>
                <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" 
                       value="{{ old('name') }}" required>
                @error('name')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>

              <div class="mb-3">
                <label for="description" class="form-label">{{ __('Description') }}</label>
                <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror" 
                          rows="3">{{ old('description') }}</textarea>
                @error('description')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>

              <div class="row">
                <div class="col-md-6">
                  <div class="mb-3">
                    <label for="price" class="form-label">{{ __('Bundle Price') }} *</label>
                    <input type="number" name="price" id="price" class="form-control @error('price') is-invalid @enderror" 
                           value="{{ old('price') }}" step="0.01" min="0" required>
                    @error('price')
                      <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="mb-3">
                    <label for="currency" class="form-label">{{ __('Currency') }} *</label>
                    <select name="currency" id="currency" class="form-select @error('currency') is-invalid @enderror" required>
                      @foreach(\App\Models\Product::getAvailableCurrencies() as $code => $name)
                        <option value="{{ $code }}" {{ old('currency', 'RON') === $code ? 'selected' : '' }}>
                          {{ $name }}
                        </option>
                      @endforeach
                    </select>
                    @error('currency')
                      <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                  </div>
                </div>
              </div>

              <div class="mb-3">
                <div class="form-check form-switch">
                  <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" 
                         {{ old('is_active', true) ? 'checked' : '' }}>
                  <label class="form-check-label" for="is_active">{{ __('Active (visible to customers)') }}</label>
                </div>
              </div>

              <div class="mb-3">
                <label for="images" class="form-label">{{ __('Bundle Images (Optional)') }}</label>
                <input type="file" name="images[]" id="images" class="form-control" multiple accept="image/*">
                <div class="form-text">{{ __('If no images are uploaded, the first product image will be used.') }}</div>
              </div>
            </div>
          </div>

          <div class="card">
            <div class="card-header">
              <h5 class="mb-0">{{ __('Select Products') }} * <small class="text-muted">({{ __('minimum 2') }})</small></h5>
            </div>
            <div class="card-body">
              <div class="table-responsive">
                <table class="table table-hover" id="productsTable">
                  <thead>
                    <tr>
                      <th style="width: 50px;">{{ __('Select') }}</th>
                      <th style="width: 60px;">{{ __('Image') }}</th>
                      <th>{{ __('Product') }}</th>
                      <th style="width: 100px;">{{ __('Price') }}</th>
                      <th style="width: 100px;">{{ __('Stock') }}</th>
                      <th style="width: 100px;">{{ __('Quantity') }}</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach($products as $product)
                      <tr>
                        <td>
                          <div class="form-check">
                            <input class="form-check-input product-checkbox" type="checkbox" 
                                   name="products[]" value="{{ $product->id }}" 
                                   id="product{{ $product->id }}"
                                   data-price="{{ $product->price }}"
                                   {{ in_array($product->id, old('products', [])) ? 'checked' : '' }}>
                          </div>
                        </td>
                        <td>
                          @if($product->images->count() > 0)
                            <img src="{{ $product->images->first()->url }}" 
                                 alt="{{ $product->name }}" 
                                 style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px;">
                          @else
                            <div class="bg-light d-flex align-items-center justify-content-center" 
                                 style="width: 50px; height: 50px; border-radius: 4px;">
                              <small class="text-muted">-</small>
                            </div>
                          @endif
                        </td>
                        <td>
                          <label for="product{{ $product->id }}" class="mb-0" style="cursor: pointer;">
                            <strong>{{ $product->name }}</strong>
                          </label>
                        </td>
                        <td>{{ $product->price > 0 ? $product->formatted_price : '-' }}</td>
                        <td>
                          <span class="badge bg-{{ $product->no_of_items > 0 ? 'success' : 'danger' }}">
                            {{ $product->no_of_items }}
                          </span>
                        </td>
                        <td>
                          <input type="number" name="quantities[{{ $product->id }}]" 
                                 class="form-control form-control-sm quantity-input" 
                                 value="{{ old('quantities.' . $product->id, 1) }}" 
                                 min="1" max="{{ $product->no_of_items }}"
                                 data-product-id="{{ $product->id }}"
                                 data-price="{{ $product->price }}"
                                 {{ in_array($product->id, old('products', [])) ? '' : 'disabled' }}>
                        </td>
                      </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>

        <div class="col-md-4">
          <div class="card sticky-top" style="top: 20px;">
            <div class="card-header bg-primary text-white">
              <h5 class="mb-0">{{ __('Bundle Summary') }}</h5>
            </div>
            <div class="card-body">
              <div class="mb-3">
                <label class="text-muted">{{ __('Products Selected') }}</label>
                <h4 id="selectedCount">0</h4>
              </div>
              <hr>
              <div class="mb-3">
                <label class="text-muted">{{ __('Total Products Value') }}</label>
                <h4 id="totalValue" class="text-decoration-line-through text-muted">0.00</h4>
              </div>
              <div class="mb-3">
                <label class="text-muted">{{ __('Bundle Price') }}</label>
                <h4 id="bundlePrice" class="text-primary">0.00</h4>
              </div>
              <hr>
              <div class="mb-3">
                <label class="text-muted">{{ __('Customer Savings') }}</label>
                <h3 id="savings" class="text-success">0.00 (0%)</h3>
              </div>
              <button type="submit" class="btn btn-primary w-100" id="submitBtn" disabled>
                <i class="fas fa-save me-1"></i> {{ __('Create Bundle') }}
              </button>
            </div>
          </div>
        </div>
      </div>
    </form>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
      const checkboxes = document.querySelectorAll('.product-checkbox');
      const quantityInputs = document.querySelectorAll('.quantity-input');
      const priceInput = document.getElementById('price');
      const submitBtn = document.getElementById('submitBtn');
      
      function updateSummary() {
        let selectedCount = 0;
        let totalValue = 0;
        
        checkboxes.forEach(checkbox => {
          if (checkbox.checked) {
            selectedCount++;
            const productId = checkbox.value;
            const quantityInput = document.querySelector(`input[name="quantities[${productId}]"]`);
            const quantity = parseInt(quantityInput.value) || 1;
            const price = parseFloat(checkbox.dataset.price) || 0;
            totalValue += price * quantity;
          }
        });
        
        const bundlePrice = parseFloat(priceInput.value) || 0;
        const savings = totalValue - bundlePrice;
        const savingsPercent = totalValue > 0 ? ((savings / totalValue) * 100).toFixed(1) : 0;
        
        document.getElementById('selectedCount').textContent = selectedCount;
        document.getElementById('totalValue').textContent = totalValue.toFixed(2);
        document.getElementById('bundlePrice').textContent = bundlePrice.toFixed(2);
        document.getElementById('savings').textContent = `${savings.toFixed(2)} (${savingsPercent}%)`;
        document.getElementById('savings').className = savings >= 0 ? 'text-success' : 'text-danger';
        
        submitBtn.disabled = selectedCount < 2;
      }
      
      checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
          const productId = this.value;
          const quantityInput = document.querySelector(`input[name="quantities[${productId}]"]`);
          quantityInput.disabled = !this.checked;
          if (!this.checked) {
            quantityInput.value = 1;
          }
          updateSummary();
        });
      });
      
      quantityInputs.forEach(input => {
        input.addEventListener('input', updateSummary);
      });
      
      priceInput.addEventListener('input', updateSummary);
      
      updateSummary();
    });
    </script>
  @endif
</div>
@endsection
