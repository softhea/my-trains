<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Image;
use App\Models\Video;
use App\Models\Category;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with('category', 'images', 'videos')->latest()->get();
        return view('admin.products.index', compact('products'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('admin.products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'no_of_items' => 'required|integer|min:0',
            'category_id' => 'required|exists:categories,id',
            'images.*' => 'nullable|image|max:8192',
            'videos' => 'nullable|string',
        ]);

        $product = Product::create($request->only(
            'name', 'description', 'price', 'no_of_items', 'category_id')
        );

        // Handle image uploads
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $img) {
                $path = $img->store('products', 'public');
                $product->images()->create(['url' => "/storage/$path"]);
            }
        }

        // Handle YouTube URLs
        if ($request->videos) {
            $urls = array_filter(array_map('trim', explode("\n", $request->videos)));
            foreach ($urls as $url) {
                // Basic validation - check if URL contains YouTube domain
                if (strpos($url, 'youtube.com') !== false || strpos($url, 'youtu.be') !== false) {
                    $product->videos()->create(['url' => $url]);
                }
            }
        }

        return redirect()->route('admin.products.index')->with('success', 'Product created successfully!');
    }

    public function edit(Product $product)
    {
        $categories = Category::all();
        $product->load('images', 'videos');
        return view('admin.products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'no_of_items' => 'required|integer|min:0',
            'category_id' => 'required|exists:categories,id',
            'images.*' => 'nullable|image|max:8192',
            'videos' => 'nullable|string',
        ]);

        $product->update($request->only('name', 'description', 'price', 'no_of_items', 'category_id'));

        // Handle image uploads
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $img) {
                $path = $img->store('products', 'public');
                $product->images()->create(['url' => "/storage/$path"]);
            }
        }

        // Handle YouTube URLs - first delete existing videos, then add new ones
        if ($request->filled('videos')) {
            // Delete existing videos
            $product->videos()->delete();
            
            // Add new videos
            $urls = array_filter(array_map('trim', explode("\n", $request->videos)));
            foreach ($urls as $url) {
                // Basic validation - check if URL contains YouTube domain
                if (strpos($url, 'youtube.com') !== false || strpos($url, 'youtu.be') !== false) {
                    $product->videos()->create(['url' => $url]);
                }
            }
        } elseif ($request->has('videos') && empty($request->videos)) {
            // If videos field is empty, delete all existing videos
            $product->videos()->delete();
        }

        return redirect()->route('admin.products.index')->with('success', 'Product updated successfully!');
    }

    public function destroy(Product $product)
    {
        // Delete product images from storage
        foreach ($product->images as $image) {
            $path = str_replace('/storage/', '', $image->url);
            Storage::disk('public')->delete($path);
        }

        // Delete the product (cascade will handle images and videos)
        $product->delete();

        return redirect()->route('admin.products.index')->with('success', 'Product deleted successfully!');
    }

    public function deleteImage(Image $image)
    {
        // Verify the image belongs to a product
        if ($image->imageable_type !== Product::class) {
            return response()->json(['error' => 'Image not found'], 404);
        }

        // Delete from storage
        $path = str_replace('/storage/', '', $image->url);
        Storage::disk('public')->delete($path);

        // Delete from database
        $image->delete();

        return response()->json(['success' => 'Image deleted successfully']);
    }
}
