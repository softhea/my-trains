<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Image;

class CategorySeeder extends Seeder
{
    /**
     * Seed the application's categories.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'Steam Locomotives', 'parent_id' => null, 'image' => '/assets/img/gallery/country.png'],
            ['name' => 'Electric Trains',  'parent_id' => null, 'image' => '/assets/img/gallery/urban.png'],
            ['name' => 'Freight Cars',     'parent_id' => null, 'image' => '/assets/img/gallery/flat-hill.png'],
            ['name' => 'Passenger Cars',   'parent_id' => null, 'image' => '/assets/img/gallery/spring-dress-blog-3.png'],
            ['name' => 'Train Sets',       'parent_id' => null, 'image' => '/assets/img/gallery/ocean-blue.png'],
        ];

        foreach ($categories as $categoryData) {
            $category = Category::firstOrCreate(
                ['name' => $categoryData['name']],
                ['parent_id' => $categoryData['parent_id']]
            );

            // Ensure category has an image
            if (! $category->images()->exists()) {
                Image::create([
                    'imageable_type' => Category::class,
                    'imageable_id' => $category->id,
                    'url' => $categoryData['image'],
                ]);
            }
        }
    }
}


