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
     * Get the parent imageable model (product or category).
     */
    public function imageable(): MorphTo
    {
        return $this->morphTo();
    }
}
