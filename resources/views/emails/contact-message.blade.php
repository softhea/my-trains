<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('New Contact Message') }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .header {
            background-color: #17a2b8;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 10px 10px 0 0;
            margin: -30px -30px 30px -30px;
        }
        .contact-details {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .detail-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            padding-bottom: 10px;
            border-bottom: 1px solid #dee2e6;
        }
        .detail-row:last-child {
            border-bottom: none;
            margin-bottom: 0;
        }
        .label {
            font-weight: bold;
            color: #495057;
            min-width: 120px;
        }
        .value {
            color: #212529;
            flex: 1;
            margin-left: 15px;
        }
        .message-content {
            background-color: #ffffff;
            border: 2px solid #17a2b8;
            border-radius: 5px;
            padding: 20px;
            margin: 20px 0;
            white-space: pre-wrap;
            word-wrap: break-word;
        }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #dee2e6;
            font-size: 14px;
            color: #6c757d;
        }
        .alert-info {
            background-color: #d1ecf1;
            border: 1px solid #bee5eb;
            color: #0c5460;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>{{ __('New Contact Message Received') }}</h1>
            <p>{{ __('Someone has sent a message through the contact form') }}</p>
        </div>

        <div class="contact-details">
            <h3>{{ __('Contact Information') }}</h3>
            <div class="detail-row">
                <span class="label">{{ __('Name:') }}</span>
                <span class="value">{{ $contactData['name'] }}</span>
            </div>
            <div class="detail-row">
                <span class="label">{{ __('Email:') }}</span>
                <span class="value">{{ $contactData['email'] }}</span>
            </div>
            <div class="detail-row">
                <span class="label">{{ __('Subject:') }}</span>
                <span class="value">{{ $contactData['subject'] }}</span>
            </div>
            <div class="detail-row">
                <span class="label">{{ __('Submitted:') }}</span>
                <span class="value">{{ $contactData['submitted_at']->format('M d, Y \a\t H:i') }}</span>
            </div>
        </div>

        <h3>{{ __('Message:') }}</h3>
        <div class="message-content">{{ $contactData['message'] }}</div>

        <div class="alert-info">
            <strong>{{ __('How to Reply:') }}</strong>
            <p style="margin: 5px 0 0 0;">
                {{ __('You can reply directly to this email to respond to :name at :email', [
                    'name' => $contactData['name'], 
                    'email' => $contactData['email']
                ]) }}
            </p>
        </div>

        <div class="footer">
            <p>{{ __('This message was sent through the contact form on :app_name', ['app_name' => config('app.name')]) }}</p>
            <p>{{ __('Received on :date', ['date' => $contactData['submitted_at']->format('F j, Y \a\t g:i A')]) }}</p>
        </div>
    </div>
</body>
</html>
