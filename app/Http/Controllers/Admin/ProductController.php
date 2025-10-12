<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Image;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProductController extends Controller
{
public function index(): View
    {
        $products = Product::with('category', 'images', 'videos')->latest()->get();

        return view('admin.products.index', compact('products'));
    }

    public function create(): View
    {
        // Get all categories with parent relationship
        $allCategories = Category::with('parent')->orderBy('name')->get();
        
        // Build hierarchical list
        $categories = $this->buildCategoryHierarchy($allCategories);

        // Get max upload size from server configuration
        $maxUploadSize = $this->getMaxUploadSize();

        return view('admin.products.create', compact('categories', 'maxUploadSize'));
    }

    /**
     * Get the maximum upload file size from server configuration
     * Returns size in bytes
     */
    private function getMaxUploadSize(): int
    {
        // Get upload_max_filesize and post_max_size from php.ini
        $uploadMax = $this->parseSize(ini_get('upload_max_filesize'));
        $postMax = $this->parseSize(ini_get('post_max_size'));
        
        // Return the smaller of the two
        return min($uploadMax, $postMax);
    }

    /**
     * Parse size string (like "8M", "2G") to bytes
     */
    private function parseSize(string $size): int
    {
        $unit = strtoupper(substr($size, -1));
        $value = (int) substr($size, 0, -1);
        
        switch ($unit) {
            case 'G':
                $value *= 1024;
                // fall through
            case 'M':
                $value *= 1024;
                // fall through
            case 'K':
                $value *= 1024;
        }
        
        return $value;
    }

    /**
     * Build a hierarchical list of categories for display in dropdown
     */
    private function buildCategoryHierarchy($allCategories, $parentId = null, $prefix = '')
    {
        $hierarchy = [];
        
        foreach ($allCategories as $category) {
            if ($category->parent_id == $parentId) {
                // Add category with prefix for visual hierarchy
                $hierarchy[] = (object)[
                    'id' => $category->id,
                    'name' => $prefix . $category->name,
                    'parent_id' => $category->parent_id
                ];
                
                // Recursively add children with increased indentation
                $children = $this->buildCategoryHierarchy($allCategories, $category->id, $prefix . '→ ');
                $hierarchy = array_merge($hierarchy, $children);
            }
        }
        
        return $hierarchy;
    }

    public function store(Request $request): RedirectResponse
    {
        // Get max upload size for validation
        $maxUploadSize = $this->getMaxUploadSize();
        $maxUploadSizeMB = $maxUploadSize / 1024 / 1024;

        // Validate file sizes before processing
        if ($request->has('images')) {
            $totalSize = 0;
            $oversizedFiles = [];
            
            foreach ($request->file('images') as $index => $file) {
                if ($file && $file->isValid()) {
                    $fileSize = $file->getSize();
                    $totalSize += $fileSize;
                    
                    // Check individual file size
                    if ($fileSize > $maxUploadSize) {
                        $fileSizeMB = round($fileSize / 1024 / 1024, 2);
                        $oversizedFiles[] = "{$file->getClientOriginalName()} ({$fileSizeMB}MB)";
                    }
                } elseif ($file) {
                    // Handle invalid file uploads
                    $error = $file->getErrorMessage();
                    Log::error("File upload validation error", [
                        'file_index' => $index,
                        'error_code' => $file->getError(),
                        'error_message' => $error,
                        'file_name' => $file->getClientOriginalName(),
                        'file_size' => $file->getSize(),
                        'upload_max_filesize' => ini_get('upload_max_filesize'),
                        'post_max_size' => ini_get('post_max_size'),
                    ]);
                    
                    return back()->withErrors([
                        'images' => __("File upload failed for ':filename': :error. Please check the file and try again.", [
                            'filename' => $file->getClientOriginalName(),
                            'error' => $error
                        ])
                    ])->withInput();
                }
            }
            
            // Check if any files exceed the size limit
            if (!empty($oversizedFiles)) {
                $filesList = implode(', ', $oversizedFiles);
                return back()->withErrors([
                    'images' => __("The following files exceed the maximum upload size of :maxMB MB: :files. Please select smaller files.", [
                        'maxMB' => round($maxUploadSizeMB, 0),
                        'files' => $filesList
                    ])
                ])->withInput();
            }
            
            // Check total size
            if ($totalSize > $maxUploadSize) {
                $totalSizeMB = round($totalSize / 1024 / 1024, 2);
                return back()->withErrors([
                    'images' => __("Total size of all files (:totalMB MB) exceeds the server limit of :maxMB MB. Please upload fewer images or reduce their size.", [
                        'totalMB' => $totalSizeMB,
                        'maxMB' => round($maxUploadSizeMB, 0)
                    ])
                ])->withInput();
            }
        }

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
            'name', 'description', 'price', 'no_of_items', 'category_id') + [
            'user_id' => $request->user()->id,
        ]);

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
                    $storagePath = storage_path('app/public/products');
                    if (!is_dir($storagePath)) {
                        if (!mkdir($storagePath, 0755, true)) {
                            throw new \Exception("Failed to create products storage directory");
                        }
                    }

                    if (!is_writable($storagePath)) {
                        throw new \Exception("Products storage directory is not writable. Check permissions.");
                    }

                    // Store the image
                    $path = $img->store('products', 'public');
                    
                    if (!$path) {
                        throw new \Exception("Failed to store image file at index $index");
                    }

                    // Verify the file was actually stored
                    if (!Storage::disk('public')->exists($path)) {
                        throw new \Exception("Image file was not properly saved at index $index");
                    }

                    // Create database record
                    $imageRecord = $product->images()->create(['url' => "/storage/$path"]);
                    
                    if (!$imageRecord) {
                        // Clean up the stored file if database insertion fails
                        Storage::disk('public')->delete($path);
                        throw new \Exception("Failed to save image record to database at index $index");
                    }

                } catch (\Exception $e) {
                    Log::error("Product image upload error: " . $e->getMessage(), [
                        'product_id' => $product->id,
                        'file_index' => $index,
                        'file_name' => $img->getClientOriginalName(),
                        'file_size' => $img->getSize(),
                        'storage_path' => storage_path('app/public/products'),
                        'permissions' => is_writable(storage_path('app/public/products')) ? 'writable' : 'not writable'
                    ]);
                    
                    return back()->withErrors([
                        'images' => "Failed to upload image '" . $img->getClientOriginalName() . "': " . $e->getMessage()
                    ])->withInput();
                }
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

        $redirectRoute = 'admin.products.index';
        if (!auth()->user()->isAdmin()) {
            $redirectRoute = 'my.products';
        }

        return redirect()
            ->route($redirectRoute)
            ->with('success', __('Product created successfully!'));
    }

    public function edit(Product $product): View
    {
        if (!auth()->user()->isAdmin() && $product->user_id !== auth()->id()) {
            abort(403, 'You are not authorized to edit this product.');
        }

        // Get all categories with parent relationship
        $allCategories = Category::with('parent')->orderBy('name')->get();
        
        // Build hierarchical list
        $categories = $this->buildCategoryHierarchy($allCategories);
        
        // Get max upload size from server configuration
        $maxUploadSize = $this->getMaxUploadSize();
        
        $product->load('images', 'videos');

        return view(
            'admin.products.edit', 
            compact('product', 'categories', 'maxUploadSize')
        );
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        if (!auth()->user()->isAdmin() && $product->user_id !== auth()->id()) {
            abort(403, 'You are not authorized to edit this product.');
        }

        // Get max upload size for validation
        $maxUploadSize = $this->getMaxUploadSize();
        $maxUploadSizeMB = $maxUploadSize / 1024 / 1024;

        // Validate file sizes before processing
        if ($request->has('images')) {
            $totalSize = 0;
            $oversizedFiles = [];
            
            foreach ($request->file('images') as $index => $file) {
                if ($file && $file->isValid()) {
                    $fileSize = $file->getSize();
                    $totalSize += $fileSize;
                    
                    // Check individual file size
                    if ($fileSize > $maxUploadSize) {
                        $fileSizeMB = round($fileSize / 1024 / 1024, 2);
                        $oversizedFiles[] = "{$file->getClientOriginalName()} ({$fileSizeMB}MB)";
                    }
                } elseif ($file) {
                    // Handle invalid file uploads
                    $error = $file->getErrorMessage();
                    Log::error("File upload validation error", [
                        'file_index' => $index,
                        'error_code' => $file->getError(),
                        'error_message' => $error,
                        'file_name' => $file->getClientOriginalName(),
                        'file_size' => $file->getSize(),
                        'upload_max_filesize' => ini_get('upload_max_filesize'),
                        'post_max_size' => ini_get('post_max_size'),
                    ]);
                    
                    return back()->withErrors([
                        'images' => __("File upload failed for ':filename': :error. Please check the file and try again.", [
                            'filename' => $file->getClientOriginalName(),
                            'error' => $error
                        ])
                    ])->withInput();
                }
            }
            
            // Check if any files exceed the size limit
            if (!empty($oversizedFiles)) {
                $filesList = implode(', ', $oversizedFiles);
                return back()->withErrors([
                    'images' => __("The following files exceed the maximum upload size of :maxMB MB: :files. Please select smaller files.", [
                        'maxMB' => round($maxUploadSizeMB, 0),
                        'files' => $filesList
                    ])
                ])->withInput();
            }
            
            // Check total size
            if ($totalSize > $maxUploadSize) {
                $totalSizeMB = round($totalSize / 1024 / 1024, 2);
                return back()->withErrors([
                    'images' => __("Total size of all files (:totalMB MB) exceeds the server limit of :maxMB MB. Please upload fewer images or reduce their size.", [
                        'totalMB' => $totalSizeMB,
                        'maxMB' => round($maxUploadSizeMB, 0)
                    ])
                ])->withInput();
            }
        }

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
                    $storagePath = storage_path('app/public/products');
                    if (!is_dir($storagePath)) {
                        if (!mkdir($storagePath, 0755, true)) {
                            throw new \Exception("Failed to create products storage directory");
                        }
                    }

                    if (!is_writable($storagePath)) {
                        throw new \Exception("Products storage directory is not writable. Check permissions.");
                    }

                    // Store the image
                    $path = $img->store('products', 'public');
                    
                    if (!$path) {
                        throw new \Exception("Failed to store image file at index $index");
                    }

                    // Verify the file was actually stored
                    if (!Storage::disk('public')->exists($path)) {
                        throw new \Exception("Image file was not properly saved at index $index");
                    }

                    // Create database record
                    $imageRecord = $product->images()->create(['url' => "/storage/$path"]);
                    
                    if (!$imageRecord) {
                        // Clean up the stored file if database insertion fails
                        Storage::disk('public')->delete($path);
                        throw new \Exception("Failed to save image record to database at index $index");
                    }

                } catch (\Exception $e) {
                    Log::error("Product image upload error: " . $e->getMessage(), [
                        'product_id' => $product->id,
                        'file_index' => $index,
                        'file_name' => $img->getClientOriginalName(),
                        'file_size' => $img->getSize(),
                        'storage_path' => storage_path('app/public/products'),
                        'permissions' => is_writable(storage_path('app/public/products')) ? 'writable' : 'not writable'
                    ]);
                    
                    return back()->withErrors([
                        'images' => "Failed to upload image '" . $img->getClientOriginalName() . "': " . $e->getMessage()
                    ])->withInput();
                }
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

        $redirectRoute = 'admin.products.index';
        if (!auth()->user()->isAdmin()) {
            $redirectRoute = 'my.products';
        }

        return redirect()
            ->route($redirectRoute)
            ->with('success', __('Product updated successfully!'));
    }

    public function destroy(Product $product): RedirectResponse
    {
        /**
         * todo use permission
         */
        if (!auth()->user()->isAdmin() && $product->user_id !== auth()->id()) {
            abort(403, 'You are not authorized to delete this product.');
        }

        // Delete product images from storage
        foreach ($product->images as $image) {
            $path = str_replace('/storage/', '', $image->url);
            Storage::disk('public')->delete($path);
        }

        // Delete the product (cascade will handle images and videos)
        $product->delete();

        $redirectRoute = 'admin.products.index';
        if (!auth()->user()->isAdmin()) {
            $redirectRoute = 'my.products';
        }

        return redirect()
            ->route($redirectRoute)
            ->with('success', __('Product deleted successfully!'));
    }

    public function deleteImage(Image $image): JsonResponse
    {
        // Verify the image belongs to a product
        if ($image->imageable_type !== Product::class) {
            return response()->json([
                'error' => 'Image not found'], 
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
