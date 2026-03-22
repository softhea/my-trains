<?php

declare(strict_types=1);

namespace App\Models;

use App\Jobs\ProcessImage;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Image extends Model
{
    use LogsActivity;

    protected $fillable = [
        'url',
        'imageable_type',
        'imageable_id',
        'is_processed',
        'original_url',
        'width',
        'height',
        'file_size',
    ];

    protected $casts = [
        'is_processed' => 'boolean',
        'width' => 'integer',
        'height' => 'integer',
        'file_size' => 'integer',
    ];

    /**
     * Accessor to ensure the URL is a fully-qualified asset URL for local files.
     */
    public function getUrlAttribute($value)
    {
        if (is_string($value) && preg_match('/^https?:\/\//i', $value)) {
            return $value;
        }

        $relative = ltrim((string) $value, '/');
        return asset($relative);
    }

    /**
     * Get the parent imageable model (product, category, or user).
     */
    public function imageable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Dispatch a job to process this image.
     */
    public function dispatchProcessing(?string $type = null): void
    {
        if (!$this->is_processed) {
            ProcessImage::dispatch($this, $type);
        }
    }

    /**
     * Check if image needs processing.
     */
    public function needsProcessing(): bool
    {
        return !$this->is_processed;
    }

    /**
     * Get human-readable file size.
     */
    public function getFormattedFileSizeAttribute(): ?string
    {
        if (!$this->file_size) {
            return null;
        }

        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = 0;
        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }
        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Get dimensions string.
     */
    public function getDimensionsAttribute(): ?string
    {
        if (!$this->width || !$this->height) {
            return null;
        }
        return "{$this->width}x{$this->height}";
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['url', 'is_processed', 'width', 'height', 'file_size'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn(string $eventName) => "Image {$eventName}");
    }
}
