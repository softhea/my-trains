<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Image;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(): View
    {
        $categories = Category::with('children.images', 'products', 'images')
            ->whereNull('parent_id')
            ->get();

        return view('admin.categories.index', compact('categories'));
    }

    public function create(): View
    {
        $parentCategories = Category::whereNull('parent_id')->get();

        return view(
            'admin.categories.create', 
            compact('parentCategories')
        );
    }

    public function store(Request $request): RedirectResponse
    {
        // Debug file upload issues before validation
        if ($request->has('images')) {
            foreach ($request->file('images') as $index => $file) {
                if ($file && !$file->isValid()) {
                    $error = $file->getErrorMessage();
                    Log::error("File upload validation error before Laravel validation", [
                        'file_index' => $index,
                        'error_code' => $file->getError(),
                        'error_message' => $error,
                        'file_name' => $file->getClientOriginalName(),
                        'file_size' => $file->getSize(),
                        'upload_max_filesize' => ini_get('upload_max_filesize'),
                        'post_max_size' => ini_get('post_max_size'),
                        'max_file_uploads' => ini_get('max_file_uploads'),
                        'temp_dir' => sys_get_temp_dir(),
                        'temp_dir_writable' => is_writable(sys_get_temp_dir()),
                    ]);
                    
                    return back()->withErrors([
                        'images' => "File upload failed for '{$file->getClientOriginalName()}': {$error}. Check server upload settings."
                    ])->withInput();
                }
            }
        }

        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
            'parent_id' => 'nullable|exists:categories,id',
            'images.*' => 'nullable|image|max:8192',
        ]);

        $category = Category::create($request->only('name', 'parent_id'));

        // Handle image uploads
        if ($request->has('images')) {
            foreach ($request->file('images') as $index => $img) {
                try {
                    // Check if file is valid
                    if (!$img->isValid()) {
                        throw new \Exception("Invalid image file at index $index");
                    }

                    // Check file size (additional check beyond validation)
                    if ($img->getSize() > 8388608) { // 8MB in bytes
                        throw new \Exception("Image file at index $index is too large (max 8MB)");
                    }

                    // Check if storage directory exists and is writable
                    $storagePath = storage_path('app/public/categories');
                    if (!is_dir($storagePath)) {
                        if (!mkdir($storagePath, 0755, true)) {
                            throw new \Exception("Failed to create categories storage directory");
                        }
                    }

                    if (!is_writable($storagePath)) {
                        throw new \Exception("Categories storage directory is not writable. Check permissions.");
                    }

                    // Store the image
                    $path = $img->store('categories', 'public');
                    
                    if (!$path) {
                        throw new \Exception("Failed to store image file at index $index");
                    }

                    // Verify the file was actually stored
                    if (!Storage::disk('public')->exists($path)) {
                        throw new \Exception("Image file was not properly saved at index $index");
                    }

                    // Create database record
                    $imageRecord = $category->images()->create(['url' => "/storage/$path"]);
                    
                    if (!$imageRecord) {
                        // Clean up the stored file if database insertion fails
                        Storage::disk('public')->delete($path);
                        throw new \Exception("Failed to save image record to database at index $index");
                    }

                } catch (\Exception $e) {
                    Log::error("Category image upload error: " . $e->getMessage(), [
                        'category_id' => $category->id,
                        'file_index' => $index,
                        'file_name' => $img->getClientOriginalName(),
                        'file_size' => $img->getSize(),
                        'storage_path' => storage_path('app/public/categories'),
                        'permissions' => is_writable(storage_path('app/public/categories')) ? 'writable' : 'not writable'
                    ]);
                    
                    return back()->withErrors([
                        'images' => "Failed to upload image '" . $img->getClientOriginalName() . "': " . $e->getMessage()
                    ])->withInput();
                }
            }
        }

        return redirect()
            ->route('admin.categories.index')
            ->with('success', __('Category created successfully!'));
    }

    public function edit(Category $category): View
    {
        $parentCategories = Category::whereNull('parent_id')
            ->where('id', '!=', $category->id)
            ->get();
        
        return view(
            'admin.categories.edit', 
            compact('category', 'parentCategories')
        );
    }

    public function update(Request $request, Category $category): RedirectResponse
    {
        // Debug file upload issues before validation
        if ($request->has('images')) {
            foreach ($request->file('images') as $index => $file) {
                if ($file && !$file->isValid()) {
                    $error = $file->getErrorMessage();
                    Log::error("File upload validation error before Laravel validation", [
                        'file_index' => $index,
                        'error_code' => $file->getError(),
                        'error_message' => $error,
                        'file_name' => $file->getClientOriginalName(),
                        'file_size' => $file->getSize(),
                        'upload_max_filesize' => ini_get('upload_max_filesize'),
                        'post_max_size' => ini_get('post_max_size'),
                        'max_file_uploads' => ini_get('max_file_uploads'),
                        'temp_dir' => sys_get_temp_dir(),
                        'temp_dir_writable' => is_writable(sys_get_temp_dir()),
                    ]);
                    
                    return back()->withErrors([
                        'images' => "File upload failed for '{$file->getClientOriginalName()}': {$error}. Check server upload settings."
                    ])->withInput();
                }
            }
        }

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
        if ($request->has('images')) {
            foreach ($request->file('images') as $index => $img) {
                try {
                    // Check if file is valid
                    if (!$img->isValid()) {
                        throw new \Exception("Invalid image file at index $index");
                    }

                    // Check file size (additional check beyond validation)
                    if ($img->getSize() > 8388608) { // 8MB in bytes
                        throw new \Exception("Image file at index $index is too large (max 8MB)");
                    }

                    // Check if storage directory exists and is writable
                    $storagePath = storage_path('app/public/categories');
                    if (!is_dir($storagePath)) {
                        if (!mkdir($storagePath, 0755, true)) {
                            throw new \Exception("Failed to create categories storage directory");
                        }
                    }

                    if (!is_writable($storagePath)) {
                        throw new \Exception("Categories storage directory is not writable. Check permissions.");
                    }

                    // Store the image
                    $path = $img->store('categories', 'public');
                    
                    if (!$path) {
                        throw new \Exception("Failed to store image file at index $index");
                    }

                    // Verify the file was actually stored
                    if (!Storage::disk('public')->exists($path)) {
                        throw new \Exception("Image file was not properly saved at index $index");
                    }

                    // Create database record
                    $imageRecord = $category->images()->create(['url' => "/storage/$path"]);
                    
                    if (!$imageRecord) {
                        // Clean up the stored file if database insertion fails
                        Storage::disk('public')->delete($path);
                        throw new \Exception("Failed to save image record to database at index $index");
                    }

                } catch (\Exception $e) {
                    Log::error("Category image upload error: " . $e->getMessage(), [
                        'category_id' => $category->id,
                        'file_index' => $index,
                        'file_name' => $img->getClientOriginalName(),
                        'file_size' => $img->getSize(),
                        'storage_path' => storage_path('app/public/categories'),
                        'permissions' => is_writable(storage_path('app/public/categories')) ? 'writable' : 'not writable'
                    ]);
                    
                    return back()->withErrors([
                        'images' => "Failed to upload image '" . $img->getClientOriginalName() . "': " . $e->getMessage()
                    ])->withInput();
                }
            }
        }

        return redirect()->route('admin.categories.index')
            ->with('success', 'Category updated successfully!');
    }

    public function destroy(Category $category): RedirectResponse
    {
        // Check if category has products
        if ($category->products()->count() > 0) {
            return back()->withErrors([
                'error' => 'Cannot delete category that has products.'
            ]);
        }

        // Check if category has children
        if ($category->children()->count() > 0) {
            return back()->withErrors([
                'error' => 'Cannot delete category that has subcategories.'
            ]);
        }

        // Delete category images from storage
        foreach ($category->images as $image) {
            $path = str_replace('/storage/', '', $image->url);
            Storage::disk('public')->delete($path);
        }

        $category->delete();

        return redirect()
            ->route('admin.categories.index')
            ->with('success', __('Category deleted successfully!'));
    }

    public function deleteImage(Image $image): JsonResponse
    {
        // Verify the image belongs to a category
        if ($image->imageable_type !== Category::class) {
            return response()->json(
                [
                    'error' => 'Image not found'
                ], 
                404
            );
        }

        // Delete from storage
        $path = str_replace('/storage/', '', $image->url);
        Storage::disk('public')->delete($path);

        // Delete from database
        $image->delete();

        return response()->json([
            'success' => 'Image deleted successfully'
        ]);
    }
}
