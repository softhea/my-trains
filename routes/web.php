<?php

declare(strict_types=1);

use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\MessageController as AdminMessageController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\App;

Route::middleware('setlocale')->group(function () {
    Route::get('/', function () {
        return view('welcome');
    });

// Removed unused dashboard route

    Route::get('/', [ProductController::class, 'index'])
        ->name('home');
    Route::get('/products', [ProductController::class, 'products'])
        ->name('products.index');
    Route::get('/products/{product}', [ProductController::class, 'show'])
        ->name('products.show');
    Route::post('/order', [OrderController::class, 'store'])
        ->name('order.store')
        ->middleware(['auth', 'verified']);

    // Contact routes
    Route::get('/contact', [ContactController::class, 'create'])->name('contact.create');
    Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');

    // Legal pages
    Route::get('/privacy-policy', function () {
        return view('legal.privacy-policy');
    })->name('privacy-policy');
    
    Route::get('/cookie-policy', function () {
        return view('legal.cookie-policy');
    })->name('cookie-policy');
    
    Route::get('/terms-and-conditions', function () {
        return view('legal.terms-and-conditions');
    })->name('terms-and-conditions');
    
    Route::get('/legal-notice', function () {
        return view('legal.legal-notice');
    })->name('legal-notice');

    Route::middleware(['auth'])->group(function () {
        Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
        Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
        Route::post('/orders/{order}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');
        Route::get('/my-products', function () {
            $products = \App\Models\Product::with(['images', 'category'])
                ->where('user_id', auth()->id())
                ->latest()
                ->paginate(12);
            return view('products.mine', compact('products'));
        })->name('my.products');
        
        // Messages
        Route::get('/messages', [MessageController::class, 'index'])->name('messages.index');
        Route::get('/messages/create', [MessageController::class, 'create'])->name('messages.create');
        Route::post('/messages', [MessageController::class, 'store'])->name('messages.store');
        Route::get('/messages/conversation/{user}', [MessageController::class, 'conversation'])->name('messages.conversation');
        Route::post('/messages/reply/{user}', [MessageController::class, 'reply'])->name('messages.reply');
        Route::get('/api/messages/unread-count', [MessageController::class, 'unreadCount'])->name('messages.unread-count');
        Route::get('/api/messages/latest-unread', [MessageController::class, 'latestUnread'])->name('messages.latest-unread');
    });

    Route::middleware(['auth'])
        ->prefix('admin')
        ->group(function () {
        // User Management (Superadmin only)
        Route::get('/users', [AdminUserController::class, 'index'])
            ->name('admin.users.index')
            ->middleware('permission:users.view');
        Route::get('/users/create', [AdminUserController::class, 'create'])
            ->name('admin.users.create')
            ->middleware('permission:users.create');
        Route::post('/users', [AdminUserController::class, 'store'])
            ->name('admin.users.store')
            ->middleware('permission:users.create');
        Route::get('/users/{user}', [AdminUserController::class, 'show'])
            ->name('admin.users.show')
            ->middleware('permission:users.view');
        Route::get('/users/{user}/edit', [AdminUserController::class, 'edit'])
            ->name('admin.users.edit')
            ->middleware('permission:users.edit');
        Route::put('/users/{user}', [AdminUserController::class, 'update'])
            ->name('admin.users.update')
            ->middleware('permission:users.edit');
        Route::delete('/users/{user}', [AdminUserController::class, 'destroy'])
            ->name('admin.users.destroy')
            ->middleware('permission:users.delete');

        Route::patch('/users/{user}/toggle-verification', [AdminUserController::class, 'toggleEmailVerification'])
            ->name('admin.users.toggle-verification')
            ->middleware('permission:users.edit');

        // Product Management
        Route::get('/products', [AdminProductController::class, 'index'])
            ->name('admin.products.index')
            ->middleware('permission:products.edit');
        Route::get('/products/create', [AdminProductController::class, 'create'])
            ->name('admin.products.create')
            ->middleware('permission:products.create');
        Route::post('/products', [AdminProductController::class, 'store'])
            ->name('admin.products.store')
            ->middleware(['permission:products.create', 'verified']);
        
        Route::get('/products/{product}/edit', [AdminProductController::class, 'edit'])
            ->name('admin.products.edit');
            /**
             * todo
             */
            //->middleware('permission:products.view');
        Route::put('/products/{product}', [AdminProductController::class, 'update'])
            ->name('admin.products.update')
            ->middleware('verified');
            /**
             * todo use my-products/{product} for own products
             */
            //->middleware('permission:products.edit');

        Route::delete('/products/{product}', [AdminProductController::class, 'destroy'])
            ->name('admin.products.destroy');
            //->middleware('permission:products.delete');

        Route::delete('products/images/{image}', [AdminProductController::class, 'deleteImage'])
            ->name('admin.products.images.destroy');
            //->middleware('permission:products.delete');

        // Category Management
        Route::get('/categories', [AdminCategoryController::class, 'index'])
            ->name('admin.categories.index')
            ->middleware('permission:categories.view');
        Route::get('/categories/create', [AdminCategoryController::class, 'create'])
            ->name('admin.categories.create')
            ->middleware('permission:categories.create');
        Route::post('/categories', [AdminCategoryController::class, 'store'])
            ->name('admin.categories.store')
            ->middleware('permission:categories.create');
        Route::get('/categories/{category}/edit', [AdminCategoryController::class, 'edit'])
            ->name('admin.categories.edit')
            ->middleware('permission:categories.view');
        Route::put('/categories/{category}', [AdminCategoryController::class, 'update'])
            ->name('admin.categories.update')
            ->middleware('permission:categories.edit');
        Route::delete('/categories/{category}', [AdminCategoryController::class, 'destroy'])
            ->name('admin.categories.destroy')
            ->middleware('permission:categories.delete');
        
        Route::delete('categories/images/{image}', [AdminCategoryController::class, 'deleteImage'])
            ->name('admin.categories.images.destroy')
            ->middleware('permission:categories.delete');

        // Seller Order Management (for users to manage orders of their products)
        Route::get('/my-orders', [AdminOrderController::class, 'index'])
            ->name('seller.orders.index')
            ->middleware('auth');
        Route::get('/my-orders/{order}', [AdminOrderController::class, 'show'])
            ->name('seller.orders.show')
            ->middleware('auth');
        Route::put('/my-orders/{order}/status', [AdminOrderController::class, 'updateStatus'])
            ->name('seller.orders.update-status')
            ->middleware('auth');

        // Order Management
        Route::get('/orders', [AdminOrderController::class, 'index'])
            ->name('admin.orders.index')
            ->middleware('permission:orders.view');
        Route::get('/orders/{order}', [AdminOrderController::class, 'show'])
            ->name('admin.orders.show')
            ->middleware('permission:orders.view');
        Route::put('/orders/{order}/status', [AdminOrderController::class, 'updateStatus'])
            ->name('admin.orders.update-status')
            ->middleware('permission:orders.edit');
        Route::delete('/orders/{order}', [AdminOrderController::class, 'destroy'])
            ->name('admin.orders.destroy')
            ->middleware('permission:orders.delete');

        // Message Management  
        Route::get('/messages', [AdminMessageController::class, 'index'])
            ->name('admin.messages.index');
        Route::get('/messages/{message}', [AdminMessageController::class, 'show'])
            ->name('admin.messages.show');
        Route::delete('/messages/{message}', [AdminMessageController::class, 'destroy'])
            ->name('admin.messages.destroy');
        Route::patch('/messages/{message}/toggle-read', [AdminMessageController::class, 'toggleRead'])
            ->name('admin.messages.toggle-read');
        Route::post('/messages/bulk-action', [AdminMessageController::class, 'bulkAction'])
            ->name('admin.messages.bulk-action');
        });

    Route::middleware('auth')->group(function () {
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    });

});

require __DIR__.'/auth.php';

// Locale switcher
Route::get('/locale/{locale}', function (string $locale) {
    abort_unless(in_array($locale, ['en', 'ro'], true), 404);
    // Remember for a long time (~1 year)
    Cookie::queue(Cookie::make('locale', $locale, 60 * 24 * 365));
    // Also apply immediately for the current request
    App::setLocale($locale);
    return back();
})->name('locale.switch');
