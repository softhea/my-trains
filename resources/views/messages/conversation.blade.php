@extends('layouts.app')

@section('title', __('Conversation with :user', ['user' => $user->name]))

@section('content')
<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-md-10">
      <!-- Header -->
      <div class="card mb-4">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
              <div class="bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center me-3" 
                   style="width: 50px; height: 50px; font-size: 1.25rem; font-weight: bold;">
                {{ strtoupper(substr($user->name, 0, 1)) }}
              </div>
              <div>
                <h4 class="mb-0">{{ $user->name }}</h4>
              </div>
            </div>
            <a href="{{ route('messages.index') }}" class="btn btn-outline-secondary">
              <i class="fas fa-arrow-left me-1"></i>{{ __('Back to Messages') }}
            </a>
          </div>
        </div>
      </div>

      <!-- Messages -->
      <div class="card">
        <div class="card-body" style="max-height: 500px; overflow-y: auto;" id="messages-container">
          @if($messages->count() > 0)
            @foreach($messages as $message)
              <div class="d-flex mb-3 {{ $message->sender_id === auth()->id() ? 'justify-content-end' : 'justify-content-start' }}">
                <div class="message {{ $message->sender_id === auth()->id() ? 'bg-primary text-white' : 'bg-light' }} rounded p-3" 
                     style="max-width: 70%;">
                  
                  @if($message->product)
                    <div class="mb-2 pb-2 {{ $message->sender_id === auth()->id() ? 'border-bottom border-light' : 'border-bottom' }}">
                      <small class="{{ $message->sender_id === auth()->id() ? 'text-white-50' : 'text-muted' }}">
                        <i class="fas fa-box me-1"></i>{{ __('About:') }} {{ $message->product->name }}
                      </small>
                    </div>
                  @endif
                  
                  <div class="fw-bold mb-1">{{ $message->subject }}</div>
                  <div class="mb-2">{{ $message->message }}</div>
                  
                  <div class="d-flex justify-content-between align-items-center">
                    <small class="{{ $message->sender_id === auth()->id() ? 'text-white-50' : 'text-muted' }}">
                      {{ $message->sender->name }}
                    </small>
                    <small class="{{ $message->sender_id === auth()->id() ? 'text-white-50' : 'text-muted' }}">
                      {{ $message->created_at->format('M d, Y g:i A') }}
                    </small>
                  </div>
                </div>
              </div>
            @endforeach
          @else
            <div class="text-center py-4">
              <i class="fas fa-comments fa-3x text-muted mb-3"></i>
              <p class="text-muted">{{ __('No messages in this conversation yet.') }}</p>
            </div>
          @endif
        </div>

        <!-- Reply Form -->
        <div class="card-footer">
          <form method="POST" action="{{ route('messages.reply', $user) }}">
            @csrf
            <div class="mb-3">
              <textarea class="form-control @error('message') is-invalid @enderror" 
                        name="message" 
                        rows="3" 
                        placeholder="{{ __('Type your reply...') }}" 
                        required>{{ old('message') }}</textarea>
              @error('message')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
            
            <!-- CAPTCHA -->
            @if(!app()->environment('local') && !auth()->check())
            <div class="mb-3">
              <label for="captcha" class="form-label">{{ __('Security Check') }}</label>
              <div class="mt-2">
                {!! NoCaptcha::renderJs() !!}
                {!! NoCaptcha::display() !!}
                @error('g-recaptcha-response')
                  <div class="text-danger mt-2">{{ $message }}</div>
                @enderror
              </div>
            </div>
            @endif
            
            <div class="d-flex justify-content-end">
              <button type="submit" class="btn btn-primary">
                <i class="fas fa-reply me-1"></i>{{ __('Send Reply') }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
  // Auto-scroll to bottom of messages
  document.addEventListener('DOMContentLoaded', function() {
    const container = document.getElementById('messages-container');
    container.scrollTop = container.scrollHeight;
  });
</script>
@endsection
