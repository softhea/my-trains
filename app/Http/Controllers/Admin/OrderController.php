<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::with(['user', 'product'])
            ->latest()
            ->paginate(20);

        return view('admin.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        $order->load(['user', 'product']);
        return view('admin.orders.show', compact('order'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:pending,processing,completed,cancelled',
        ]);

        $oldStatus = $order->status;
        $newStatus = $request->status;

        // If changing from cancelled to any other status, reduce stock again
        if ($oldStatus === 'cancelled' && $newStatus !== 'cancelled') {
            if (!$order->product->hasStock($order->quantity)) {
                return back()->with('error', 'Not enough stock available to update this order.');
            }
            $order->product->reduceStock($order->quantity);
        }

        // If changing to cancelled from any other status, restore stock
        if ($oldStatus !== 'cancelled' && $newStatus === 'cancelled') {
            $order->product->restoreStock($order->quantity);
        }

        $order->update(['status' => $newStatus]);

        return back()->with('success', 'Order status updated successfully.');
    }

    public function destroy(Order $order)
    {
        // Restore stock if the order wasn't cancelled
        if ($order->status !== 'cancelled') {
            $order->product->restoreStock($order->quantity);
        }

        $order->delete();

        return redirect()->route('admin.orders.index')
            ->with('success', 'Order deleted successfully.');
    }
}
