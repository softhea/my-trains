<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index()
    {
        $query = Order::with(['user', 'product']);

        // Determine if this is a seller route or admin route
        $isSellerRoute = request()->routeIs('seller.*');

        if ($isSellerRoute) {
            // Seller routes: show only orders for current user's products
            $query->whereHas('product', function ($q) {
                $q->where('user_id', auth()->id());
            });
            $view = 'seller.orders.index';
        } else {
            // Admin routes: admins see all orders, non-admins see only their product orders
            if (!auth()->user()->isAdmin()) {
                $query->whereHas('product', function ($q) {
                    $q->where('user_id', auth()->id());
                });
            }
            $view = 'admin.orders.index';
        }

        $orders = $query->latest()->paginate(20);

        return view($view, compact('orders'));
    }

    public function show(Order $order)
    {
        $order->load(['user', 'product']);

        // Check if user can view this order
        if (!auth()->user()->isAdmin() && $order->product->user_id !== auth()->id()) {
            abort(403, 'You can only view orders for your own products.');
        }

        // Determine view based on route
        $isSellerRoute = request()->routeIs('seller.*');
        $view = $isSellerRoute ? 'seller.orders.show' : 'admin.orders.show';

        return view($view, compact('order'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:pending,processing,completed,cancelled',
        ]);

        $oldStatus = $order->status;
        $newStatus = $request->status;

        // Check if user can manage this order (admin or product owner)
        if (!auth()->user()->isAdmin() && $order->product->user_id !== auth()->id()) {
            abort(403, 'You can only manage orders for your own products.');
        }

        // Stock management logic:
        // - Stock is reduced when moving from 'pending' to 'processing' (seller accepts)
        // - Stock is restored when moving from 'processing' or 'completed' to 'cancelled'
        // - Stock is restored when moving from 'processing' back to 'pending' (seller rejects)

        // If changing from pending to processing (seller accepts order)
        if ($oldStatus === 'pending' && $newStatus === 'processing') {
            if (!$order->product->hasStock($order->quantity)) {
                return back()->with('error', __('Not enough stock available to accept this order.'));
            }
            $order->product->reduceStock($order->quantity);
        }

        // If changing from processing back to pending (seller rejects after accepting)
        if ($oldStatus === 'processing' && $newStatus === 'pending') {
            $order->product->restoreStock($order->quantity);
        }

        // If changing to cancelled from processing or completed, restore stock
        if (in_array($oldStatus, ['processing', 'completed']) && $newStatus === 'cancelled') {
            $order->product->restoreStock($order->quantity);
        }

        // If changing from cancelled to processing, reduce stock again
        if ($oldStatus === 'cancelled' && $newStatus === 'processing') {
            if (!$order->product->hasStock($order->quantity)) {
                return back()->with('error', __('Not enough stock available to update this order.'));
            }
            $order->product->reduceStock($order->quantity);
        }

        $order->update(['status' => $newStatus]);

        return back()->with('success', __('Order status updated successfully.'));
    }

    public function destroy(Order $order)
    {
        // Restore stock only if the order was in processing or completed status
        // (since pending orders never reduced stock)
        if (in_array($order->status, ['processing', 'completed'])) {
            $order->product->restoreStock($order->quantity);
        }

        $order->delete();

        return redirect()->route('admin.orders.index')
            ->with('success', __('Order deleted successfully.'));
    }
}
