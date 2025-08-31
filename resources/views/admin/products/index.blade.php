@extends('layouts.app')

@section('title', __('Manage Products'))

@section('content')
<div class="container py-5">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h1>{{ __('Manage Products') }}</h1>
    <a href="{{ route('admin.products.create') }}" class="btn btn-primary">{{ __('Add New Product') }}</a>
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
            <th>{{ __('Image') }}</th>
            <th>{{ __('Product Name') }}</th>
            <th>{{ __('Category') }}</th>
            <th>{{ __('Owner') }}</th>
            <th>{{ __('Price') }}</th>
            <th>{{ __('Images') }}</th>
            <th>{{ __('Videos') }}</th>
            <th>{{ __('Actions') }}</th>
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
                    <small class="text-muted">{{ __('No image') }}</small>
                  </div>
                @endif
              </td>
              <td>
                <strong>{{ $product->name }}</strong>
                <br>
                <small class="text-muted">{{ \Illuminate\Support\Str::limit($product->description, 60) }}</small>
              </td>
              <td>
                <span class="badge bg-secondary">{{ $product->category->name ?? __('No Category') }}</span>
              </td>
              <td>
                <span class="badge bg-dark">{{ $product->user->name ?? '—' }}</span>
              </td>
              <td>
                <strong>{{ $product->formatted_price }}</strong>
              </td>
              <td>
                <span class="badge bg-info">{{ $product->images->count() }} {{ __('images') }}</span>
              </td>
              <td>
                <span class="badge bg-warning">{{ $product->videos->count() }} {{ __('videos') }}</span>
              </td>
              <td>
                <div class="btn-group" role="group" aria-label="Product actions">
                  <a href="{{ route('products.show', $product) }}" 
                     class="btn btn-outline-info btn-sm" 
                     target="_blank"
                     data-bs-toggle="tooltip" 
                     title="{{ __('View product on frontend') }}">
                    <i class="fas fa-eye"></i> {{ __('View') }}
                  </a>
                  <a href="{{ route('admin.products.edit', $product) }}" 
                     class="btn btn-outline-primary btn-sm"
                     data-bs-toggle="tooltip" 
                     title="{{ __('Edit product details') }}">
                    <i class="fas fa-edit"></i> {{ __('Edit') }}
                  </a>
                  <button type="button" 
                          class="btn btn-outline-danger btn-sm" 
                          data-bs-toggle="modal" 
                          data-bs-target="#deleteModal{{ $product->id }}"
                          title="{{ __('Delete product permanently') }}">
                    <i class="fas fa-trash"></i> {{ __('Delete') }}
                  </button>
                </div>

                <!-- Delete Confirmation Modal -->
                <div class="modal fade" id="deleteModal{{ $product->id }}" tabindex="-1" aria-labelledby="deleteModalLabel{{ $product->id }}" aria-hidden="true">
                  <div class="modal-dialog">
                    <div class="modal-content">
                      <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title" id="deleteModalLabel{{ $product->id }}">
                          <i class="fas fa-exclamation-triangle me-2"></i>
                          {{ __('Confirm Delete') }}
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
                              <strong>{{ __('Are you sure you want to delete this product?') }}</strong><br>
                              <small class="text-danger">{{ __('This action cannot be undone and will also delete all associated images and videos.') }}</small>
                            </p>
                          </div>
                        </div>
                      </div>
                      <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                          <i class="fas fa-times me-1"></i>
                          {{ __('Cancel') }}
                        </button>
                        <form method="POST" action="{{ route('admin.products.destroy', $product) }}" class="d-inline">
                          @csrf
                          @method('DELETE')
                          <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash me-1"></i>
                            {{ __('Delete Product') }}
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
              <h5 class="card-title">{{ __('Total Products') }}</h5>
              <h2>{{ $products->count() }}</h2>
            </div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="card bg-success text-white">
            <div class="card-body">
              <h5 class="card-title">{{ __('With Images') }}</h5>
              <h2>{{ $products->filter(function($p) { return $p->images->count() > 0; })->count() }}</h2>
            </div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="card bg-warning text-white">
            <div class="card-body">
              <h5 class="card-title">{{ __('With Videos') }}</h5>
              <h2>{{ $products->filter(function($p) { return $p->videos->count() > 0; })->count() }}</h2>
            </div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="card bg-info text-white">
            <div class="card-body">
              <h5 class="card-title">{{ __('Avg. Price') }}</h5>
              <h2>${{ $products->count() > 0 ? number_format($products->avg('price'), 2) : '0.00' }}</h2>
            </div>
          </div>
        </div>
      </div>
    </div>
  @else
    <div class="alert alert-info">
      <h4>{{ __('No products found') }}</h4>
      <p>{{ __('Start by creating your first product.') }}</p>
      <a href="{{ route('admin.products.create') }}" class="btn btn-primary">{{ __('Create First Product') }}</a>
    </div>
  @endif
</div>
@endsection
