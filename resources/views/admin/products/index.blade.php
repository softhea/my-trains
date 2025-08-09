@extends('layouts.app')

@section('title', 'Manage Products')

@section('content')
<div class="container py-5">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Manage Products</h1>
    <a href="{{ route('admin.products.create') }}" class="btn btn-primary">Add New Product</a>
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

  @if ($products->count() > 0)
    <div class="table-responsive">
      <table class="table table-striped">
        <thead>
          <tr>
            <th>Image</th>
            <th>Product Name</th>
            <th>Category</th>
            <th>Price</th>
            <th>Images</th>
            <th>Videos</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          @foreach ($products as $product)
            <tr>
              <td>
                @if($product->images->count() > 0)
                  <img src="{{ $product->images->first()->url }}" 
                       alt="{{ $product->name }}" 
                       style="width: 80px; height: 80px; object-fit: cover; border-radius: 4px;">
                @else
                  <div class="bg-light d-flex align-items-center justify-content-center" 
                       style="width: 80px; height: 80px; border-radius: 4px;">
                    <small class="text-muted">No image</small>
                  </div>
                @endif
              </td>
              <td>
                <strong>{{ $product->name }}</strong>
                <br>
                <small class="text-muted">{{ \Illuminate\Support\Str::limit($product->description, 60) }}</small>
              </td>
              <td>
                <span class="badge bg-secondary">{{ $product->category->name ?? 'No Category' }}</span>
              </td>
              <td>
                <strong>${{ number_format($product->price, 2) }}</strong>
              </td>
              <td>
                <span class="badge bg-info">{{ $product->images->count() }} images</span>
              </td>
              <td>
                <span class="badge bg-warning">{{ $product->videos->count() }} videos</span>
              </td>
              <td>
                <div class="btn-group" role="group" aria-label="Product actions">
                  <a href="{{ route('products.show', $product) }}" 
                     class="btn btn-outline-info btn-sm" 
                     target="_blank"
                     data-bs-toggle="tooltip" 
                     title="View product on frontend">
                    <i class="fas fa-eye"></i> View
                  </a>
                  <a href="{{ route('admin.products.edit', $product) }}" 
                     class="btn btn-outline-primary btn-sm"
                     data-bs-toggle="tooltip" 
                     title="Edit product details">
                    <i class="fas fa-edit"></i> Edit
                  </a>
                  <button type="button" 
                          class="btn btn-outline-danger btn-sm" 
                          data-bs-toggle="modal" 
                          data-bs-target="#deleteModal{{ $product->id }}"
                          title="Delete product permanently">
                    <i class="fas fa-trash"></i> Delete
                  </button>
                </div>

                <!-- Delete Confirmation Modal -->
                <div class="modal fade" id="deleteModal{{ $product->id }}" tabindex="-1" aria-labelledby="deleteModalLabel{{ $product->id }}" aria-hidden="true">
                  <div class="modal-dialog">
                    <div class="modal-content">
                      <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title" id="deleteModalLabel{{ $product->id }}">
                          <i class="fas fa-exclamation-triangle me-2"></i>
                          Confirm Delete
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                      </div>
                      <div class="modal-body">
                        <div class="d-flex align-items-start">
                          @if($product->images->count() > 0)
                            <img src="{{ $product->images->first()->url }}" 
                                 alt="{{ $product->name }}" 
                                 class="me-3" 
                                 style="width: 60px; height: 60px; object-fit: cover; border-radius: 4px;">
                          @endif
                          <div>
                            <h6 class="mb-2">{{ $product->name }}</h6>
                            <p class="mb-2 text-muted">{{ \Illuminate\Support\Str::limit($product->description, 100) }}</p>
                            <p class="mb-0">
                              <strong>Are you sure you want to delete this product?</strong><br>
                              <small class="text-danger">This action cannot be undone and will also delete all associated images and videos.</small>
                            </p>
                          </div>
                        </div>
                      </div>
                      <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                          <i class="fas fa-times me-1"></i>
                          Cancel
                        </button>
                        <form method="POST" action="{{ route('admin.products.destroy', $product) }}" class="d-inline">
                          @csrf
                          @method('DELETE')
                          <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash me-1"></i>
                            Delete Product
                          </button>
                        </form>
                      </div>
                    </div>
                  </div>
                </div>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>

    <div class="mt-4">
      <div class="row">
        <div class="col-md-3">
          <div class="card bg-primary text-white">
            <div class="card-body">
              <h5 class="card-title">Total Products</h5>
              <h2>{{ $products->count() }}</h2>
            </div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="card bg-success text-white">
            <div class="card-body">
              <h5 class="card-title">With Images</h5>
              <h2>{{ $products->filter(function($p) { return $p->images->count() > 0; })->count() }}</h2>
            </div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="card bg-warning text-white">
            <div class="card-body">
              <h5 class="card-title">With Videos</h5>
              <h2>{{ $products->filter(function($p) { return $p->videos->count() > 0; })->count() }}</h2>
            </div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="card bg-info text-white">
            <div class="card-body">
              <h5 class="card-title">Avg. Price</h5>
              <h2>${{ $products->count() > 0 ? number_format($products->avg('price'), 2) : '0.00' }}</h2>
            </div>
          </div>
        </div>
      </div>
    </div>
  @else
    <div class="alert alert-info">
      <h4>No products found</h4>
      <p>Start by creating your first product.</p>
      <a href="{{ route('admin.products.create') }}" class="btn btn-primary">Create First Product</a>
    </div>
  @endif
</div>
@endsection
