<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Order extends Model
{
    use LogsActivity;

    protected $fillable = [
        'order_product_id',
        'order_seller_id',
        'order_buyer_id',
        'quantity',
        'status',
        'total_price',
        'note',
    ];

    protected $casts = [
        'total_price' => 'decimal:2',
    ];

    /**
     * Get the product snapshot for this order.
     */
    public function orderProduct(): BelongsTo
    {
        return $this->belongsTo(OrderProduct::class);
    }

    /**
     * Get the seller snapshot for this order.
     */
    public function orderSeller(): BelongsTo
    {
        return $this->belongsTo(OrderSeller::class);
    }

    /**
     * Get the buyer snapshot for this order.
     */
    public function orderBuyer(): BelongsTo
    {
        return $this->belongsTo(OrderBuyer::class);
    }

    /**
     * Get the original product (may be null if deleted).
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'order_product_id', 'id')
            ->whereHas('orderProduct', function ($query) {
                $query->whereColumn('order_products.product_id', 'products.id');
            });
    }

    /**
     * Get the original seller user (may be null if deleted).
     */
    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'order_seller_id', 'id')
            ->whereHas('orderSeller', function ($query) {
                $query->whereColumn('order_sellers.user_id', 'users.id');
            });
    }

    /**
     * Get the original buyer user (may be null if deleted).
     */
    public function buyer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'order_buyer_id', 'id')
            ->whereHas('orderBuyer', function ($query) {
                $query->whereColumn('order_buyers.user_id', 'users.id');
            });
    }

    /**
     * Get product name (from snapshot).
     */
    public function getProductNameAttribute(): ?string
    {
        return $this->orderProduct?->name;
    }

    /**
     * Get product price (from snapshot).
     */
    public function getProductPriceAttribute(): ?string
    {
        return $this->orderProduct?->price;
    }

    /**
     * Get seller name (from snapshot).
     */
    public function getSellerNameAttribute(): ?string
    {
        return $this->orderSeller?->name;
    }

    /**
     * Get seller email (from snapshot).
     */
    public function getSellerEmailAttribute(): ?string
    {
        return $this->orderSeller?->email;
    }

    /**
     * Get buyer name (from snapshot).
     */
    public function getBuyerNameAttribute(): ?string
    {
        return $this->orderBuyer?->name;
    }

    /**
     * Get buyer email (from snapshot).
     */
    public function getBuyerEmailAttribute(): ?string
    {
        return $this->orderBuyer?->email;
    }

    /**
     * Check if order can be cancelled.
     */
    public function canBeCancelled(): bool
    {
        return in_array($this->status, ['pending', 'processing']);
    }

    /**
     * Cancel the order and restore stock.
     */
    public function cancel(): bool
    {
        if (!$this->canBeCancelled()) {
            return false;
        }

        $this->update(['status' => 'cancelled']);

        // Try to restore stock to original product if it still exists
        $originalProduct = $this->orderProduct?->product;
        if ($originalProduct) {
            $originalProduct->restoreStock($this->quantity);
        }

        return true;
    }

    /**
     * Get status color for display.
     */
    public function getStatusColor(): string
    {
        return match ($this->status) {
            'pending' => 'warning',
            'processing' => 'info',
            'completed' => 'success',
            'cancelled' => 'danger',
            default => 'secondary'
        };
    }

    /**
     * Get formatted status.
     */
    public function getFormattedStatus(): string
    {
        return match ($this->status) {
            'pending' => __('Pending'),
            'processing' => __('Processing'),
            'completed' => __('Completed'),
            'cancelled' => __('Cancelled'),
            default => ucfirst($this->status)
        };
    }

    /**
     * Create an order with snapshots.
     */
    public static function createWithSnapshots(
        Product $product,
        User $buyer,
        int $quantity,
        ?string $note = null
    ): self {
        $seller = $product->user;

        // Create snapshots
        $orderProduct = OrderProduct::createFromProduct($product);
        $orderSeller = $seller ? OrderSeller::createFromUser($seller) : null;
        $orderBuyer = OrderBuyer::createFromUser($buyer);

        // Calculate total price
        $totalPrice = $product->price * $quantity;

        // Create order
        $order = self::create([
            'order_product_id' => $orderProduct->id,
            'order_seller_id' => $orderSeller?->id,
            'order_buyer_id' => $orderBuyer->id,
            'quantity' => $quantity,
            'status' => 'pending',
            'total_price' => $totalPrice,
            'note' => $note,
        ]);

        // Reduce product stock
        $product->reduceStock($quantity);

        return $order;
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn(string $eventName) => "Order {$eventName}");
    }
}
