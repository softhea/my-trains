<?php

namespace App\Jobs;

use App\Models\Image;
use App\Services\ImageService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessImage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     */
    public int $tries = 3;

    /**
     * The number of seconds to wait before retrying the job.
     */
    public int $backoff = 30;

    /**
     * The maximum number of seconds the job can run.
     */
    public int $timeout = 120;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public Image $image,
        public ?string $type = null
    ) {}

    /**
     * Execute the job.
     */
    public function handle(ImageService $imageService): void
    {
        if ($this->image->is_processed) {
            Log::info("Image already processed, skipping", ['image_id' => $this->image->id]);
            return;
        }

        $type = $this->type ?? $imageService->getImageType($this->image);
        
        $success = $imageService->processImage($this->image, $type);
        
        if (!$success) {
            Log::warning("Image processing failed", [
                'image_id' => $this->image->id,
                'attempt' => $this->attempts()
            ]);
            
            if ($this->attempts() >= $this->tries) {
                $this->image->update(['is_processed' => true]);
                Log::error("Image processing failed after max attempts, marking as processed to prevent infinite retries", [
                    'image_id' => $this->image->id
                ]);
            } else {
                $this->release($this->backoff);
            }
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error("ProcessImage job failed permanently", [
            'image_id' => $this->image->id,
            'error' => $exception->getMessage()
        ]);
        
        $this->image->update(['is_processed' => true]);
    }
}
