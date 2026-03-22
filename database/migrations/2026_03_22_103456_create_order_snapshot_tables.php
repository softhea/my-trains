<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Create order_products table (snapshot of product at time of order)
        Schema::create('order_products', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id')->nullable()->index();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2);
            $table->string('currency', 3)->default('RON');
            $table->unsignedBigInteger('category_id')->nullable();
            $table->string('category_name')->nullable();
            $table->timestamps();
        });

        // Create order_sellers table (snapshot of seller at time of order)
        Schema::create('order_sellers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->string('name');
            $table->string('email');
            $table->string('phone')->nullable();
            $table->string('city')->nullable();
            $table->timestamps();
        });

        // Create order_buyers table (snapshot of buyer at time of order)
        Schema::create('order_buyers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->string('name');
            $table->string('email');
            $table->string('phone')->nullable();
            $table->string('city')->nullable();
            $table->timestamps();
        });

        // Add new foreign key columns to orders table
        Schema::table('orders', function (Blueprint $table) {
            $table->unsignedBigInteger('order_product_id')->nullable()->after('id');
            $table->unsignedBigInteger('order_seller_id')->nullable()->after('order_product_id');
            $table->unsignedBigInteger('order_buyer_id')->nullable()->after('order_seller_id');
        });

        // Migrate existing data
        $this->migrateExistingOrders();

        // Add foreign key constraints after data migration
        Schema::table('orders', function (Blueprint $table) {
            $table->foreign('order_product_id')->references('id')->on('order_products')->onDelete('set null');
            $table->foreign('order_seller_id')->references('id')->on('order_sellers')->onDelete('set null');
            $table->foreign('order_buyer_id')->references('id')->on('order_buyers')->onDelete('set null');
        });

        // Remove old columns (keep them nullable for now, can be removed in a future migration)
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['user_id', 'seller_id', 'product_id']);
        });
    }

    private function migrateExistingOrders(): void
    {
        $orders = DB::table('orders')
            ->select('orders.*')
            ->get();

        foreach ($orders as $order) {
            // Get product data
            $product = DB::table('products')->where('id', $order->product_id)->first();
            $category = $product ? DB::table('categories')->where('id', $product->category_id)->first() : null;

            // Get seller data
            $seller = DB::table('users')->where('id', $order->seller_id)->first();

            // Get buyer data
            $buyer = DB::table('users')->where('id', $order->user_id)->first();

            // Create order_product snapshot
            $orderProductId = null;
            if ($product) {
                $orderProductId = DB::table('order_products')->insertGetId([
                    'product_id' => $product->id,
                    'name' => $product->name,
                    'description' => $product->description,
                    'price' => $product->price,
                    'currency' => $product->currency ?? 'RON',
                    'category_id' => $product->category_id,
                    'category_name' => $category?->name,
                    'created_at' => $order->created_at,
                    'updated_at' => $order->created_at,
                ]);
            }

            // Create order_seller snapshot
            $orderSellerId = null;
            if ($seller) {
                $orderSellerId = DB::table('order_sellers')->insertGetId([
                    'user_id' => $seller->id,
                    'name' => $seller->name,
                    'email' => $seller->email,
                    'phone' => $seller->phone,
                    'city' => $seller->city,
                    'created_at' => $order->created_at,
                    'updated_at' => $order->created_at,
                ]);
            }

            // Create order_buyer snapshot
            $orderBuyerId = null;
            if ($buyer) {
                $orderBuyerId = DB::table('order_buyers')->insertGetId([
                    'user_id' => $buyer->id,
                    'name' => $buyer->name,
                    'email' => $buyer->email,
                    'phone' => $buyer->phone,
                    'city' => $buyer->city,
                    'created_at' => $order->created_at,
                    'updated_at' => $order->created_at,
                ]);
            }

            // Update order with new foreign keys
            DB::table('orders')->where('id', $order->id)->update([
                'order_product_id' => $orderProductId,
                'order_seller_id' => $orderSellerId,
                'order_buyer_id' => $orderBuyerId,
            ]);
        }
    }

    public function down(): void
    {
        // Re-add old columns
        Schema::table('orders', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable()->after('order_buyer_id');
            $table->unsignedBigInteger('seller_id')->nullable()->after('user_id');
            $table->unsignedBigInteger('product_id')->nullable()->after('seller_id');
        });

        // Restore data from snapshots
        $orders = DB::table('orders')
            ->leftJoin('order_products', 'orders.order_product_id', '=', 'order_products.id')
            ->leftJoin('order_sellers', 'orders.order_seller_id', '=', 'order_sellers.id')
            ->leftJoin('order_buyers', 'orders.order_buyer_id', '=', 'order_buyers.id')
            ->select(
                'orders.id',
                'order_products.product_id',
                'order_sellers.user_id as seller_user_id',
                'order_buyers.user_id as buyer_user_id'
            )
            ->get();

        foreach ($orders as $order) {
            DB::table('orders')->where('id', $order->id)->update([
                'product_id' => $order->product_id,
                'seller_id' => $order->seller_user_id,
                'user_id' => $order->buyer_user_id,
            ]);
        }

        // Drop foreign keys and new columns
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['order_product_id']);
            $table->dropForeign(['order_seller_id']);
            $table->dropForeign(['order_buyer_id']);
            $table->dropColumn(['order_product_id', 'order_seller_id', 'order_buyer_id']);
        });

        // Drop snapshot tables
        Schema::dropIfExists('order_buyers');
        Schema::dropIfExists('order_sellers');
        Schema::dropIfExists('order_products');
    }
};
