<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\User;

class AssignUsersToProductsSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        $users = User::pluck('id')->toArray();
        
        if (empty($users)) {
            $this->command->error('No users found in the database. Please run UserSeeder first.');
            return;
        }

        $products = Product::whereNull('user_id')->get();
        
        if ($products->isEmpty()) {
            $this->command->info('All products already have users assigned.');
            return;
        }

        foreach ($products as $product) {
            $randomUserId = $users[array_rand($users)];
            $product->update(['user_id' => $randomUserId]);
        }

        $this->command->info("Assigned random users to {$products->count()} products.");
    }
}