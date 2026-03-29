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
     * Access via: $order->orderProduct?->product
     */
    public function getOriginalProductAttribute(): ?Product
    {
        return $this->orderProduct?->product;
    }

    /**
     * Get the original seller user (may be null if deleted).
     * Access via: $order->orderSeller?->user
     */
    public function getOriginalSellerAttribute(): ?User
    {
        return $this->orderSeller?->user;
    }

    /**
     * Get the original buyer user (may be null if deleted).
     * Access via: $order->orderBuyer?->user
     */
    public function getOriginalBuyerAttribute(): ?User
    {
        return $this->orderBuyer?->user;
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
     * Cancel the order and restore stock if applicable.
     * Stock is only restored if the order was in 'processing' or 'completed' status
     * (pending orders never had stock reduced).
     */
    public function cancel(): bool
    {
        if (!$this->canBeCancelled()) {
            return false;
        }

        $oldStatus = $this->status;
        $this->update(['status' => 'cancelled']);

        // Only restore stock if it was actually reduced (processing or completed orders)
        if (in_array($oldStatus, ['processing', 'completed'])) {
            $originalProduct = $this->orderProduct?->product;
            if ($originalProduct) {
                $originalProduct->restoreStock($this->quantity);
            }
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
     * 
     * @param Product $product The product being ordered
     * @param User $buyer The user placing the order
     * @param int $quantity Number of items
     * @param string|null $note Optional order note
     * @param float|null $customPrice Optional custom total price (for bundle orders)
     */
    public static function createWithSnapshots(
        Product $product,
        User $buyer,
        int $quantity,
        ?string $note = null,
        ?float $customPrice = null
    ): self {
        $seller = $product->user;

        // Create snapshots
        $orderProduct = OrderProduct::createFromProduct($product);
        $orderSeller = $seller ? OrderSeller::createFromUser($seller) : null;
        $orderBuyer = OrderBuyer::createFromUser($buyer);

        // Calculate total price (use custom price if provided, e.g., for bundle discounts)
        $totalPrice = $customPrice ?? ($product->price * $quantity);

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
