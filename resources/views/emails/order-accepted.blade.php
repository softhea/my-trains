<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Order Accepted') }}</title>
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
            background-color: #28a745;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 10px 10px 0 0;
            margin: -30px -30px 30px -30px;
        }
        .order-details {
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
        }
        .value {
            color: #212529;
        }
        .product-info {
            border: 1px solid #dee2e6;
            border-radius: 5px;
            padding: 15px;
            margin: 15px 0;
        }
        .status-badge {
            background-color: #28a745;
            color: white;
            padding: 5px 15px;
            border-radius: 20px;
            display: inline-block;
            font-weight: bold;
        }
        .button {
            display: inline-block;
            background-color: #007bff;
            color: white;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
        }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #dee2e6;
            font-size: 14px;
            color: #6c757d;
        }
        .highlight-box {
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>{{ __('Great News! Your Order Has Been Accepted!') }}</h1>
            <p>{{ __('Order #:order_id', ['order_id' => $order->id]) }}</p>
        </div>

        <p>{{ __('Hello :buyer_name,', ['buyer_name' => $buyer->name]) }}</p>
        
        <div class="highlight-box">
            <p style="margin: 0;"><strong>{{ __('Your order has been accepted by the seller and is now being processed!') }}</strong></p>
        </div>

        <div class="order-details">
            <div class="detail-row">
                <span class="label">{{ __('Order ID:') }}</span>
                <span class="value">#{{ $order->id }}</span>
            </div>
            <div class="detail-row">
                <span class="label">{{ __('Order Date:') }}</span>
                <span class="value">{{ $order->created_at->format('M d, Y \a\t H:i') }}</span>
            </div>
            <div class="detail-row">
                <span class="label">{{ __('Seller:') }}</span>
                <span class="value">{{ $seller->name }}</span>
            </div>
            @if($seller->phone)
            <div class="detail-row">
                <span class="label">{{ __('Seller Phone:') }}</span>
                <span class="value">{{ $seller->phone }}</span>
            </div>
            @endif
            <div class="detail-row">
                <span class="label">{{ __('Quantity:') }}</span>
                <span class="value">{{ $order->quantity }}</span>
            </div>
            <div class="detail-row">
                <span class="label">{{ __('Total Price:') }}</span>
                <span class="value">${{ number_format($order->total_price, 2) }}</span>
            </div>
            <div class="detail-row">
                <span class="label">{{ __('Status:') }}</span>
                <span class="value"><span class="status-badge">{{ __('Processing') }}</span></span>
            </div>
        </div>

        <div class="product-info">
            <h3>{{ __('Product Details') }}</h3>
            <div class="detail-row">
                <span class="label">{{ __('Product Name:') }}</span>
                <span class="value">{{ $product->name }}</span>
            </div>
            <div class="detail-row">
                <span class="label">{{ __('Price per item:') }}</span>
                <span class="value">${{ number_format($product->price, 2) }}</span>
            </div>
        </div>

        @if($order->note)
        <div style="margin: 20px 0;">
            <h4>{{ __('Your Note:') }}</h4>
            <p style="background-color: #f8f9fa; padding: 15px; border-radius: 5px; border-left: 4px solid #007bff;">
                {{ $order->note }}
            </p>
        </div>
        @endif

        <div style="text-align: center;">
            <a href="{{ route('orders.index') }}" class="button">
                {{ __('View My Orders') }}
            </a>
        </div>

        <p>{{ __('The seller will contact you soon regarding delivery or pickup arrangements. You can track your order status in your dashboard.') }}</p>

        <div class="footer">
            <p>{{ __('Thank you for your purchase!') }}</p>
            <p>{{ __('Best regards,') }}<br>{{ config('app.name') }} {{ __('Team') }}</p>
        </div>
    </div>
</body>
</html>
