@extends('layouts.app')

@section('title', __('Manage Bundles'))

@section('content')
<div class="container py-5">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h1>{{ __('Manage Bundles') }}</h1>
    <a href="{{ route('admin.bundles.create') }}" class="btn btn-primary">
      <i class="fas fa-plus me-1"></i> {{ __('Create Bundle') }}
    </a>
  </div>

  @if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      {{ session('success') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  @endif

  @if ($bundles->count() > 0)
    <div class="table-responsive">
      <table class="table table-striped">
        <thead>
          <tr>
            <th>{{ __('Image') }}</th>
            <th>{{ __('Bundle Name') }}</th>
            <th>{{ __('Products') }}</th>
            <th>{{ __('Price') }}</th>
            <th>{{ __('Savings') }}</th>
            <th>{{ __('Status') }}</th>
            <th>{{ __('Actions') }}</th>
          </tr>
        </thead>
        <tbody>
          @foreach ($bundles as $bundle)
            <tr>
              <td>
                @if($bundle->images->count() > 0)
                  <img src="{{ $bundle->images->first()->url }}" 
                       alt="{{ $bundle->name }}" 
                       style="width: 80px; height: 80px; object-fit: cover; border-radius: 4px;">
                @elseif($bundle->products->first()?->images->count() > 0)
                  <img src="{{ $bundle->products->first()->images->first()->url }}" 
                       alt="{{ $bundle->name }}" 
                       style="width: 80px; height: 80px; object-fit: cover; border-radius: 4px;">
                @else
                  <div class="bg-light d-flex align-items-center justify-content-center" 
                       style="width: 80px; height: 80px; border-radius: 4px;">
                    <small class="text-muted">{{ __('No image') }}</small>
                  </div>
                @endif
              </td>
              <td>
                <strong>{{ $bundle->name }}</strong>
                @if($bundle->description)
                  <br><small class="text-muted">{{ \Illuminate\Support\Str::limit($bundle->description, 60) }}</small>
                @endif
              </td>
              <td>
                <span class="badge bg-info">{{ $bundle->products->count() }} {{ __('products') }}</span>
                <br>
                <small class="text-muted">
                  @foreach($bundle->products->take(3) as $product)
                    {{ $product->name }}{{ !$loop->last ? ', ' : '' }}
                  @endforeach
                  @if($bundle->products->count() > 3)
                    ...
                  @endif
                </small>
              </td>
              <td>
                <strong>{{ $bundle->formatted_price }}</strong>
                @if($bundle->has_meaningful_savings)
                  <br>
                  <small class="text-muted text-decoration-line-through">
                    {{ format_currency($bundle->total_products_value, $bundle->currency) }}
                  </small>
                @endif
              </td>
              <td>
                @if($bundle->has_meaningful_savings)
                  <span class="badge bg-success">
                    {{ format_currency($bundle->savings, $bundle->currency) }} ({{ $bundle->savings_percentage }}%)
                  </span>
                @else
                  <span class="badge bg-secondary">{{ __('N/A') }}</span>
                @endif
              </td>
              <td>
                @if($bundle->is_active)
                  <span class="badge bg-success">{{ __('Active') }}</span>
                @else
                  <span class="badge bg-danger">{{ __('Inactive') }}</span>
                @endif
                @if(!$bundle->hasStock())
                  <br><span class="badge bg-warning mt-1">{{ __('Out of stock') }}</span>
                @endif
              </td>
              <td>
                <div class="btn-group" role="group">
                  <a href="{{ route('admin.bundles.edit', $bundle) }}" 
                     class="btn btn-outline-primary btn-sm">
                    <i class="fas fa-edit"></i>
                  </a>
                  <form method="POST" action="{{ route('admin.bundles.toggle-status', $bundle) }}" class="d-inline">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="btn btn-outline-{{ $bundle->is_active ? 'warning' : 'success' }} btn-sm"
                            title="{{ $bundle->is_active ? __('Deactivate') : __('Activate') }}">
                      <i class="fas fa-{{ $bundle->is_active ? 'pause' : 'play' }}"></i>
                    </button>
                  </form>
                  <button type="button" 
                          class="btn btn-outline-danger btn-sm" 
                          data-bs-toggle="modal" 
                          data-bs-target="#deleteModal{{ $bundle->id }}">
                    <i class="fas fa-trash"></i>
                  </button>
                </div>

                <!-- Delete Modal -->
                <div class="modal fade" id="deleteModal{{ $bundle->id }}" tabindex="-1">
                  <div class="modal-dialog">
                    <div class="modal-content">
                      <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title">{{ __('Confirm Delete') }}</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                      </div>
                      <div class="modal-body">
                        <p>{{ __('Are you sure you want to delete the bundle') }} <strong>{{ $bundle->name }}</strong>?</p>
                        <p class="text-muted">{{ __('This will not delete the products in the bundle.') }}</p>
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
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>

    <div class="mt-4">
      <div class="row">
        <div class="col-md-4">
          <div class="card bg-primary text-white">
            <div class="card-body">
              <h5 class="card-title">{{ __('Total Bundles') }}</h5>
              <h2>{{ $bundles->count() }}</h2>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card bg-success text-white">
            <div class="card-body">
              <h5 class="card-title">{{ __('Active Bundles') }}</h5>
              <h2>{{ $bundles->where('is_active', true)->count() }}</h2>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card bg-info text-white">
            <div class="card-body">
              <h5 class="card-title">{{ __('Avg. Savings') }}</h5>
              <h2>{{ $bundles->count() > 0 ? number_format($bundles->avg('savings_percentage'), 1) : 0 }}%</h2>
            </div>
          </div>
        </div>
      </div>
    </div>
  @else
    <div class="alert alert-info">
      <h4>{{ __('No bundles found') }}</h4>
      <p>{{ __('Create your first bundle to offer products together at a discounted price.') }}</p>
      <a href="{{ route('admin.bundles.create') }}" class="btn btn-primary">{{ __('Create First Bundle') }}</a>
    </div>
  @endif
</div>
@endsection
