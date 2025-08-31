@extends('layouts.app')

@section('title', 'Edit Product')

@section('content')
<div class="container py-5">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Edit Product: {{ $product->name }}</h1>
    <div>
      <a href="{{ route('products.show', $product) }}" class="btn btn-outline-info" target="_blank">View Product</a>
      <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">Back to Products</a>
    </div>
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

  <div class="row">
    <div class="col-md-8">
      <div class="card">
        <div class="card-body">
          <form action="{{ route('admin.products.update', $product) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="mb-3">
              <label for="name" class="form-label">Product Name</label>
              <input type="text" 
                     name="name" 
                     id="name"
                     class="form-control @error('name') is-invalid @enderror" 
                     value="{{ old('name', $product->name) }}"
                     required>
              @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div class="mb-3">
              <label for="description" class="form-label">Description</label>
              <textarea name="description" 
                        id="description"
                        class="form-control @error('description') is-invalid @enderror" 
                        rows="4"
                        required>{{ old('description', $product->description) }}</textarea>
              @error('description')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div class="row">
              <div class="col-md-3">
                <div class="mb-3">
                  <label for="price" class="form-label">Price</label>
                  <input type="number" 
                         name="price" 
                         id="price"
                         class="form-control @error('price') is-invalid @enderror" 
                         value="{{ old('price', $product->price) }}"
                         step="0.01"
                         min="0"
                         required>
                  @error('price')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
              </div>
              <div class="col-md-3">
                <div class="mb-3">
                  <label for="currency" class="form-label">Currency</label>
                  <select name="currency" 
                          id="currency"
                          class="form-select @error('currency') is-invalid @enderror" 
                          required>
                    @foreach(\App\Models\Product::getAvailableCurrencies() as $code => $name)
                      <option value="{{ $code }}" {{ old('currency', $product->currency) === $code ? 'selected' : '' }}>
                        {{ $name }}
                      </option>
                    @endforeach
                  </select>
                  @error('currency')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
              </div>
              <div class="col-md-3">
                <div class="mb-3">
                  <label for="no_of_items" class="form-label">Stock (Items Available)</label>
                  <input type="number" 
                         name="no_of_items" 
                         id="no_of_items"
                         class="form-control @error('no_of_items') is-invalid @enderror" 
                         value="{{ old('no_of_items', $product->no_of_items) }}"
                         min="0"
                         required>
                  @error('no_of_items')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
              </div>
              <div class="col-md-3">
                <div class="mb-3">
                  <label for="category_id" class="form-label">Category</label>
                  <select name="category_id" id="category_id" class="form-select @error('category_id') is-invalid @enderror" required>
                    <option value="">Select a category</option>
                    @foreach ($categories as $category)
                      <option value="{{ $category->id }}" 
                              {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                      </option>
                    @endforeach
                  </select>
                  @error('category_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
              </div>
            </div>

            <!-- Existing Images -->
            @if($product->images->count() > 0)
              <div class="mb-3">
                <label class="form-label">
                  Current Images 
                  <small class="text-muted">(Click to view full size)</small>
                </label>
                <div class="row">
                  @foreach($product->images as $index => $image)
                    <div class="col-md-3 mb-3">
                      <div class="card position-relative">
                        <img src="{{ $image->url }}" 
                             class="card-img-top product-image clickable-image" 
                             style="height: 200px; object-fit: cover; cursor: pointer;" 
                             alt="Product Image {{ $index + 1 }}"
                             data-index="{{ $index }}"
                             onclick="openImageModal({{ $index }})">
                        
                        <!-- Zoom indicator -->
                        <div class="zoom-indicator position-absolute top-50 start-50 translate-middle" 
                             style="background: rgba(0,0,0,0.7); color: white; padding: 0.5rem; border-radius: 50%; opacity: 0; transition: opacity 0.3s;">
                          <i class="fas fa-search-plus"></i>
                        </div>
                        
                        <div class="card-body p-2">
                          <button type="button" 
                                  class="btn btn-sm btn-danger w-100" 
                                  onclick="deleteProductImage({{ $image->id }})"
                                  title="Delete this image">
                            <i class="fas fa-trash me-1"></i>
                            Delete
                          </button>
                        </div>
                      </div>
                    </div>
                  @endforeach
                </div>
              </div>
            @endif

            <!-- Add New Images -->
            <div class="mb-3">
              <label for="images" class="form-label">Add New Images (Optional)</label>
              <input type="file" 
                     name="images[]" 
                     id="images"
                     class="form-control @error('images.*') is-invalid @enderror" 
                     multiple 
                     accept="image/*">
              @error('images.*')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
              <div class="form-text">You can upload additional images. Maximum size: 8MB per image.</div>
            </div>

            <!-- YouTube Videos -->
            <div class="mb-3">
              <label for="videos" class="form-label">YouTube Video URLs (Optional)</label>
              <textarea name="videos" 
                        id="videos"
                        class="form-control @error('videos') is-invalid @enderror" 
                        rows="5"
                        placeholder="Enter YouTube URLs, one per line:&#10;https://www.youtube.com/watch?v=VIDEO_ID&#10;https://youtu.be/VIDEO_ID&#10;https://www.youtube.com/embed/VIDEO_ID">{{ old('videos', $product->videos->pluck('url')->implode("\n")) }}</textarea>
              @error('videos')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
              <div class="form-text">
                <strong>Supported formats:</strong><br>
                • https://www.youtube.com/watch?v=VIDEO_ID<br>
                • https://youtu.be/VIDEO_ID<br>
                • https://www.youtube.com/embed/VIDEO_ID<br>
                Enter one URL per line. Leave empty to remove all videos.
              </div>
            </div>

            <div class="d-flex gap-2">
              <button type="submit" class="btn btn-primary">Update Product</button>
              <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary">Cancel</a>
            </div>
          </form>
        </div>
      </div>
    </div>
    
    <div class="col-md-4">
      <div class="card bg-light">
        <div class="card-body">
          <h5 class="card-title">📊 Product Stats</h5>
          <ul class="list-unstyled">
            <li class="mb-2">
              <strong>Stock:</strong> 
              <span class="badge bg-{{ $product->getStockStatus() === 'out_of_stock' ? 'danger' : ($product->getStockStatus() === 'low_stock' ? 'warning' : 'success') }}">
                {{ $product->no_of_items }} items
              </span>
            </li>
            <li class="mb-2">
              <strong>Images:</strong> {{ $product->images->count() }} uploaded
            </li>
            <li class="mb-2">
              <strong>Videos:</strong> {{ $product->videos->count() }} YouTube videos
            </li>
            <li class="mb-2">
              <strong>Category:</strong> {{ $product->category->name ?? 'None' }}
            </li>
            <li class="mb-2">
              <strong>Created:</strong> {{ $product->created_at->format('M d, Y') }}
            </li>
            <li class="mb-2">
              <strong>Updated:</strong> {{ $product->updated_at->format('M d, Y') }}
            </li>
          </ul>
        </div>
      </div>

      <div class="card mt-3">
        <div class="card-body">
          <h5 class="card-title">⚠️ Danger Zone</h5>
          <p class="text-muted">Permanently delete this product and all its associated data.</p>
          <form method="POST" action="{{ route('admin.products.destroy', $product) }}" onsubmit="return confirm('Are you sure you want to delete this product? This action cannot be undone and will remove all images, videos, and related data.')">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger btn-sm w-100">
              Delete Product
            </button>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>


<style>
/* Product image hover effects */
.product-image {
  transition: all 0.3s ease;
}

.product-image:hover {
  transform: scale(1.02);
  box-shadow: 0 4px 15px rgba(0,0,0,0.2);
}

.product-image:hover + .zoom-indicator {
  opacity: 1 !important;
}

.clickable-image {
  position: relative;
}

.zoom-indicator {
  pointer-events: none;
}

/* Modal enhancements */
#imageModal .modal-body {
  background: rgba(0,0,0,0.9);
}

#imageModal .btn {
  backdrop-filter: blur(10px);
}

