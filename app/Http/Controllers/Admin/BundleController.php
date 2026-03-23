<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\ProcessImage;
use App\Models\Bundle;
use App\Models\Image;
use App\Models\Product;
use App\Services\ImageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class BundleController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();
        
        if ($user->isAdmin()) {
            $bundles = Bundle::with(['user', 'products', 'images'])->latest()->get();
        } else {
            $bundles = Bundle::with(['user', 'products', 'images'])
                ->where('user_id', $user->id)
                ->latest()
                ->get();
        }

        return view('admin.bundles.index', compact('bundles'));
    }

    public function create(): View
    {
        $user = auth()->user();
        
        if ($user->isAdmin()) {
            $products = Product::with('images')
                ->availableInBundle()
                ->active()
                ->orderBy('name')
                ->get();
        } else {
            $products = Product::with('images')
                ->where('user_id', $user->id)
                ->availableInBundle()
                ->active()
                ->orderBy('name')
                ->get();
        }

        return view('admin.bundles.create', compact('products'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'currency' => 'required|in:RON,EUR',
            'products' => 'required|array|min:2',
            'products.*' => 'exists:products,id',
            'quantities' => 'required|array',
            'quantities.*' => 'integer|min:1',
            'images.*' => 'nullable|image|max:8192',
        ]);

        $user = auth()->user();
        
        if (!$user->isAdmin()) {
            $userProductIds = Product::where('user_id', $user->id)->pluck('id')->toArray();
            foreach ($request->products as $productId) {
                if (!in_array($productId, $userProductIds)) {
                    return back()->withErrors(['products' => __('You can only add your own products to bundles.')])->withInput();
                }
            }
        }

        $bundle = Bundle::create([
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'currency' => $request->currency,
            'user_id' => $user->id,
            'is_active' => $request->has('is_active'),
        ]);

        foreach ($request->products as $productId) {
            $quantity = $request->quantities[$productId] ?? 1;
            $bundle->products()->attach($productId, ['quantity' => $quantity]);
        }

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $img) {
                try {
                    if (!$img->isValid()) {
                        continue;
                    }

                    $path = $img->store('bundles', 'public');
                    
                    if ($path) {
                        $imageRecord = $bundle->images()->create(['url' => "/storage/$path"]);
                        ProcessImage::dispatch($imageRecord, 'product');
                    }
                } catch (\Exception $e) {
                    Log::error("Bundle image upload error: " . $e->getMessage());
                }
            }
        }

        $redirectRoute = $user->isAdmin() ? 'admin.bundles.index' : 'seller.bundles.index';

        return redirect()
            ->route($redirectRoute)
            ->with('success', __('Bundle created successfully!'));
    }

    public function edit(Bundle $bundle): View
    {
        $user = auth()->user();
        
        if (!$user->isAdmin() && $bundle->user_id !== $user->id) {
            abort(403);
        }

        $bundle->load(['products', 'images']);

        if ($user->isAdmin()) {
            $products = Product::with('images')
                ->availableInBundle()
                ->active()
                ->orderBy('name')
                ->get();
        } else {
            $products = Product::with('images')
                ->where('user_id', $user->id)
                ->availableInBundle()
                ->active()
                ->orderBy('name')
                ->get();
        }

        $selectedProducts = $bundle->products->pluck('pivot.quantity', 'id')->toArray();

        return view('admin.bundles.edit', compact('bundle', 'products', 'selectedProducts'));
    }

    public function update(Request $request, Bundle $bundle): RedirectResponse
    {
        $user = auth()->user();
        
        if (!$user->isAdmin() && $bundle->user_id !== $user->id) {
            abort(403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'currency' => 'required|in:RON,EUR',
            'products' => 'required|array|min:2',
            'products.*' => 'exists:products,id',
            'quantities' => 'required|array',
            'quantities.*' => 'integer|min:1',
            'images.*' => 'nullable|image|max:8192',
        ]);

        if (!$user->isAdmin()) {
            $userProductIds = Product::where('user_id', $user->id)->pluck('id')->toArray();
            foreach ($request->products as $productId) {
                if (!in_array($productId, $userProductIds)) {
                    return back()->withErrors(['products' => __('You can only add your own products to bundles.')])->withInput();
                }
            }
        }

        $bundle->update([
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'currency' => $request->currency,
            'is_active' => $request->has('is_active'),
        ]);

        $bundle->products()->detach();
        foreach ($request->products as $productId) {
            $quantity = $request->quantities[$productId] ?? 1;
            $bundle->products()->attach($productId, ['quantity' => $quantity]);
        }

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $img) {
                try {
                    if (!$img->isValid()) {
                        continue;
                    }

                    $path = $img->store('bundles', 'public');
                    
                    if ($path) {
                        $imageRecord = $bundle->images()->create(['url' => "/storage/$path"]);
                        ProcessImage::dispatch($imageRecord, 'product');
                    }
                } catch (\Exception $e) {
                    Log::error("Bundle image upload error: " . $e->getMessage());
                }
            }
        }

        $redirectRoute = $user->isAdmin() ? 'admin.bundles.index' : 'seller.bundles.index';

        return redirect()
            ->route($redirectRoute)
            ->with('success', __('Bundle updated successfully!'));
    }

    public function destroy(Bundle $bundle, ImageService $imageService): RedirectResponse
    {
        $user = auth()->user();
        
        if (!$user->isAdmin() && $bundle->user_id !== $user->id) {
            abort(403);
        }

        foreach ($bundle->images as $image) {
            $imageService->deleteImage($image);
        }

        $bundle->delete();

        $redirectRoute = $user->isAdmin() ? 'admin.bundles.index' : 'seller.bundles.index';

        return redirect()
            ->route($redirectRoute)
            ->with('success', __('Bundle deleted successfully!'));
    }

    public function deleteImage(Image $image, ImageService $imageService): JsonResponse
    {
        if ($image->imageable_type !== Bundle::class) {
            return response()->json(['error' => 'Image not found'], 404);
        }

        $imageService->deleteImage($image);

        return response()->json(['success' => 'Image deleted successfully']);
    }

    public function toggleStatus(Bundle $bundle): RedirectResponse
    {
        $user = auth()->user();
        
        if (!$user->isAdmin() && $bundle->user_id !== $user->id) {
            abort(403);
        }

        $bundle->update(['is_active' => !$bundle->is_active]);

        return back()->with('success', __('Bundle status updated successfully!'));
    }
}
