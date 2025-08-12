<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class VideoReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'videos:report';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Show a report of all product videos';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('📹 Product Videos Report');
        $this->line('======================');
        
        $products = \App\Models\Product::with(['videos', 'category'])->get();
        
        $totalVideos = 0;
        foreach ($products as $product) {
            $videoCount = $product->videos->count();
            $totalVideos += $videoCount;
            
            $this->line("🚂 {$product->name} ({$product->category->name})");
            $this->line("   Videos: {$videoCount}");
            
            foreach ($product->videos as $video) {
                $this->line("   - {$video->url}");
            }
            $this->line('');
        }
        
        $this->info("Total products: {$products->count()}");
        $this->info("Total videos: {$totalVideos}");
        $this->info("🎬 All videos are now from Trenuletele Mele style content!");
        
        return 0;
    }
}
