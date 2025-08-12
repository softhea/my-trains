<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Image;

class FixProductImagesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $replacements = [
            7 => '/assets/img/gallery/flat-hill.png',
            9 => '/assets/img/gallery/urban.png',
        ];

        foreach ($replacements as $productId => $imagePath) {
            $product = Product::find($productId);
            if (!$product) {
                $this->command?->warn("Product {$productId} not found, skipping.");
                continue;
            }

            $image = $product->images()->first();
            if ($image) {
                $image->update(['url' => $imagePath]);
                $this->command?->info("Updated image for Product {$productId}.");
            } else {
                Image::create([
                    'imageable_type' => Product::class,
                    'imageable_id' => $product->id,
                    'url' => $imagePath,
                ]);
                $this->command?->info("Created image for Product {$productId}.");
            }
        }
    }
}


