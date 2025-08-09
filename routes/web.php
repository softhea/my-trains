<?php

declare(strict_types=1);

use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/', [ProductController::class, 'index'])
    ->name('home');
Route::get('/products/{product}', [ProductController::class, 'show'])
    ->name('products.show');
Route::post('/order', [OrderController::class, 'store'])
    ->name('order.store')
    ->middleware('auth');

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

        // Product Management
        Route::get('/products', [AdminProductController::class, 'index'])
            ->name('admin.products.index')
            ->middleware('permission:products.view');
        Route::get('/products/create', [AdminProductController::class, 'create'])
            ->name('admin.products.create')
            ->middleware('permission:products.create');
        Route::post('/products', [AdminProductController::class, 'store'])
            ->name('admin.products.store')
            ->middleware('permission:products.create');
        Route::get('/products/{product}/edit', [AdminProductController::class, 'edit'])
            ->name('admin.products.edit')
            ->middleware('permission:products.view');
        Route::put('/products/{product}', [AdminProductController::class, 'update'])
            ->name('admin.products.update')
            ->middleware('permission:products.edit');
        Route::delete('/products/{product}', [AdminProductController::class, 'destroy'])
            ->name('admin.products.destroy')
            ->middleware('permission:products.delete');

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
        Route::delete('/images/{image}', [AdminCategoryController::class, 'deleteImage'])
            ->name('admin.images.destroy')
            ->middleware('permission:categories.delete');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
