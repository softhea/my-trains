<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Product extends Model
{
    use LogsActivity;

    public const AVAILABILITY_STANDALONE = 'standalone';
    public const AVAILABILITY_BUNDLE_ONLY = 'bundle_only';
    public const AVAILABILITY_BOTH = 'both';

    public $fillable = [
        'name',
        'description', 
        'price', 
        'currency',
        'no_of_items',
        'category_id',
        'views_count',
        'user_id',
        'is_active',
        'availability',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function images()
    {
        return $this->morphMany(Image::class, 'imageable');
    }

    public function videos(): HasMany
    {
        return $this->hasMany(Video::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get orders for this product (via order_products snapshot).
     */
    public function orders(): HasManyThrough
    {
        return $this->hasManyThrough(
            Order::class,
            OrderProduct::class,
            'product_id',
            'order_product_id',
            'id',
            'id'
        );
    }

    /**
     * Check if product has enough stock
     */
    public function hasStock(int $quantity = 1): bool
    {
        return $this->no_of_items >= $quantity;
    }

    /**
     * Check if product is out of stock
     */
    public function isOutOfStock(): bool
    {
        return $this->no_of_items <= 0;
    }

    /**
     * Get stock status
     */
    public function getStockStatus(): string
    {
        if ($this->no_of_items <= 0) {
            return 'out_of_stock';
        } elseif ($this->no_of_items <= 5) {
            return 'low_stock';
        } else {
            return 'in_stock';
        }
    }

    /**
     * Reduce stock quantity
     */
    public function reduceStock(int $quantity): bool
    {
        if (!$this->hasStock($quantity)) {
            return false;
        }

        $this->decrement('no_of_items', $quantity);
        return true;
    }

    /**
     * Restore stock quantity (for order cancellations)
     */
    public function restoreStock(int $quantity): void
    {
        $this->increment('no_of_items', $quantity);
    }

    /**
     * Get formatted price with currency
     */
    public function getFormattedPriceAttribute(): string
    {
        return format_currency((float) $this->price, $this->currency);
    }

    /**
     * Get available currencies
     */
    public static function getAvailableCurrencies(): array
    {
        return [
            'RON' => 'RON (Romanian Leu)',
            'EUR' => 'EUR (Euro)',
        ];
    }

    /**
     * Get bundles that include this product
     */
    public function bundles(): BelongsToMany
    {
        return $this->belongsToMany(Bundle::class, 'bundle_products')
            ->withPivot('quantity')
            ->withTimestamps();
    }

    /**
     * Check if product can be purchased standalone
     */
    public function canPurchaseStandalone(): bool
    {
        return $this->is_active 
            && in_array($this->availability, [self::AVAILABILITY_STANDALONE, self::AVAILABILITY_BOTH]);
    }

    /**
     * Check if product is available in bundles
     */
    public function isAvailableInBundle(): bool
    {
        return in_array($this->availability, [self::AVAILABILITY_BUNDLE_ONLY, self::AVAILABILITY_BOTH]);
    }

    /**
     * Check if product is bundle-only
     */
    public function isBundleOnly(): bool
    {
        return $this->availability === self::AVAILABILITY_BUNDLE_ONLY;
    }

    /**
     * Get active bundles containing this product
     */
    public function getActiveBundlesAttribute()
    {
        return $this->bundles()->where('is_active', true)->get();
    }

    /**
     * Deactivate all bundles containing this product
     */
    public function deactivateBundles(): void
    {
        $this->bundles()->update(['is_active' => false]);
    }

    /**
     * Scope for active products
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for products available standalone
     */
    public function scopeAvailableStandalone($query)
    {
        return $query->whereIn('availability', [self::AVAILABILITY_STANDALONE, self::AVAILABILITY_BOTH]);
    }

    /**
     * Scope for products available in bundles
     */
    public function scopeAvailableInBundle($query)
    {
        return $query->whereIn('availability', [self::AVAILABILITY_BUNDLE_ONLY, self::AVAILABILITY_BOTH]);
    }

    /**
     * Get available availability options
     */
    public static function getAvailabilityOptions(): array
    {
        return [
            self::AVAILABILITY_STANDALONE => __('Standalone only'),
            self::AVAILABILITY_BUNDLE_ONLY => __('Bundle only'),
            self::AVAILABILITY_BOTH => __('Both standalone and bundle'),
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn(string $eventName) => "Product {$eventName}");
    }
}
