<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;
use App\Models\Image;
use App\Models\Video;

class TrainProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get categories
        $steamCategory = Category::where('name', 'Steam Locomotives')->first();
        $electricCategory = Category::where('name', 'Electric Trains')->first();
        $freightCategory = Category::where('name', 'Freight Cars')->first();
        $passengerCategory = Category::where('name', 'Passenger Cars')->first();
        $trainSetCategory = Category::where('name', 'Train Sets')->first();

        // YouTube videos from model train channels (replace with Trenuletele Mele videos)
        $youtubeVideos = [
            'https://www.youtube.com/watch?v=Zf3wgHTvgcI',
            'https://www.youtube.com/watch?v=5AurcPkhSjY',
            'https://www.youtube.com/watch?v=DmD3jcHF66A',
            'https://www.youtube.com/watch?v=UT7qcNsiHgk',
            'https://www.youtube.com/watch?v=QTe-dVxoFhw',
        ];

        $products = [
            [
                'name' => 'DB BR 01 Steam Locomotive',
                'description' => 'Authentic German DB BR 01 steam locomotive with detailed weathering and sound effects. Perfect for model railway enthusiasts who appreciate historical accuracy.',
                'price' => 299.99,
                'currency' => 'EUR',
                'no_of_items' => 15,
                'category_id' => $steamCategory->id,
                'image_url' => 'https://images.unsplash.com/photo-1544620347-c4fd4a3d5957?w=500&h=400&fit=crop',
                'videos' => [$youtubeVideos[0]]
            ],
            [
                'name' => 'ICE 3 High-Speed Electric Train',
                'description' => 'Modern ICE 3 high-speed electric train set with LED lighting and digital control. Features realistic acceleration and braking sounds.',
                'price' => 449.99,
                'currency' => 'EUR',
                'no_of_items' => 8,
                'category_id' => $electricCategory->id,
                'image_url' => 'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=500&h=400&fit=crop',
                'videos' => [$youtubeVideos[1], $youtubeVideos[2]]
            ],
            [
                'name' => 'Union Pacific Freight Car Set',
                'description' => 'Complete set of 6 Union Pacific freight cars including boxcar, tank car, and flatbed. High-quality die-cast construction with authentic livery.',
                'price' => 189.99,
                'no_of_items' => 22,
                'category_id' => $freightCategory->id,
                'image_url' => 'https://images.unsplash.com/photo-1506905925346-21bda4d32df4?w=500&h=400&fit=crop',
                'videos' => [$youtubeVideos[3]]
            ],
            [
                'name' => 'Orient Express Passenger Cars',
                'description' => 'Luxurious Orient Express passenger car set with detailed interior lighting and elegant blue livery. Includes dining car and sleeping cars.',
                'price' => 359.99,
                'no_of_items' => 12,
                'category_id' => $passengerCategory->id,
                'image_url' => 'https://images.unsplash.com/photo-1474487548417-781cb71495f3?w=500&h=400&fit=crop',
                'videos' => [$youtubeVideos[4]]
            ],
            [
                'name' => 'Märklin Starter Train Set',
                'description' => 'Complete starter set perfect for beginners. Includes locomotive, 3 cars, oval track, transformer, and detailed instruction manual.',
                'price' => 149.99,
                'no_of_items' => 25,
                'category_id' => $trainSetCategory->id,
                'image_url' => 'https://images.unsplash.com/photo-1518611012118-696072aa579a?w=500&h=400&fit=crop',
                'videos' => []
            ],
            [
                'name' => 'Pennsylvania Railroad Steam Engine',
                'description' => 'Classic Pennsylvania Railroad K4 Pacific steam locomotive with tender. Features working headlight and authentic PRR livery.',
                'price' => 275.50,
                'no_of_items' => 18,
                'category_id' => $steamCategory->id,
                'image_url' => 'https://images.unsplash.com/photo-1544620347-c4fd4a3d5957?w=500&h=400&fit=crop&q=80',
                'videos' => [$youtubeVideos[0]]
            ],
            [
                'name' => 'TGV Duplex Electric Train',
                'description' => 'French TGV Duplex double-decker high-speed train with realistic pantograph and interior details. Digital ready with DCC decoder.',
                'price' => 399.99,
                'no_of_items' => 6,
                'category_id' => $electricCategory->id,
                'image_url' => 'https://images.unsplash.com/photo-1558618047-3c8c76ca7d13?w=500&h=400&fit=crop',
                'videos' => [$youtubeVideos[1]]
            ],
            [
                'name' => 'Burlington Northern Tank Cars',
                'description' => 'Set of 4 Burlington Northern tank cars for transporting liquids. Detailed undercarriage and authentic weathering effects.',
                'price' => 129.99,
                'no_of_items' => 30,
                'category_id' => $freightCategory->id,
                'image_url' => 'https://images.unsplash.com/photo-1506905925346-21bda4d32df4?w=500&h=400&fit=crop&q=75',
                'videos' => []
            ],
            [
                'name' => 'Swiss Alpine Express Cars',
                'description' => 'Scenic Swiss Alpine Express passenger cars with panoramic windows. Perfect for mountain railway layouts with detailed Alpine livery.',
                'price' => 219.99,
                'no_of_items' => 14,
                'category_id' => $passengerCategory->id,
                'image_url' => 'https://images.unsplash.com/photo-1588063839811-d21e5201ad7a?w=500&h=400&fit=crop',
                'videos' => [$youtubeVideos[2], $youtubeVideos[3]]
            ],
            [
                'name' => 'Complete Freight Yard Set',
                'description' => 'Comprehensive freight yard starter set with locomotive, 8 freight cars, loading dock, and extended track layout. Everything needed for freight operations.',
                'price' => 199.99,
                'no_of_items' => 10,
                'category_id' => $trainSetCategory->id,
                'image_url' => 'https://images.unsplash.com/photo-1520340356584-f9917d1eea6f?w=500&h=400&fit=crop',
                'videos' => [$youtubeVideos[4]]
            ]
        ];

        // Get available users for random assignment
        $userIds = \App\Models\User::pluck('id')->toArray();

        foreach ($products as $productData) {
            // Create the product
            $product = Product::create([
                'name' => $productData['name'],
                'description' => $productData['description'],
                'price' => $productData['price'],
                'currency' => $productData['currency'] ?? ['RON', 'EUR'][array_rand(['RON', 'EUR'])],
                'no_of_items' => $productData['no_of_items'],
                'category_id' => $productData['category_id'],
                'user_id' => !empty($userIds) ? $userIds[array_rand($userIds)] : null,
            ]);

            // Add image
            Image::create([
                'imageable_type' => Product::class,
                'imageable_id' => $product->id,
                'url' => $productData['image_url'],
            ]);

            // Add videos if any
            foreach ($productData['videos'] as $videoUrl) {
                Video::create([
                    'product_id' => $product->id,
                    'url' => $videoUrl,
                ]);
            }

            echo "Created product: {$productData['name']}\n";
        }

        echo "Created 10 train products successfully!\n";
    }
}