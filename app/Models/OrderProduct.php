<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class OrderProduct extends Model
{
    use LogsActivity;

    protected $fillable = [
        'product_id',
        'name',
        'description',
        'price',
        'currency',
        'category_id',
        'category_name',
    ];

    protected $casts = [
        'price' => 'decimal:2',
    ];

    /**
     * Get the original product (may be null if deleted).
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get orders using this product snapshot.
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Get formatted price with currency.
     */
    public function getFormattedPriceAttribute(): string
    {
        return format_currency((float) $this->price, $this->currency);
    }

    /**
     * Create a snapshot from a Product model.
     */
    public static function createFromProduct(Product $product): self
    {
        return self::create([
            'product_id' => $product->id,
            'name' => $product->name,
            'description' => $product->description,
            'price' => $product->price,
            'currency' => $product->currency ?? 'RON',
            'category_id' => $product->category_id,
            'category_name' => $product->category?->name,
        ]);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn(string $eventName) => "OrderProduct {$eventName}");
    }
}
