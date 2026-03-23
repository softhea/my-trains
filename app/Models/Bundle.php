<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Bundle extends Model
{
    use LogsActivity;

    protected $fillable = [
        'name',
        'description',
        'price',
        'currency',
        'user_id',
        'is_active',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'bundle_products')
            ->withPivot('quantity')
            ->withTimestamps();
    }

    public function images()
    {
        return $this->morphMany(Image::class, 'imageable');
    }

    /**
     * Check if all products in the bundle have stock
     */
    public function hasStock(): bool
    {
        foreach ($this->products as $product) {
            $requiredQty = $product->pivot->quantity;
            if (!$product->hasStock($requiredQty)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Check if all products in the bundle are active
     */
    public function allProductsActive(): bool
    {
        return $this->products()->where('is_active', false)->count() === 0;
    }

    /**
     * Reduce stock for all products in the bundle
     */
    public function reduceStock(): bool
    {
        if (!$this->hasStock()) {
            return false;
        }

        foreach ($this->products as $product) {
            $product->reduceStock($product->pivot->quantity);
        }

        return true;
    }

    /**
     * Restore stock for all products in the bundle (for cancellations)
     */
    public function restoreStock(): void
    {
        foreach ($this->products as $product) {
            $product->restoreStock($product->pivot->quantity);
        }
    }

    /**
     * Get formatted price with currency
     */
    public function getFormattedPriceAttribute(): string
    {
        return format_currency((float) $this->price, $this->currency);
    }

    /**
     * Get the total value of individual products
     */
    public function getTotalProductsValueAttribute(): float
    {
        $total = 0;
        foreach ($this->products as $product) {
            $total += (float) $product->price * $product->pivot->quantity;
        }
        return $total;
    }

    /**
     * Get savings compared to buying products individually
     */
    public function getSavingsAttribute(): float
    {
        return $this->total_products_value - (float) $this->price;
    }

    /**
     * Get savings percentage
     */
    public function getSavingsPercentageAttribute(): float
    {
        if ($this->total_products_value <= 0) {
            return 0;
        }
        return round(($this->savings / $this->total_products_value) * 100, 1);
    }

    /**
     * Check if the bundle has meaningful savings to display
     * Returns false if products have 0 price (bundle-only products)
     */
    public function getHasMeaningfulSavingsAttribute(): bool
    {
        return $this->total_products_value > 0 && $this->savings > 0;
    }

    /**
     * Scope for active bundles
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for bundles by seller
     */
    public function scopeBySeller($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn(string $eventName) => "Bundle {$eventName}");
    }
}
