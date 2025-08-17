@extends('layouts.app')

@section('title', 'Add Product')

@section('content')
<div class="container py-5">
  <h1 class="mb-4">Create New Product</h1>

  @if ($errors->any())
    <div class="alert alert-danger">
      <ul class="mb-0">
        @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
    @csrf

    <div class="mb-3">
      <label>Name</label>
      <input type="text" name="name" class="form-control" required>
    </div>

    <div class="mb-3">
      <label>Description</label>
      <textarea name="description" class="form-control" rows="4"></textarea>
    </div>

    <div class="row">
      <div class="col-md-4">
        <div class="mb-3">
          <label>Price</label>
          <input type="number" name="price" class="form-control" step="0.01" required>
        </div>
      </div>
      <div class="col-md-4">
        <div class="mb-3">
          <label>Currency</label>
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
          <label>Stock (Number of Items)</label>
          <input type="number" name="no_of_items" class="form-control" min="0" value="0" required>
        </div>
      </div>
    </div>

    <div class="mb-3">
      <label>Category</label>
      <select name="category_id" class="form-control" required>
        @foreach ($categories as $category)
          <option value="{{ $category->id }}">{{ $category->name }}</option>
        @endforeach
      </select>
    </div>

    <div class="mb-3">
      <label>Images (you can upload multiple)</label>
      <input type="file" name="images[]" class="form-control" multiple accept="image/*">
    </div>

    <div class="mb-3">
      <label for="videos" class="form-label">YouTube Video URLs (Optional)</label>
      <textarea name="videos" 
                id="videos"
                class="form-control @error('videos') is-invalid @enderror" 
                rows="4" 
                placeholder="Enter YouTube URLs, one per line:&#10;https://www.youtube.com/watch?v=VIDEO_ID&#10;https://youtu.be/VIDEO_ID&#10;https://www.youtube.com/embed/VIDEO_ID"></textarea>
      @error('videos')
        <div class="invalid-feedback">{{ $message }}</div>
      @enderror
      <div class="form-text">
        <strong>Supported formats:</strong><br>
        • https://www.youtube.com/watch?v=VIDEO_ID<br>
        • https://youtu.be/VIDEO_ID<br>
        • https://www.youtube.com/embed/VIDEO_ID<br>
        Enter one URL per line.
      </div>
    </div>

    <button class="btn btn-primary">Create Product</button>
  </form>
</div>
@endsection