@extends('layouts.app')

@section('title', 'Manage Categories')

@section('content')
<div class="container py-5">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Categories</h1>
    <a href="{{ route('admin.categories.create') }}" class="btn btn-primary">Add New Category</a>
  </div>

  @if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      {{ session('success') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  @endif

  @if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
      <ul class="mb-0">
        @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  @endif

  @if ($categories->count() > 0)
    <div class="table-responsive">
      <table class="table table-striped">
        <thead>
          <tr>
            <th>Image</th>
            <th>Category Name</th>
            <th>Subcategories</th>
            <th>Products Count</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          @foreach ($categories as $category)
            <tr>
              <td>
                @if($category->images->count() > 0)
                  <img src="{{ $category->images->first()->url }}" 
                       alt="{{ $category->name }}" 
                       style="width: 60px; height: 60px; object-fit: cover; border-radius: 4px;">
                @else
                  <div class="bg-light d-flex align-items-center justify-content-center" 
                       style="width: 60px; height: 60px; border-radius: 4px;">
                    <small class="text-muted">No image</small>
                  </div>
                @endif
              </td>
              <td><strong>{{ $category->name }}</strong></td>
              <td>
                @if ($category->children->count() > 0)
                  <ul class="list-unstyled mb-0">
                    @foreach ($category->children as $child)
                      <li class="ms-3 d-flex align-items-center mb-2">
                        @if($child->images->count() > 0)
                          <img src="{{ $child->images->first()->url }}" 
                               alt="{{ $child->name }}" 
                               style="width: 30px; height: 30px; object-fit: cover; border-radius: 3px; margin-right: 8px;">
                        @endif
                        <small>{{ $child->name }}</small>
                        <div class="btn-group btn-group-sm ms-2" role="group" aria-label="Subcategory actions">
                          <a href="{{ route('admin.categories.edit', $child) }}" 
                             class="btn btn-outline-secondary btn-sm"
                             data-bs-toggle="tooltip" 
                             title="Edit subcategory">
                            <i class="fas fa-edit"></i>
                          </a>
                          <button type="button" 
                                  class="btn btn-outline-danger btn-sm" 
                                  data-bs-toggle="modal" 
                                  data-bs-target="#deleteSubModal{{ $child->id }}"
                                  title="Delete subcategory">
                            <i class="fas fa-trash"></i>
                          </button>
                        </div>
                      </li>
                    @endforeach
                  </ul>
                @else
                  <small class="text-muted">No subcategories</small>
                @endif
              </td>
              <td>
                <span class="badge bg-info">{{ $category->products->count() }} products</span>
              </td>
              <td>
                <div class="btn-group" role="group" aria-label="Category actions">
                  <a href="{{ route('admin.categories.edit', $category) }}" 
                     class="btn btn-outline-primary btn-sm"
                     data-bs-toggle="tooltip" 
                     title="Edit category">
                    <i class="fas fa-edit"></i> Edit
                  </a>
                  <button type="button" 
                          class="btn btn-outline-danger btn-sm" 
                          data-bs-toggle="modal" 
                          data-bs-target="#deleteModal{{ $category->id }}"
                          title="Delete category permanently">
                    <i class="fas fa-trash"></i> Delete
                  </button>
                </div>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>

    <!-- Delete Confirmation Modals -->
    @foreach ($categories as $category)
      <div class="modal fade" id="deleteModal{{ $category->id }}" tabindex="-1" aria-labelledby="deleteModalLabel{{ $category->id }}" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header bg-danger text-white">
              <h5 class="modal-title" id="deleteModalLabel{{ $category->id }}">
                <i class="fas fa-exclamation-triangle me-2"></i>
                Confirm Category Deletion
              </h5>
              <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
              <div class="d-flex align-items-start">
                @if($category->images->count() > 0)
                  <img src="{{ $category->images->first()->url }}" 
                       alt="{{ $category->name }}" 
                       class="me-3" 
                       style="width: 60px; height: 60px; object-fit: cover; border-radius: 4px;">
                @else
                  <div class="bg-light d-flex align-items-center justify-content-center me-3" 
                       style="width: 60px; height: 60px; border-radius: 4px;">
                    <i class="fas fa-folder fa-2x text-muted"></i>
                  </div>
                @endif
                <div>
                  <h6 class="mb-2">{{ $category->name }}</h6>
                  @if($category->products->count() > 0)
                    <p class="mb-2 text-warning">
                      <i class="fas fa-exclamation-triangle me-1"></i>
                      This category contains <strong>{{ $category->products->count() }} products</strong>.
                    </p>
                  @endif
                  @if($category->children->count() > 0)
                    <p class="mb-2 text-warning">
                      <i class="fas fa-exclamation-triangle me-1"></i>
                      This category has <strong>{{ $category->children->count() }} subcategories</strong>.
                    </p>
                  @endif
                  <p class="mb-0">
                    <strong>Are you sure you want to delete this category?</strong><br>
                    <small class="text-danger">This action cannot be undone and will also delete all associated images{{ $category->products->count() > 0 ? ' and affect related products' : '' }}{{ $category->children->count() > 0 ? ' and all subcategories' : '' }}.</small>
                  </p>
                </div>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                <i class="fas fa-times me-1"></i>
                Cancel
              </button>
              <form method="POST" action="{{ route('admin.categories.destroy', $category) }}" class="d-inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">
                  <i class="fas fa-trash me-1"></i>
                  Delete Category
                </button>
              </form>
            </div>
          </div>
        </div>
      </div>
    @endforeach

    <!-- Delete Confirmation Modals for Subcategories -->
    @foreach ($categories as $category)
      @foreach ($category->children as $child)
        <div class="modal fade" id="deleteSubModal{{ $child->id }}" tabindex="-1" aria-labelledby="deleteSubModalLabel{{ $child->id }}" aria-hidden="true">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteSubModalLabel{{ $child->id }}">
                  <i class="fas fa-exclamation-triangle me-2"></i>
                  Confirm Subcategory Deletion
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                <div class="d-flex align-items-start">
                  @if($child->images->count() > 0)
                    <img src="{{ $child->images->first()->url }}" 
                         alt="{{ $child->name }}" 
                         class="me-3" 
                         style="width: 60px; height: 60px; object-fit: cover; border-radius: 4px;">
                  @else
                    <div class="bg-light d-flex align-items-center justify-content-center me-3" 
                         style="width: 60px; height: 60px; border-radius: 4px;">
                      <i class="fas fa-folder fa-2x text-muted"></i>
                    </div>
                  @endif
                  <div>
                    <h6 class="mb-2">{{ $child->name }}</h6>
                    <p class="mb-2 text-muted">
                      Subcategory of: <strong>{{ $category->name }}</strong>
                    </p>
                    @if($child->products->count() > 0)
                      <p class="mb-2 text-warning">
                        <i class="fas fa-exclamation-triangle me-1"></i>
                        This subcategory contains <strong>{{ $child->products->count() }} products</strong>.
                      </p>
                    @endif
                    <p class="mb-0">
                      <strong>Are you sure you want to delete this subcategory?</strong><br>
                      <small class="text-danger">This action cannot be undone and will also delete all associated images{{ $child->products->count() > 0 ? ' and affect related products' : '' }}.</small>
                    </p>
                  </div>
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                  <i class="fas fa-times me-1"></i>
                  Cancel
                </button>
                <form method="POST" action="{{ route('admin.categories.destroy', $child) }}" class="d-inline">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="btn btn-danger">
                    <i class="fas fa-trash me-1"></i>
                    Delete Subcategory
                  </button>
                </form>
              </div>
            </div>
          </div>
        </div>
      @endforeach
    @endforeach
  @else
    <div class="alert alert-info">
      <h4>No categories found</h4>
      <p>Start by creating your first category.</p>
      <a href="{{ route('admin.categories.create') }}" class="btn btn-primary">Create First Category</a>
    </div>
  @endif
</div>
@endsection