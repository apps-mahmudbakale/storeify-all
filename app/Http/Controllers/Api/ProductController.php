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
            'selling_price', 'buying_price', 'vat_percentage','image',
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

        $results = $query->paginate($perPage);

        // Add has_image flag to each item without loading the base64 data
        $results->getCollection()->transform(function ($product) {
            $product->has_image = !empty($product->image);
            return $product;
        });

        return response()->json($results);
    }

    /**
     * Get a single product with its base64 image.
     */
    public function show($id)
    {
        $product = Product::findOrFail($id);

        return response()->json([
            'id'               => $product->id,
            'name'             => $product->name,
            'barcode'          => $product->barcode,
            'product_category' => $product->product_category,
            'unit'             => $product->unit,
            'selling_price'    => $product->selling_price,
            'buying_price'     => $product->buying_price,
            'vat_percentage'   => $product->vat_percentage,
            'qty'              => $product->qty,
            'min_qty'          => $product->min_qty,
            'expiry_date'      => $product->expiry_date,
            'image'            => $product->image,
            'has_image'        => !empty($product->image),
            'created_at'       => $product->created_at,
            'updated_at'       => $product->updated_at,
        ]);
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
