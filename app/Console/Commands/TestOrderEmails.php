<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Order;
use App\Models\User;
use App\Models\Product;
use App\Mail\NewOrderNotification;
use App\Mail\OrderAcceptedNotification;
use Illuminate\Support\Facades\Mail;

class TestOrderEmails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:order-emails {--order=} {--type=both}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test order email notifications';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $orderId = $this->option('order');
        $type = $this->option('type');

        if ($orderId) {
            $order = Order::with(['user', 'seller', 'product'])->find($orderId);
            if (!$order) {
                $this->error("Order with ID {$orderId} not found.");
                return;
            }
        } else {
            // Get the latest order for testing
            $order = Order::with(['user', 'seller', 'product'])->latest()->first();
            if (!$order) {
                $this->error("No orders found in the database.");
                return;
            }
        }

        $this->info("Testing email notifications for Order #{$order->id}");
        $this->info("Buyer: {$order->user->name} ({$order->user->email})");
        $this->info("Seller: {$order->seller->name} ({$order->seller->email})");
        $this->info("Product: {$order->product->name}");

        if ($type === 'new' || $type === 'both') {
            $this->info("\n--- Testing New Order Notification (to Seller) ---");
            try {
                Mail::to($order->seller->email)->send(new NewOrderNotification($order));
                $this->info("✅ New order notification sent successfully to seller!");
            } catch (\Exception $e) {
                $this->error("❌ Failed to send new order notification: " . $e->getMessage());
            }
        }

        if ($type === 'accepted' || $type === 'both') {
            $this->info("\n--- Testing Order Accepted Notification (to Buyer) ---");
            try {
                Mail::to($order->user->email)->send(new OrderAcceptedNotification($order));
                $this->info("✅ Order accepted notification sent successfully to buyer!");
            } catch (\Exception $e) {
                $this->error("❌ Failed to send order accepted notification: " . $e->getMessage());
            }
        }

        $this->info("\n🔔 Email testing completed!");
        $this->info("📧 Check your mail logs or configured mail service for the emails.");
    }
}