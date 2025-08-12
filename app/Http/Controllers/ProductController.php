<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        // Latest products (show latest 6 with stock)
        $latestProducts = Product::with(['images', 'category', 'user'])
            ->where('no_of_items', '>', 0)
            ->latest()
            ->take(6)
            ->get();

        // Most popular products by view count (top 6)
        $popularProducts = Product::with(['images', 'category', 'user'])
            ->where('no_of_items', '>', 0)
            ->orderByDesc('views_count')
            ->take(6)
            ->get();

        return view('home', [
            'latestProducts' => $latestProducts,
            'popularProducts' => $popularProducts,
        ]);
    }

    /**
     * Display products page with search, filter and sort
     */
    public function products(Request $request): View
    {
        $query = Product::with(['category', 'images', 'user']);

        // Search by name and description
        if ($request->filled('search')) {
            $searchTerm = $request->get('search');
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('description', 'LIKE', "%{$searchTerm}%");
            });
        }

        // Filter by category
        if ($request->filled('category')) {
            $query->where('category_id', $request->get('category'));
        }

        // Filter by stock availability
        if ($request->filled('stock')) {
            if ($request->get('stock') === 'in_stock') {
                $query->where('no_of_items', '>', 0);
            } elseif ($request->get('stock') === 'out_of_stock') {
                $query->where('no_of_items', '<=', 0);
            }
        }

        // Price range filter
        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->get('min_price'));
        }
        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->get('max_price'));
        }

        // Sorting
        $sortBy = $request->get('sort', 'latest');
        switch ($sortBy) {
            case 'price_low':
                $query->orderBy('price', 'asc');
                break;
            case 'price_high':
                $query->orderBy('price', 'desc');
                break;
            case 'name_asc':
                $query->orderBy('name', 'asc');
                break;
            case 'name_desc':
                $query->orderBy('name', 'desc');
                break;
            case 'oldest':
                $query->orderBy('created_at', 'asc');
                break;
            default: // 'latest'
                $query->orderBy('created_at', 'desc');
                break;
        }

        $products = $query->paginate(12)->withQueryString();
        
        // Get categories for filter dropdown
        $categories = \App\Models\Category::orderBy('name')->get();
        
        // Get price range for filters
        $priceRange = Product::selectRaw('MIN(price) as min_price, MAX(price) as max_price')->first();

        return view('products.index', compact('products', 'categories', 'priceRange'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        // Increment views counter for popularity
        $product->increment('views_count');

        $product->load(['images', 'videos', 'user']);
        
        return view('products.show', compact('product'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        //
    }
}
