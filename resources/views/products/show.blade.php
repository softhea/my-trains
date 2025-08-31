@extends('layouts.app')

@section('title', $product->name)

@section('content')
<div class="container py-5">
  <div class="row">
    <div class="col-md-6">
      @if($product->images->count() > 0)
        <!-- Main Image Display -->
        <div class="position-relative">
          <div id="productCarousel" class="carousel slide" data-bs-ride="carousel">
            <div class="carousel-inner">
              @foreach ($product->images as $image)
                <div class="carousel-item @if($loop->first) active @endif">
                  <img src="{{ $image->url }}" 
                       class="d-block w-100 product-image" 
                       alt="{{ $product->name }}"
                       data-index="{{ $loop->index }}"
                       style="cursor: pointer; max-height: 400px; object-fit: cover;">
                </div>
              @endforeach
            </div>
            @if($product->images->count() > 1)
              <button class="carousel-control-prev" type="button" data-bs-target="#productCarousel" data-bs-slide="prev">
                <span class="carousel-control-prev-icon"></span>
              </button>
              <button class="carousel-control-next" type="button" data-bs-target="#productCarousel" data-bs-slide="next">
                <span class="carousel-control-next-icon"></span>
              </button>
            @endif
          </div>
          
          <!-- Zoom indicator -->
          <div class="position-absolute top-0 end-0 m-3">
            <span class="badge bg-dark bg-opacity-75 text-white">
              <i class="fas fa-search-plus me-1"></i>
              {{ __('Click to zoom') }}
            </span>
          </div>
        </div>

        <!-- Thumbnail Gallery -->
        @if($product->images->count() > 1)
          <div class="mt-3">
            <div class="row g-2">
              @foreach ($product->images as $image)
                <div class="col-3">
                  <img src="{{ $image->url }}" 
                       class="img-thumbnail product-thumbnail" 
                       alt="{{ $product->name }}"
                       data-index="{{ $loop->index }}"
                       style="cursor: pointer; height: 80px; object-fit: cover; width: 100%;">
                </div>
              @endforeach
            </div>
          </div>
        @endif
      @else
        <div class="bg-light d-flex align-items-center justify-content-center" style="height: 400px;">
          <div class="text-center text-muted">
            <i class="fas fa-image fa-3x mb-3"></i>
            <p>{{ __('No images available') }}</p>
          </div>
        </div>
      @endif
    </div>
    <div class="col-md-6">
      <h1>{{ $product->name }}</h1>
      <p class="text-muted mb-1">{{ __('Added By') }}: {{ $product->user->name ?? '-' }}</p>
      <p class="lead">{{ $product->formatted_price }}</p>
      
      <!-- Stock Status -->
      <div class="mb-3">
        @if($product->isOutOfStock())
          <span class="badge bg-danger px-3 py-2">{{ __('Out of Stock') }}</span>
        @elseif($product->getStockStatus() === 'low_stock')
          <span class="badge bg-warning px-3 py-2">{{ __('Only :count left in stock', ['count' => $product->no_of_items]) }}</span>
        @else
          <span class="badge bg-success px-3 py-2">{{ $product->no_of_items }} {{ __('in stock') }}</span>
        @endif
      </div>
      
      <p style="white-space: pre-wrap;">{{ $product->description }}</p>
      
      @auth
        <!-- Contact Seller Button -->
        @if($product->user && $product->user->id !== Auth::id())
          <div class="mb-3">
            <a href="{{ route('messages.create', ['product_id' => $product->id]) }}" class="btn btn-outline-primary">
              <i class="fas fa-envelope me-1"></i>{{ __('Contact Seller') }}
            </a>
          </div>
        @endif

        @if($product->isOutOfStock())
          <div class="alert alert-warning">
            <strong>{{ __('Sorry!') }}</strong> {{ __('This product is currently out of stock.') }}
          </div>
        @else
          <form action="{{ route('order.store') }}" method="POST">
            @csrf
            <input type="hidden" name="product_id" value="{{ $product->id }}">
            <div class="mb-2">
              <label>{{ __('Quantity') }}</label>
              <input type="number" name="quantity" value="1" class="form-control" min="1" max="{{ $product->no_of_items }}">
              <small class="text-muted">{{ __('Maximum available: :count', ['count' => $product->no_of_items]) }}</small>
            </div>
            <div class="mb-2">
              <label>{{ __('Note (optional)') }}</label>
              <textarea name="note" class="form-control"></textarea>
            </div>
            <button class="btn btn-success">{{ __('Place Order') }}</button>
          </form>
        @endif
      @else
        <!-- For non-authenticated users -->
        @if($product->user)
          <div class="mb-3">
            <a href="{{ route('login') }}" class="btn btn-outline-primary">
              <i class="fas fa-envelope me-1"></i>{{ __('Contact Seller') }}
            </a>
            <small class="d-block text-muted mt-1">{{ __('Login required to send messages') }}</small>
          </div>
        @endif
        <p><a href="{{ route('login') }}">{{ __('Login') }}</a> {{ __('to place an order.') }}</p>
      @endauth
    </div>
  </div>

  @if($product->videos->count())
  <div class="row mt-5">
    <div class="col-12">
      <h3 class="mb-4">
        <i class="fas fa-play-circle text-danger me-2"></i>
        {{ __('Product Videos') }}
      </h3>
    </div>
    @foreach($product->videos as $video)
      <div class="col-lg-6 col-md-12 mb-4">
        @if($video->isValidYouTubeUrl())
          <div class="card shadow-sm">
            <div class="ratio ratio-16x9">
              <iframe 
                src="{{ $video->embed_url }}" 
                title="Product Video {{ $loop->iteration }}"
                frameborder="0" 
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                allowfullscreen
                loading="lazy">
              </iframe>
            </div>
            <div class="card-body py-2">
              <small class="text-muted">
                <i class="fab fa-youtube text-danger me-1"></i>
                Video {{ $loop->iteration }}
                <a href="{{ $video->url }}" target="_blank" class="ms-2">
                  <i class="fas fa-external-link-alt"></i> {{ __('Open in YouTube') }}
                </a>
              </small>
            </div>
          </div>
        @else
          <div class="alert alert-warning">
            <i class="fas fa-exclamation-triangle me-2"></i>
            {{ __('Invalid YouTube URL:') }} <a href="{{ $video->url }}" target="_blank">{{ $video->url }}</a>
          </div>
        @endif
      </div>
    @endforeach
  </div>
  @endif

  <!-- Lightbox Modal -->
  @if($product->images->count() > 0)
    <div class="modal fade" id="imageModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content bg-transparent border-0">
          <div class="modal-header border-0 pb-0">
            <div class="d-flex align-items-center text-white">
              <h5 class="modal-title me-3">{{ $product->name }}</h5>
              <span class="badge bg-dark bg-opacity-75" id="imageCounter">1 / {{ $product->images->count() }}</span>
            </div>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body text-center p-0">
            <div class="position-relative">
              <img id="modalImage" 
                   src="" 
                   alt="{{ $product->name }}" 
                   class="img-fluid"
                   style="max-height: 80vh; max-width: 100%; object-fit: contain;">
              
              <!-- Navigation buttons -->
              @if($product->images->count() > 1)
                <button type="button" 
                        class="btn btn-dark btn-lg position-absolute top-50 start-0 translate-middle-y ms-3" 
                        id="prevImage"
                        style="opacity: 0.8;">
                  <i class="fas fa-chevron-left"></i>
                </button>
                <button type="button" 
                        class="btn btn-dark btn-lg position-absolute top-50 end-0 translate-middle-y me-3" 
                        id="nextImage"
                        style="opacity: 0.8;">
                  <i class="fas fa-chevron-right"></i>
                </button>
              @endif
            </div>
          </div>
          
          @if($product->images->count() > 1)
            <div class="modal-footer border-0 pt-0">
              <div class="d-flex justify-content-center">
                <div class="row g-2" id="modalThumbnails">
                  @foreach ($product->images as $image)
                    <div class="col-auto">
                      <img src="{{ $image->url }}" 
                           class="img-thumbnail modal-thumbnail" 
                           alt="{{ $product->name }}"
                           data-index="{{ $loop->index }}"
                           style="width: 60px; height: 60px; object-fit: cover; cursor: pointer; opacity: 0.6;">
                    </div>
                  @endforeach
                </div>
              </div>
            </div>
          @endif
        </div>
      </div>
    </div>
  @endif
</div>

<!-- Pass image data to JavaScript -->
<script>
  window.productImages = @json($product->images->map(function($image) {
    return $image->url;
  }));
</script>
@endsection