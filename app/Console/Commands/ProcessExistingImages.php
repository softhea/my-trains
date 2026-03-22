<?php

namespace App\Console\Commands;

use App\Jobs\ProcessImage;
use App\Models\Image;
use App\Services\ImageService;
use Illuminate\Console\Command;

class ProcessExistingImages extends Command
{
    protected $signature = 'images:process 
                            {--sync : Process images synchronously instead of queuing}
                            {--type= : Filter by image type (profile, product, category)}
                            {--limit= : Limit number of images to process}
                            {--force : Reprocess already processed images}';

    protected $description = 'Process existing unprocessed images (resize and optimize)';

    public function handle(ImageService $imageService): int
    {
        $query = Image::query();
        
        if (!$this->option('force')) {
            $query->where('is_processed', false);
        }
        
        if ($type = $this->option('type')) {
            $modelClass = match ($type) {
                'profile' => 'App\Models\User',
                'product' => 'App\Models\Product',
                'category' => 'App\Models\Category',
                default => null,
            };
            
            if ($modelClass) {
                $query->where('imageable_type', $modelClass);
            } else {
                $this->error("Invalid type: {$type}. Use: profile, product, or category");
                return self::FAILURE;
            }
        }
        
        if ($limit = $this->option('limit')) {
            $query->limit((int) $limit);
        }
        
        $images = $query->get();
        $count = $images->count();
        
        if ($count === 0) {
            $this->info('No images to process.');
            return self::SUCCESS;
        }
        
        $this->info("Found {$count} images to process.");
        
        if ($this->option('sync')) {
            $this->info('Processing images synchronously...');
            $bar = $this->output->createProgressBar($count);
            $bar->start();
            
            $processed = 0;
            $failed = 0;
            
            foreach ($images as $image) {
                $type = $imageService->getImageType($image);
                $success = $imageService->processImage($image, $type);
                
                if ($success) {
                    $processed++;
                } else {
                    $failed++;
                }
                
                $bar->advance();
            }
            
            $bar->finish();
            $this->newLine(2);
            
            $this->info("Processed: {$processed}, Failed: {$failed}");
        } else {
            $this->info('Dispatching jobs to queue...');
            $bar = $this->output->createProgressBar($count);
            $bar->start();
            
            foreach ($images as $image) {
                $type = $imageService->getImageType($image);
                ProcessImage::dispatch($image, $type);
                $bar->advance();
            }
            
            $bar->finish();
            $this->newLine(2);
            
            $this->info("Dispatched {$count} jobs to the queue.");
            $this->info('Run `php artisan queue:work` to process them.');
        }
        
        return self::SUCCESS;
    }
}
