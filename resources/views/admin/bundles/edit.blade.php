@extends('layouts.app')

@section('title', __('Edit Bundle'))

@section('content')
<div class="container py-5">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h1>{{ __('Edit Bundle') }}: {{ $bundle->name }}</h1>
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

  <form action="{{ route('admin.bundles.update', $bundle) }}" method="POST" enctype="multipart/form-data" id="bundleForm">
    @csrf
    @method('PUT')

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
                     value="{{ old('name', $bundle->name) }}" required>
              @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div class="mb-3">
              <label for="description" class="form-label">{{ __('Description') }}</label>
              <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror" 
                        rows="3">{{ old('description', $bundle->description) }}</textarea>
              @error('description')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div class="row">
              <div class="col-md-6">
                <div class="mb-3">
                  <label for="price" class="form-label">{{ __('Bundle Price') }} *</label>
                  <input type="number" name="price" id="price" class="form-control @error('price') is-invalid @enderror" 
                         value="{{ old('price', $bundle->price) }}" step="0.01" min="0" required>
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
                      <option value="{{ $code }}" {{ old('currency', $bundle->currency) === $code ? 'selected' : '' }}>
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
                       {{ old('is_active', $bundle->is_active) ? 'checked' : '' }}>
                <label class="form-check-label" for="is_active">{{ __('Active (visible to customers)') }}</label>
              </div>
            </div>

            @if($bundle->images->count() > 0)
              <div class="mb-3">
                <label class="form-label">{{ __('Current Images') }}</label>
                <div class="row">
                  @foreach($bundle->images as $image)
                    <div class="col-md-3 mb-2">
                      <div class="position-relative">
                        <img src="{{ $image->url }}" alt="Bundle image" 
                             class="img-fluid rounded" style="height: 100px; object-fit: cover; width: 100%;">
                        <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0 m-1"
                                onclick="deleteBundleImage({{ $image->id }})">
                          <i class="fas fa-times"></i>
                        </button>
                      </div>
                    </div>
                  @endforeach
                </div>
              </div>
            @endif

            <div class="mb-3">
              <label for="images" class="form-label">{{ __('Add New Images (Optional)') }}</label>
              <input type="file" name="images[]" id="images" class="form-control" multiple accept="image/*">
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
                    @php
                      $isSelected = isset($selectedProducts[$product->id]);
                      $quantity = $selectedProducts[$product->id] ?? 1;
                    @endphp
                    <tr>
                      <td>
                        <div class="form-check">
                          <input class="form-check-input product-checkbox" type="checkbox" 
                                 name="products[]" value="{{ $product->id }}" 
                                 id="product{{ $product->id }}"
                                 data-price="{{ $product->price }}"
                                 {{ $isSelected ? 'checked' : '' }}>
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
                               value="{{ old('quantities.' . $product->id, $quantity) }}" 
                               min="1" max="{{ $product->no_of_items }}"
                               data-product-id="{{ $product->id }}"
                               data-price="{{ $product->price }}"
                               {{ $isSelected ? '' : 'disabled' }}>
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
            <button type="submit" class="btn btn-primary w-100" id="submitBtn">
              <i class="fas fa-save me-1"></i> {{ __('Update Bundle') }}
            </button>
          </div>
        </div>

        <div class="card mt-3">
          <div class="card-body">
            <h5 class="card-title text-danger">{{ __('Danger Zone') }}</h5>
            <p class="text-muted small">{{ __('Delete this bundle permanently.') }}</p>
            <button type="button" class="btn btn-danger btn-sm w-100" data-bs-toggle="modal" data-bs-target="#deleteModal">
              <i class="fas fa-trash me-1"></i> {{ __('Delete Bundle') }}
            </button>
          </div>
        </div>
      </div>
    </div>
  </form>

  <!-- Delete Modal -->
  <div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header bg-danger text-white">
          <h5 class="modal-title">{{ __('Confirm Delete') }}</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <p>{{ __('Are you sure you want to delete the bundle') }} <strong>{{ $bundle->name }}</strong>?</p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
          <form method="POST" action="{{ route('admin.bundles.destroy', $bundle) }}" class="d-inline">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger">{{ __('Delete') }}</button>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

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

function deleteBundleImage(imageId) {
  if (!confirm('{{ __("Are you sure you want to delete this image?") }}')) {
    return;
  }
  
  fetch(`/admin/bundles/images/${imageId}`, {
    method: 'DELETE',
    headers: {
      'X-CSRF-TOKEN': '{{ csrf_token() }}',
      'Accept': 'application/json'
    }
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      location.reload();
    } else {
      alert(data.error || '{{ __("Failed to delete image") }}');
    }
  })
  .catch(error => {
    console.error('Error:', error);
    alert('{{ __("Failed to delete image") }}');
  });
}
</script>
@endsection
