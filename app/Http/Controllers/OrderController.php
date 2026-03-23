<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Product;
use App\Models\Bundle;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\NewOrderNotification;

class OrderController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'note' => 'nullable|string',
        ]);

        $product = Product::findOrFail($request->product_id);
        
        // Check if product is active
        if (!$product->is_active) {
            return back()->with('error', __('This product is currently unavailable.'));
        }

        // Check if product can be purchased standalone
        if (!$product->canPurchaseStandalone()) {
            return back()->with('error', __('This product is only available as part of a bundle.'));
        }
        
        // Prevent ordering own product
        if ($product->user_id && $product->user_id === auth()->id()) {
            return back()->with('error', __('You cannot order your own product.'));
        }

        // Check if product has enough stock
        if (!$product->hasStock($request->quantity)) {
            return back()->with('error', __('Sorry, we only have :count items in stock.', ['count' => $product->no_of_items]));
        }

        try {
            $order = null;
            DB::transaction(function () use ($request, $product, &$order) {
                // Create the order with snapshots (stock is NOT reduced here)
                $order = Order::createWithSnapshots(
                    $product,
                    auth()->user(),
                    $request->quantity,
                    $request->note
                );

                // Override status to pending (createWithSnapshots reduces stock, but we don't want that yet)
                // Restore stock since we only reduce on acceptance
                $product->restoreStock($request->quantity);

                // Load relationships for email
                $order->load(['orderProduct', 'orderSeller', 'orderBuyer']);
            });

            // Send email notification to seller
            if ($order && $order->orderSeller && $order->orderSeller->email) {
                Mail::to($order->orderSeller->email)->send(new NewOrderNotification($order));
            }

            return back()->with('success', __('Order placed successfully! We\'ll contact you soon.'));
        } catch (\Exception $e) {
            return back()->with('error', __('There was an error placing your order. Please try again.'));
        }
    }

    public function index()
    {
        $orders = auth()->user()->orders()
            ->with(['orderProduct', 'orderSeller', 'orderBuyer'])
            ->latest()
            ->paginate(10);

        // Calculate total amount of all orders
        $totalAmount = auth()->user()->orders()->sum('total_price');

        return view('orders.index', compact('orders', 'totalAmount'));
    }

    public function show(Order $order)
    {
        // Ensure user can only view their own orders
        $buyerUserId = $order->orderBuyer?->user_id;
        if ($buyerUserId !== auth()->id() && !auth()->user()->isAdmin()) {
            abort(403);
        }

        $order->load(['orderProduct', 'orderSeller', 'orderBuyer']);

        return view('orders.show', compact('order'));
    }

    public function cancel(Order $order)
    {
        // Ensure user can only cancel their own orders
        $buyerUserId = $order->orderBuyer?->user_id;
        if ($buyerUserId !== auth()->id() && !auth()->user()->isAdmin()) {
            abort(403);
        }

        if ($order->cancel()) {
            return back()->with('success', 'Order cancelled successfully.');
        }

        return back()->with('error', 'This order cannot be cancelled.');
    }

    public function storeBundle(Request $request)
    {
        $request->validate([
            'bundle_id' => 'required|exists:bundles,id',
            'note' => 'nullable|string',
        ]);

        $bundle = Bundle::with('products')->findOrFail($request->bundle_id);
        
        // Check if bundle is active
        if (!$bundle->is_active) {
            return back()->with('error', __('This bundle is currently unavailable.'));
        }

        // Prevent ordering own bundle
        if ($bundle->user_id === auth()->id()) {
            return back()->with('error', __('You cannot order your own bundle.'));
        }

        // Check if all products have enough stock
        if (!$bundle->hasStock()) {
            return back()->with('error', __('One or more products in this bundle are out of stock.'));
        }

        try {
            $orders = [];
            DB::transaction(function () use ($request, $bundle, &$orders) {
                // Create an order for each product in the bundle
                foreach ($bundle->products as $product) {
                    $quantity = $product->pivot->quantity;
                    
                    // Calculate proportional price for this product in the bundle
                    $productValue = (float) $product->price * $quantity;
                    $totalBundleValue = $bundle->total_products_value;
                    $proportion = $totalBundleValue > 0 ? $productValue / $totalBundleValue : 0;
                    $discountedPrice = (float) $bundle->price * $proportion;
                    
                    $order = Order::createWithSnapshots(
                        $product,
                        auth()->user(),
                        $quantity,
                        __('Bundle: :name', ['name' => $bundle->name]) . ($request->note ? "\n" . $request->note : ''),
                        $discountedPrice
                    );

                    // Restore stock since we only reduce on acceptance
                    $product->restoreStock($quantity);

                    $order->load(['orderProduct', 'orderSeller', 'orderBuyer']);
                    $orders[] = $order;
                }
            });

            // Send email notifications to sellers
            foreach ($orders as $order) {
                if ($order->orderSeller && $order->orderSeller->email) {
                    Mail::to($order->orderSeller->email)->send(new NewOrderNotification($order));
                }
            }

            return redirect()->route('orders.index')
                ->with('success', __('Bundle ordered successfully! :count orders have been placed.', ['count' => count($orders)]));
        } catch (\Exception $e) {
            return back()->with('error', __('There was an error placing your order. Please try again.'));
        }
    }
}
