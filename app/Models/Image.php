<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Image extends Model
{
    protected $fillable = [
        'url',
        'imageable_type',
        'imageable_id',
    ];

    /**
     * Accessor to ensure the URL is a fully-qualified asset URL for local files.
     */
    public function getUrlAttribute($value)
    {
        if (is_string($value) && preg_match('/^https?:\/\//i', $value)) {
            return $value; // external URL
        }

        // Normalize and generate a full asset URL (e.g., http://host/storage/...)
        $relative = ltrim((string) $value, '/');
        return asset($relative);
    }

    /**
     * Get the parent imageable model (product or category).
     */
    public function imageable(): MorphTo
    {
        return $this->morphTo();
    }
}
