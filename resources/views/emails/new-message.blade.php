<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('New Message from :sender', ['sender' => $sender->name]) }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .message-content {
            background-color: #ffffff;
            padding: 20px;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .product-info {
            background-color: #e9ecef;
            padding: 15px;
            border-radius: 8px;
            margin: 15px 0;
        }
        .button {
            display: inline-block;
            padding: 12px 24px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin: 10px 0;
        }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #dee2e6;
            font-size: 14px;
            color: #6c757d;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ __('New Message from :sender', ['sender' => $sender->name]) }}</h1>
        <p>{{ __('You have received a new message on :app_name', ['app_name' => config('app.name')]) }}</p>
    </div>

    <div class="message-content">
        <h2>{{ __('Subject: :subject', ['subject' => $userMessage->subject]) }}</h2>
        
        @if($product)
            <div class="product-info">
                <strong>{{ __('Regarding Product:') }}</strong> {{ $product->name }}
                <br>
                <small>{{ __('Price:') }} {{ $product->price }} {{ $product->currency }}</small>
            </div>
        @endif

        <h3>{{ __('Message:') }}</h3>
        <p style="white-space: pre-wrap;">{{ $userMessage->message }}</p>

        <p><strong>{{ __('From:') }}</strong> {{ $sender->name }}</p>
        <!-- @if($sender->email)
            <p><strong>{{ __('Email:') }}</strong> {{ $sender->email }}</p>
        @endif -->
        <!-- @if($sender->phone)
            <p><strong>{{ __('Phone:') }}</strong> {{ $sender->phone }}</p>
        @endif
        @if($sender->city)
            <p><strong>{{ __('City:') }}</strong> {{ $sender->city }}</p>
        @endif -->

        <p><strong>{{ __('Sent:') }}</strong> {{ $userMessage->created_at->format('F j, Y \a\t g:i A') }}</p>
    </div>

    <div style="text-align: center;">
        <a href="{{ route('messages.conversation', $sender) }}" class="button">
            {{ __('Reply to Message') }}
        </a>
    </div>

    <div class="footer">
        <p>{{ __('You are receiving this email because you have an account on :app_name and someone sent you a message.', ['app_name' => config('app.name')]) }}</p>
        <p>{{ __('If you no longer wish to receive these notifications, you can adjust your settings in your account.') }}</p>
        <p>{{ __('This is an automated email, please do not reply directly to this email.') }}</p>
    </div>
</body>
</html>
