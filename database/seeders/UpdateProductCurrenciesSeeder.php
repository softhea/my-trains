<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Product;

class UpdateProductCurrenciesSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        $currencies = ['RON', 'EUR'];
        
        $products = Product::whereNull('currency')
            ->orWhere('currency', '')
            ->get();
        
        foreach ($products as $product) {
            $randomCurrency = $currencies[array_rand($currencies)];
            $product->update(['currency' => $randomCurrency]);
        }

        $this->command->info("Updated {$products->count()} products with random currencies.");
    }
}