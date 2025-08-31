@extends('layouts.app')

@section('title', 'Edit Category')

@section('content')
<div class="container py-5">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Edit Category: {{ $category->name }}</h1>
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
          <form action="{{ route('admin.categories.update', $category) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="mb-3">
              <label for="name" class="form-label">Category Name</label>
              <input type="text" 
                     name="name" 
                     id="name"
                     class="form-control @error('name') is-invalid @enderror" 
                     value="{{ old('name', $category->name) }}"
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
                  <option value="{{ $parent->id }}" 
                          {{ old('parent_id', $category->parent_id) == $parent->id ? 'selected' : '' }}>
                    {{ $parent->name }}
                  </option>
                @endforeach
              </select>
              @error('parent_id')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
              <div class="form-text">Select a parent category to make this a subcategory, or leave empty to make it a main category.</div>
            </div>

            <!-- Existing Images -->
            @if($category->images->count() > 0)
              <div class="mb-3">
                <label class="form-label">Current Images</label>
                <div class="row">
                  @foreach($category->images as $image)
                    <div class="col-md-3 mb-3">
                      <div class="card">
                        <img src="{{ $image->url }}" class="card-img-top" style="height: 150px; object-fit: cover;" alt="Category Image">
                        <div class="card-body p-2">
                          <button type="button" 
                                  class="btn btn-sm btn-danger w-100" 
                                  onclick="deleteCategoryImage({{ $image->id }})"
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
              <div class="form-text">You can upload additional images for this category. Maximum size: 8MB per image.</div>
            </div>

            <div class="d-flex gap-2">
              <button type="submit" class="btn btn-primary">Update Category</button>
              <a href="{{ route('admin.categories.index') }}" class="btn btn-outline-secondary">Cancel</a>
            </div>
          </form>
        </div>
      </div>
    </div>
    
    <div class="col-md-4">
      <div class="card bg-light">
        <div class="card-body">
          <h5 class="card-title">📊 Category Stats</h5>
          <ul class="list-unstyled">
            <li class="mb-2">
              <strong>Products:</strong> {{ $category->products->count() }} products in this category
            </li>
            <li class="mb-2">
              <strong>Subcategories:</strong> {{ $category->children->count() }} subcategories
            </li>
            @if ($category->parent)
              <li class="mb-2">
                <strong>Parent:</strong> {{ $category->parent->name }}
              </li>
            @endif
          </ul>
          
          @if ($category->children->count() > 0)
            <div class="mt-3">
              <h6>Subcategories:</h6>
              <ul class="list-unstyled">
                @foreach ($category->children as $child)
                  <li>• {{ $child->name }}</li>
                @endforeach
              </ul>
            </div>
          @endif
        </div>
      </div>
    </div>
  </div>
</div>


@endsection
