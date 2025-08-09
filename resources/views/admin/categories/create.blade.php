@extends('layouts.app')

@section('title', 'Create Category')

@section('content')
<div class="container py-5">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Create New Category</h1>
    <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">Back to Categories</a>
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
          <form action="{{ route('admin.categories.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="mb-3">
              <label for="name" class="form-label">Category Name</label>
              <input type="text" 
                     name="name" 
                     id="name"
                     class="form-control @error('name') is-invalid @enderror" 
                     value="{{ old('name') }}"
                     required
                     placeholder="Enter category name">
              @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div class="mb-3">
              <label for="parent_id" class="form-label">Parent Category (Optional)</label>
              <select name="parent_id" id="parent_id" class="form-select @error('parent_id') is-invalid @enderror">
                <option value="">-- No Parent (Main Category) --</option>
                @foreach ($parentCategories as $parent)
                  <option value="{{ $parent->id }}" {{ old('parent_id') == $parent->id ? 'selected' : '' }}>
                    {{ $parent->name }}
                  </option>
                @endforeach
              </select>
              @error('parent_id')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
              <div class="form-text">Select a parent category to create a subcategory, or leave empty to create a main category.</div>
            </div>

            <div class="mb-3">
              <label for="images" class="form-label">Category Images (Optional)</label>
              <input type="file" 
                     name="images[]" 
                     id="images"
                     class="form-control @error('images.*') is-invalid @enderror" 
                     multiple 
                     accept="image/*">
              @error('images.*')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
              <div class="form-text">You can upload multiple images for this category. Maximum size: 2MB per image.</div>
            </div>

            <div class="d-flex gap-2">
              <button type="submit" class="btn btn-primary">Create Category</button>
              <a href="{{ route('admin.categories.index') }}" class="btn btn-outline-secondary">Cancel</a>
            </div>
          </form>
        </div>
      </div>
    </div>
    
    <div class="col-md-4">
      <div class="card bg-light">
        <div class="card-body">
          <h5 class="card-title">💡 Tips</h5>
          <ul class="list-unstyled">
            <li class="mb-2">
              <strong>Main Categories:</strong> Leave "Parent Category" empty to create top-level categories like "Electric Trains" or "Steam Locomotives".
            </li>
            <li class="mb-2">
              <strong>Subcategories:</strong> Select a parent category to create subcategories like "HO Scale" under "Electric Trains".
            </li>
            <li>
              <strong>Category Names:</strong> Use clear, descriptive names that customers will easily understand.
            </li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
