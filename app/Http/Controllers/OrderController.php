<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

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
        
        // Prevent ordering own product
        if ($product->user_id && $product->user_id === auth()->id()) {
            return back()->with('error', __('You cannot order your own product.'));
        }

        // Check if product has enough stock
        if (!$product->hasStock($request->quantity)) {
            return back()->with('error', __('Sorry, we only have :count items in stock.', ['count' => $product->no_of_items]));
        }

        try {
            DB::transaction(function () use ($request, $product) {
                // Create the order
                $totalPrice = $product->price * $request->quantity;
                
                $order = Order::create([
                    'user_id' => auth()->id(),
                    'seller_id' => $product->user_id,
                    'product_id' => $request->product_id,
                    'quantity' => $request->quantity,
                    'total_price' => $totalPrice,
                    'note' => $request->note,
                    'status' => 'pending',
                ]);

                // Reduce stock
                $product->reduceStock($request->quantity);
            });

            return back()->with('success', __('Order placed successfully! We\'ll contact you soon.'));
        } catch (\Exception $e) {
            return back()->with('error', __('There was an error placing your order. Please try again.'));
        }
    }

    public function index()
    {
        $orders = auth()->user()->orders()
            ->with('product')
            ->latest()
            ->paginate(10);

        return view('orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        // Ensure user can only view their own orders
        if ($order->user_id !== auth()->id() && !auth()->user()->isAdmin()) {
            abort(403);
        }

        return view('orders.show', compact('order'));
    }

    public function cancel(Order $order)
    {
        // Ensure user can only cancel their own orders
        if ($order->user_id !== auth()->id() && !auth()->user()->isAdmin()) {
            abort(403);
        }

        if ($order->cancel()) {
            return back()->with('success', 'Order cancelled successfully.');
        }

        return back()->with('error', 'This order cannot be cancelled.');
    }
}
