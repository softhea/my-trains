<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Image;
use Illuminate\Support\Facades\Storage;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::with('children.images', 'products', 'images')
            ->whereNull('parent_id')
            ->get();

        return view('admin.categories.index', compact('categories'));
    }

    public function create()
    {
        $parentCategories = Category::whereNull('parent_id')->get();
        return view('admin.categories.create', compact('parentCategories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
            'parent_id' => 'nullable|exists:categories,id',
            'images.*' => 'nullable|image|max:8192',
        ]);

        $category = Category::create($request->only('name', 'parent_id'));

        // Handle image uploads
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $img) {
                $path = $img->store('categories', 'public');
                $category->images()->create(['url' => "/storage/$path"]);
            }
        }

        return redirect()->route('admin.categories.index')
            ->with('success', 'Category created successfully!');
    }

    public function edit(Category $category)
    {
        $parentCategories = Category::whereNull('parent_id')
            ->where('id', '!=', $category->id)
            ->get();
        
        return view('admin.categories.edit', compact('category', 'parentCategories'));
    }

    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
            'parent_id' => 'nullable|exists:categories,id',
            'images.*' => 'nullable|image|max:8192',
        ]);

        // Prevent setting parent to itself or its children
        if ($request->parent_id == $category->id) {
            return back()->withErrors(['parent_id' => 'Category cannot be its own parent.']);
        }

        $category->update($request->only('name', 'parent_id'));

        // Handle image uploads
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $img) {
                $path = $img->store('categories', 'public');
                $category->images()->create(['url' => "/storage/$path"]);
            }
        }

        return redirect()->route('admin.categories.index')
            ->with('success', 'Category updated successfully!');
    }

    public function destroy(Category $category)
    {
        // Check if category has products
        if ($category->products()->count() > 0) {
            return back()->withErrors(['error' => 'Cannot delete category that has products.']);
        }

        // Check if category has children
        if ($category->children()->count() > 0) {
            return back()->withErrors(['error' => 'Cannot delete category that has subcategories.']);
        }

        // Delete category images from storage
        foreach ($category->images as $image) {
            $path = str_replace('/storage/', '', $image->url);
            Storage::disk('public')->delete($path);
        }

        $category->delete();

        return redirect()->route('admin.categories.index')
            ->with('success', 'Category deleted successfully!');
    }

    public function deleteImage(Image $image)
    {
        // Verify the image belongs to a category
        if ($image->imageable_type !== Category::class) {
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
