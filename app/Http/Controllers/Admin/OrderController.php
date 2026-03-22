<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\OrderAcceptedNotification;

class OrderController extends Controller
{
    public function index()
    {
        $query = Order::with(['orderProduct', 'orderSeller', 'orderBuyer']);

        // Determine if this is a seller route or admin route
        $isSellerRoute = request()->routeIs('seller.*');

        if ($isSellerRoute) {
            // Seller routes: show only orders for current user's products
            $query->whereHas('orderSeller', function ($q) {
                $q->where('user_id', auth()->id());
            });
            $view = 'seller.orders.index';
            
            // Calculate total for seller's product orders
            $totalAmount = Order::whereHas('orderSeller', function ($q) {
                $q->where('user_id', auth()->id());
            })->sum('total_price');
        } else {
            // Admin routes: admins see all orders, non-admins see only their product orders
            if (!auth()->user()->isAdmin()) {
                $query->whereHas('orderSeller', function ($q) {
                    $q->where('user_id', auth()->id());
                });
                
                // Calculate total for non-admin user's product orders
                $totalAmount = Order::whereHas('orderSeller', function ($q) {
                    $q->where('user_id', auth()->id());
                })->sum('total_price');
            } else {
                // Calculate total for all orders (admin view)
                $totalAmount = Order::sum('total_price');
            }
            $view = 'admin.orders.index';
        }

        $orders = $query->latest()->paginate(20);

        return view($view, compact('orders', 'totalAmount'));
    }

    public function show(Order $order)
    {
        $order->load(['orderProduct', 'orderSeller', 'orderBuyer']);

        // Check if user can view this order
        $sellerUserId = $order->orderSeller?->user_id;
        if (!auth()->user()->isAdmin() && $sellerUserId !== auth()->id()) {
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
        $sellerUserId = $order->orderSeller?->user_id;
        if (!auth()->user()->isAdmin() && $sellerUserId !== auth()->id()) {
            abort(403, 'You can only manage orders for your own products.');
        }

        // Get the original product for stock management
        $originalProduct = $order->orderProduct?->product;

        // Stock management logic:
        // - Stock is reduced when moving from 'pending' to 'processing' (seller accepts)
        // - Stock is restored when moving from 'processing' or 'completed' to 'cancelled'
        // - Stock is restored when moving from 'processing' back to 'pending' (seller rejects)

        // If changing from pending to processing (seller accepts order)
        if ($oldStatus === 'pending' && $newStatus === 'processing') {
            if ($originalProduct && !$originalProduct->hasStock($order->quantity)) {
                return back()->with('error', __('Not enough stock available to accept this order.'));
            }
            if ($originalProduct) {
                $originalProduct->reduceStock($order->quantity);
            }
        }

        // If changing from processing back to pending (seller rejects after accepting)
        if ($oldStatus === 'processing' && $newStatus === 'pending') {
            if ($originalProduct) {
                $originalProduct->restoreStock($order->quantity);
            }
        }

        // If changing to cancelled from processing or completed, restore stock
        if (in_array($oldStatus, ['processing', 'completed']) && $newStatus === 'cancelled') {
            if ($originalProduct) {
                $originalProduct->restoreStock($order->quantity);
            }
        }

        // If changing from cancelled to processing, reduce stock again
        if ($oldStatus === 'cancelled' && $newStatus === 'processing') {
            if ($originalProduct && !$originalProduct->hasStock($order->quantity)) {
                return back()->with('error', __('Not enough stock available to update this order.'));
            }
            if ($originalProduct) {
                $originalProduct->reduceStock($order->quantity);
            }
        }

        $order->update(['status' => $newStatus]);

        // Send email notification to buyer when order is accepted (status changes to processing)
        if ($oldStatus === 'pending' && $newStatus === 'processing') {
            $order->load(['orderProduct', 'orderSeller', 'orderBuyer']);
            $buyerEmail = $order->orderBuyer?->email;
            if ($buyerEmail) {
                Mail::to($buyerEmail)->send(new OrderAcceptedNotification($order));
            }
        }

        return back()->with('success', __('Order status updated successfully.'));
    }

    public function destroy(Order $order)
    {
        // Restore stock only if the order was in processing or completed status
        // (since pending orders never reduced stock)
        if (in_array($order->status, ['processing', 'completed'])) {
            $originalProduct = $order->orderProduct?->product;
            if ($originalProduct) {
                $originalProduct->restoreStock($order->quantity);
            }
        }

        $order->delete();

        return redirect()->route('admin.orders.index')
            ->with('success', __('Order deleted successfully.'));
    }
}
