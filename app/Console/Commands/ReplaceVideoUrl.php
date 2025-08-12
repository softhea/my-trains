<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ReplaceVideoUrl extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'videos:replace {old_url} {new_url}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Replace a video URL with a new one from Trenuletele Mele channel';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $oldUrl = $this->argument('old_url');
        $newUrl = $this->argument('new_url');
        
        $videos = \App\Models\Video::where('url', $oldUrl)->get();
        
        if ($videos->isEmpty()) {
            $this->error("No videos found with URL: {$oldUrl}");
            return 1;
        }
        
        $this->info("Found {$videos->count()} video(s) to update.");
        
        foreach ($videos as $video) {
            $productName = $video->product->name;
            $video->update(['url' => $newUrl]);
            $this->line("Updated video for product: {$productName}");
        }
        
        $this->info("✅ Successfully replaced {$videos->count()} video(s)!");
        $this->info("🎬 Old URL: {$oldUrl}");
        $this->info("🎬 New URL: {$newUrl}");
        
        return 0;
    }
}