.modal-thumbnail:hover {
  opacity: 1 !important;
  transform: scale(1.1);
  border: 2px solid #ffc107 !important;
}
</style>

<!-- Image Lightbox Modal -->
@if($product->images->count() > 0)
<div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-fullscreen">
    <div class="modal-content bg-dark">
      <div class="modal-header border-0">
        <h5 class="modal-title text-white" id="imageModalLabel">
          <i class="fas fa-images me-2"></i>
          Product Images
          <span id="imageCounter" class="badge bg-primary ms-2"></span>
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      
      <div class="modal-body d-flex align-items-center justify-content-center p-0" onclick="closeModal()" style="cursor: pointer;">
        <button id="prevImage" class="btn btn-light position-absolute start-0 top-50 translate-middle-y ms-3" style="z-index: 1000;" onclick="event.stopPropagation();">
          <i class="fas fa-chevron-left"></i>
        </button>
        
        <img id="modalImage" 
             src="" 
             alt="Product Image" 
             class="img-fluid"
             style="max-width: 90%; max-height: 90vh; object-fit: contain;"
             onclick="event.stopPropagation();">
        
        <button id="nextImage" class="btn btn-light position-absolute end-0 top-50 translate-middle-y me-3" style="z-index: 1000;" onclick="event.stopPropagation();">
          <i class="fas fa-chevron-right"></i>
        </button>
      </div>
      
      <div class="modal-footer border-0 justify-content-center">
        <div class="d-flex gap-2 flex-wrap" id="imageThumbnails">
          @foreach($product->images as $index => $image)
            <img src="{{ $image->url }}" 
                 class="modal-thumbnail" 
                 style="width: 60px; height: 60px; object-fit: cover; cursor: pointer; border-radius: 4px; opacity: 0.6; transition: all 0.3s;"
                 onclick="setModalImage({{ $index }}); event.stopPropagation();"
                 alt="Thumbnail {{ $index + 1 }}">
          @endforeach
        </div>
      </div>
    </div>
  </div>
