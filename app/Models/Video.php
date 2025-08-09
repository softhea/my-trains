<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Video extends Model
{
    protected $fillable = [
        'url',
        'product_id',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the YouTube embed URL from various YouTube URL formats
     */
    public function getEmbedUrlAttribute(): string
    {
        return $this->convertToEmbedUrl($this->url);
    }

    /**
     * Convert various YouTube URL formats to embed format
     */
    public static function convertToEmbedUrl(string $url): string
    {
        // If it's already an embed URL, return as-is
        if (strpos($url, 'youtube.com/embed/') !== false) {
            return $url;
        }

        // Extract video ID from various YouTube URL formats
        $videoId = null;
        
        // Standard youtube.com/watch?v=VIDEO_ID
        if (preg_match('/youtube\.com\/watch\?v=([^&]+)/', $url, $matches)) {
            $videoId = $matches[1];
        }
        // Short youtu.be/VIDEO_ID
        elseif (preg_match('/youtu\.be\/([^?]+)/', $url, $matches)) {
            $videoId = $matches[1];
        }
        // Mobile youtube.com/v/VIDEO_ID
        elseif (preg_match('/youtube\.com\/v\/([^?]+)/', $url, $matches)) {
            $videoId = $matches[1];
        }
        // Already embed format youtube.com/embed/VIDEO_ID
        elseif (preg_match('/youtube\.com\/embed\/([^?]+)/', $url, $matches)) {
            $videoId = $matches[1];
        }

        // If we found a video ID, create embed URL
        if ($videoId) {
            return "https://www.youtube.com/embed/{$videoId}";
        }

        // If we can't parse it, return original URL
        return $url;
    }

    /**
     * Get YouTube video thumbnail URL
     */
    public function getThumbnailUrlAttribute(): string
    {
        $embedUrl = $this->embed_url;
        
        // Extract video ID from embed URL
        if (preg_match('/youtube\.com\/embed\/([^?]+)/', $embedUrl, $matches)) {
            $videoId = $matches[1];
            return "https://img.youtube.com/vi/{$videoId}/hqdefault.jpg";
        }

        return '';
    }

    /**
     * Check if the URL is a valid YouTube URL
     */
    public function isValidYouTubeUrl(): bool
    {
        $patterns = [
            '/youtube\.com\/watch\?v=/',
            '/youtu\.be\//',
            '/youtube\.com\/v\//',
            '/youtube\.com\/embed\//'
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $this->url)) {
                return true;
            }
        }

        return false;
    }
}
