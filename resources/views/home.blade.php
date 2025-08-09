@extends('layouts.app')

@section('title', 'Home')

@section('content')
<section id="hero">
  <!-- Hero banner from Majestic -->
</section>

<section id="products" class="container pb-5">
  <h2>Featured Trains</h2>
  <div class="row">
    @foreach ($products as $product)
      <div class="col-md-4 mb-4">
        <div class="card">
          <img src="{{ $product->images->first()->url ?? asset('assets/img/placeholder.jpg') }}" class="card-img-top" alt="{{ $product->name }}">
          <div class="card-body">
            <h5 class="card-title">{{ $product->name }}</h5>
            <p class="card-text">${{ $product->price }}</p>
            <a href="{{ route('products.show', $product) }}" class="btn btn-primary">View Details</a>
          </div>
        </div>
      </div>
    @endforeach
  </div>
</section>
@endsection