</div>

<script>
// Initialize product images for lightbox
window.productImages = [
  @foreach($product->images as $image)
    "{{ $image->url }}"{{ !$loop->last ? ',' : '' }}
  @endforeach
];

let currentImageIndex = 0;
let modalInstance = null;

function openImageModal(index = 0) {
  currentImageIndex = index;
  updateModalImage(index);
  
  const modalElement = document.getElementById('imageModal');
  modalInstance = bootstrap.Modal.getOrCreateInstance(modalElement);
  modalInstance.show();
}

function setModalImage(index) {
  currentImageIndex = index;
  updateModalImage(index);
}

function updateModalImage(index) {
  if (index < 0) index = window.productImages.length - 1;
  if (index >= window.productImages.length) index = 0;
  
  currentImageIndex = index;
  
  const modalImage = document.getElementById('modalImage');
  const imageCounter = document.getElementById('imageCounter');
  const thumbnails = document.querySelectorAll('.modal-thumbnail');
  
  // Update main image
  modalImage.src = window.productImages[index];
  
  // Update counter
  if (imageCounter) {
    imageCounter.textContent = `${index + 1} / ${window.productImages.length}`;
  }
  
  // Update thumbnail highlighting
  thumbnails.forEach((thumb, i) => {
    thumb.style.opacity = i === index ? '1' : '0.6';
    thumb.style.border = i === index ? '3px solid #007bff' : 'none';
  });
}

// Event listeners for navigation
document.addEventListener('DOMContentLoaded', function() {
  const prevBtn = document.getElementById('prevImage');
  const nextBtn = document.getElementById('nextImage');
  
  if (prevBtn) {
    prevBtn.addEventListener('click', function() {
      updateModalImage(currentImageIndex - 1);
    });
  }
  
  if (nextBtn) {
    nextBtn.addEventListener('click', function() {
      updateModalImage(currentImageIndex + 1);
    });
  }
  
  // Keyboard navigation
  document.addEventListener('keydown', function(e) {
    const modal = document.getElementById('imageModal');
    if (modal && modal.classList.contains('show')) {
      if (e.key === 'ArrowLeft') {
        e.preventDefault();
        updateModalImage(currentImageIndex - 1);
      } else if (e.key === 'ArrowRight') {
        e.preventDefault();
        updateModalImage(currentImageIndex + 1);
      } else if (e.key === 'Escape') {
        e.preventDefault();
        closeModal();
      }
    }
  });
  
  // Modal event listeners for proper cleanup
  const modal = document.getElementById('imageModal');
  if (modal) {
    modal.addEventListener('hidden.bs.modal', function() {
      // Ensure all backdrops are removed
      const backdrops = document.querySelectorAll('.modal-backdrop');
      backdrops.forEach(backdrop => backdrop.remove());
      
      // Reset body styles
      document.body.classList.remove('modal-open');
      document.body.style.overflow = '';
      document.body.style.paddingRight = '';
    });
    
    modal.addEventListener('hide.bs.modal', function() {
      // Additional cleanup if needed
      modalInstance = null;
    });
  }
});

// Helper function to properly close modal
function closeModal() {
  if (modalInstance) {
    modalInstance.hide();
  } else {
    const modal = document.getElementById('imageModal');
    const instance = bootstrap.Modal.getInstance(modal);
    if (instance) {
      instance.hide();
    }
  }
}
</script>
@endif

@endsection
