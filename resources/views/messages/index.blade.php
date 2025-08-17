@extends('layouts.app')

@section('title', __('Messages'))

@section('content')
<div class="container py-5">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h1>{{ __('Messages') }}</h1>
    <a href="{{ route('messages.create') }}" class="btn btn-primary">
      <i class="fas fa-plus me-1"></i>{{ __('New Message') }}
    </a>
  </div>

  @if($conversations->count() > 0)
    <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-body">
            <div class="list-group list-group-flush">
              @foreach($conversations as $otherUserId => $messages)
                @php
                  $latestMessage = $messages->first();
                  $otherUser = $latestMessage->sender_id === auth()->id() 
                    ? $latestMessage->receiver 
                    : $latestMessage->sender;
                  $unreadCount = $messages->where('receiver_id', auth()->id())->where('read_at', null)->count();
                @endphp
                
                <a href="{{ route('messages.conversation', $otherUser) }}" 
                   class="list-group-item list-group-item-action {{ $unreadCount > 0 ? 'border-start border-primary border-3' : '' }}">
                  <div class="d-flex w-100 justify-content-between align-items-start">
                    <div class="flex-grow-1">
                      <div class="d-flex align-items-center mb-1">
                        <div class="bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center me-3" 
                             style="width: 40px; height: 40px; font-size: 1rem; font-weight: bold;">
                          {{ strtoupper(substr($otherUser->name, 0, 1)) }}
                        </div>
                        <div>
                          <h6 class="mb-1 fw-bold">{{ $otherUser->name }}</h6>
                          <p class="mb-1 text-muted">{{ $latestMessage->subject }}</p>
                        </div>
                      </div>
                      <p class="mb-1">{{ \Illuminate\Support\Str::limit($latestMessage->message, 80) }}</p>
                      @if($latestMessage->product)
                        <small class="text-info">
                          <i class="fas fa-box me-1"></i>{{ __('About:') }} {{ $latestMessage->product->name }}
                        </small>
                      @endif
                    </div>
                    <div class="text-end">
                      <small class="text-muted">{{ $latestMessage->created_at->diffForHumans() }}</small>
                      @if($unreadCount > 0)
                        <br>
                        <span class="badge bg-primary">{{ $unreadCount }}</span>
                      @endif
                    </div>
                  </div>
                </a>
              @endforeach
            </div>
          </div>
        </div>
      </div>
    </div>
  @else
    <div class="text-center py-5">
      <i class="fas fa-comments fa-4x text-muted mb-3"></i>
      <h3>{{ __('No conversations yet') }}</h3>
      <p class="text-muted">{{ __('Start a conversation by sending a message to someone.') }}</p>
      <a href="{{ route('messages.create') }}" class="btn btn-primary">
        <i class="fas fa-plus me-1"></i>{{ __('Send Message') }}
      </a>
    </div>
  @endif
</div>
@endsection
