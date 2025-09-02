@extends('layouts.app')

@section('title', __('Send Message'))

@section('content')
<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-md-8">
      <div class="card">
        <div class="card-header">
          <h4 class="mb-0">{{ __('Send Message') }}</h4>
        </div>
        <div class="card-body">
          <form method="POST" action="{{ route('messages.store') }}">
            @csrf
            
            @if($receiver)
              <input type="hidden" name="receiver_id" value="{{ $receiver->id }}">
            @endif
            @if($product)
              <input type="hidden" name="product_id" value="{{ $product->id }}">
            @endif

            <!-- Receiver Info -->
            <div class="mb-3">
              <label class="form-label">{{ __('To') }}:</label>
              @if($receiver)
                <div class="d-flex align-items-center">
                  <div class="bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center me-3" 
                       style="width: 40px; height: 40px; font-size: 1rem; font-weight: bold;">
                    {{ strtoupper(substr($receiver->name, 0, 1)) }}
                  </div>
                  <div>
                    <strong>{{ $receiver->name }}</strong>  
                  </div>
                </div>
              @else
                <select class="form-select @error('receiver_id') is-invalid @enderror" name="receiver_id" required>
                  <option value="">{{ __('Select a user...') }}</option>
                  @foreach($users as $user)
                    <option value="{{ $user->id }}" {{ old('receiver_id') == $user->id ? 'selected' : '' }}>
                      {{ $user->name }}
                    </option>
                  @endforeach
                </select>
                @error('receiver_id')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              @endif
            </div>

            @if($product)
              <!-- Product Info -->
              <div class="mb-3">
                <label class="form-label">{{ __('About Product') }}:</label>
                <div class="card bg-light">
                  <div class="card-body py-2">
                    <div class="d-flex align-items-center">
                      @if($product->images->count() > 0)
                        <img src="{{ $product->images->first()->url }}" 
                             class="rounded me-3" 
                             style="width: 50px; height: 50px; object-fit: cover;">
                      @endif
                      <div>
                        <strong>{{ $product->name }}</strong>
                        <br>
                        <small class="text-muted">{{ format_currency($product->price) }}</small>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            @endif

            <!-- Subject -->
            <div class="mb-3">
              <label for="subject" class="form-label">{{ __('Subject') }}</label>
              <input type="text" 
                     class="form-control @error('subject') is-invalid @enderror" 
                     id="subject" 
                     name="subject" 
                     value="{{ old('subject', $subject) }}" 
                     required>
              @error('subject')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <!-- Message -->
            <div class="mb-3">
              <label for="message" class="form-label">{{ __('Message') }}</label>
              <textarea class="form-control @error('message') is-invalid @enderror" 
                        id="message" 
                        name="message" 
                        rows="6" 
                        placeholder="{{ __('Type your message here...') }}" 
                        required>{{ old('message') }}</textarea>
              @error('message')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
              <div class="form-text">{{ __('Maximum 2000 characters') }}</div>
            </div>

            <!-- Actions -->
            <div class="d-flex justify-content-between">
              <a href="{{ route('messages.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i>{{ __('Back') }}
              </a>
              <button type="submit" class="btn btn-primary">
                <i class="fas fa-paper-plane me-1"></i>{{ __('Send Message') }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
