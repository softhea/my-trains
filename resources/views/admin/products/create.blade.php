@extends('layouts.app')

@section('title', __('Add Product'))

@section('content')
<div class="container py-5">
  <h1 class="mb-4">{{ __('Create New Product') }}</h1>

  @if ($errors->any())
    <div class="alert alert-danger">
      <ul class="mb-0">
        @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data" id="productForm">
    @csrf

    <div class="mb-3">
      <label>{{ __('Name') }}</label>
      <input type="text" name="name" class="form-control" required>
    </div>

    <div class="mb-3">
      <label>{{ __('Description') }}</label>
      <textarea name="description" class="form-control" rows="4"></textarea>
    </div>

    <div class="row">
      <div class="col-md-4">
        <div class="mb-3">
          <label>{{ __('Price') }}</label>
          <input type="number" name="price" class="form-control" step="0.01" required>
        </div>
      </div>
      <div class="col-md-4">
        <div class="mb-3">
          <label>{{ __('Currency') }}</label>
          <select name="currency" class="form-select" required>
            @foreach(\App\Models\Product::getAvailableCurrencies() as $code => $name)
              <option value="{{ $code }}" {{ old('currency', 'RON') === $code ? 'selected' : '' }}>
                {{ $name }}
              </option>
            @endforeach
          </select>
        </div>
      </div>
      <div class="col-md-4">
        <div class="mb-3">
          <label>{{ __('Stock (Number of Items)') }}</label>
          <input type="number" name="no_of_items" class="form-control" min="0" value="0" required>
        </div>
      </div>
    </div>

    <div class="mb-3">
      <label>{{ __('Category') }}</label>
      <select name="category_id" class="form-control" required style="font-family: monospace;">
        <option value="">{{ __('Select a category') }}</option>
        @foreach ($categories as $category)
          <option value="{{ $category->id }}" 
                  @if($category->parent_id) style="color: #666;" @endif>
            {{ $category->name }}
          </option>
        @endforeach
      </select>
      <div class="form-text">
        <small>{{ __('Categories with "→" are subcategories') }}</small>
      </div>
    </div>

    <div class="mb-3">
      <label>{{ __('Images (you can upload multiple)') }}</label>
      <input type="file" name="images[]" id="images" class="form-control" multiple accept="image/*">
      <div class="form-text">
        {{ __('Maximum file size:') }} {{ number_format($maxUploadSize / 1024 / 1024, 0) }}MB {{ __('per image') }}
      </div>
      <div id="fileSizeError" class="text-danger mt-2" style="display: none;"></div>
    </div>

    <div class="mb-3">
      <label for="videos" class="form-label">{{ __('YouTube Video URLs (Optional)') }}</label>
      <textarea name="videos" 
                id="videos"
                class="form-control @error('videos') is-invalid @enderror" 
                rows="4" 
                placeholder="{{ __('Enter YouTube URLs, one per line:') }}&#10;https://www.youtube.com/watch?v=VIDEO_ID&#10;https://youtu.be/VIDEO_ID&#10;https://www.youtube.com/embed/VIDEO_ID"></textarea>
      @error('videos')
        <div class="invalid-feedback">{{ $message }}</div>
      @enderror
      <div class="form-text">
        <strong>{{ __('Supported formats:') }}</strong><br>
        • https://www.youtube.com/watch?v=VIDEO_ID<br>
        • https://youtu.be/VIDEO_ID<br>
        • https://www.youtube.com/embed/VIDEO_ID<br>
        {{ __('Enter one URL per line.') }}
      </div>
    </div>

    <button type="submit" class="btn btn-primary" id="submitBtn">{{ __('Create Product') }}</button>
  </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('productForm');
    const fileInput = document.getElementById('images');
    const errorDiv = document.getElementById('fileSizeError');
    const submitBtn = document.getElementById('submitBtn');
    const maxFileSize = {{ $maxUploadSize }}; // bytes
    const maxFileSizeMB = (maxFileSize / 1024 / 1024).toFixed(0);

    // Validate files on selection
    fileInput.addEventListener('change', function() {
        validateFiles();
    });

    // Validate before form submission
    form.addEventListener('submit', function(e) {
        if (!validateFiles()) {
            e.preventDefault();
            return false;
        }
    });

    function validateFiles() {
        errorDiv.style.display = 'none';
        errorDiv.innerHTML = '';
        
        if (!fileInput.files || fileInput.files.length === 0) {
            fileInput.classList.remove('is-invalid');
            submitBtn.disabled = false;
            return true;
        }

        const files = Array.from(fileInput.files);
        const oversizedFiles = [];
        let totalSize = 0;

        files.forEach(file => {
            totalSize += file.size;
            if (file.size > maxFileSize) {
                oversizedFiles.push({
                    name: file.name,
                    size: (file.size / 1024 / 1024).toFixed(2)
                });
            }
        });

        // Check individual file sizes
        if (oversizedFiles.length > 0) {
            let errorMsg = '{{ __("The following files exceed the maximum upload size of") }} ' + maxFileSizeMB + 'MB:<br><ul>';
            oversizedFiles.forEach(file => {
                errorMsg += '<li><strong>' + file.name + '</strong> (' + file.size + 'MB)</li>';
            });
            errorMsg += '</ul>{{ __("Please select smaller files or reduce the image quality/size.") }}';
            
            errorDiv.innerHTML = errorMsg;
            errorDiv.style.display = 'block';
            fileInput.classList.add('is-invalid');
            submitBtn.disabled = true;
            return false;
        }

        // Check total upload size (should not exceed post_max_size)
        if (totalSize > maxFileSize) {
            const totalSizeMB = (totalSize / 1024 / 1024).toFixed(2);
            errorDiv.innerHTML = '{{ __("Total size of all files") }} (' + totalSizeMB + 'MB) {{ __("exceeds the server limit of") }} ' + maxFileSizeMB + 'MB.<br>{{ __("Please upload fewer images or reduce their size.") }}';
            errorDiv.style.display = 'block';
            fileInput.classList.add('is-invalid');
            submitBtn.disabled = true;
            return false;
        }

        fileInput.classList.remove('is-invalid');
        submitBtn.disabled = false;
        return true;
    }
});
</script>
@endsection