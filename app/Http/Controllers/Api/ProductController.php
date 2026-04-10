<?php

namespace App\Http\Controllers\Api;

use App\Models\Product;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * List all products (excludes image for performance).
     * Supports ?search=, ?category=, ?per_page=
     */
    public function index(Request $request)
    {
        $query = Product::select(
            'id', 'name', 'barcode', 'product_category', 'unit',
            'selling_price', 'buying_price', 'vat_percentage',
            'qty', 'min_qty', 'expiry_date', 'created_at', 'updated_at'
        );

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('barcode', $request->search);
        }

        if ($request->filled('category')) {
            $query->where('product_category', $request->category);
        }

        $perPage = min((int) $request->get('per_page', 20), 100);

        return response()->json($query->paginate($perPage));
    }

    /**
     * Get a single product with its base64 image.
     */
    public function show($id)
    {
        $product = Product::findOrFail($id);

        return response()->json($product);
    }

    /**
     * Get all categories.
     */
    public function categories()
    {
        $categories = Product::select('product_category')
            ->whereNotNull('product_category')
            ->distinct()
            ->orderBy('product_category')
            ->pluck('product_category');

        return response()->json($categories);
    }
}
