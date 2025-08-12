<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    public $fillable = [
        'name',
        'description', 
        'price', 
        'no_of_items',
        'category_id',
        'views_count',
        'user_id',
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

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
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
}
