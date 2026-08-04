<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockClosing;
use App\Models\ProductHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class StockClosingController extends Controller
{
    /**
     * Display stock closing form for a period
     */
    public function index(Request $request)
    {
        $month = $request->get('month', now()->month);
        $year = $request->get('year', now()->year);
        
        $periodStart = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $periodEnd = $periodStart->copy()->endOfMonth();
        
        $products = Product::all();
        $closings = StockClosing::whereMonth('period_end', $month)
            ->whereYear('period_end', $year)
            ->get();
        
        return view('stock-closing.index', compact('products', 'month', 'year', 'periodStart', 'periodEnd', 'closings'));
    }

    /**
     * Show form to close stock for a product
     */
    public function create(Product $product, Request $request)
    {
        $month = $request->get('month', now()->month);
        $year = $request->get('year', now()->year);
        
        $periodStart = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $periodEnd = $periodStart->copy()->endOfMonth();
        
        // Get previous period's closing qty (if exists)
        $previousClosing = StockClosing::where('product_id', $product->id)
            ->where('period_end', '<', $periodStart)
            ->orderBy('period_end', 'desc')
            ->first();
        
        $openingQty = $previousClosing ? $previousClosing->closing_qty : 0;
        
        // Calculate dispensed qty for this period
        $qtyDispensed = ProductHistory::where('product_id', $product->id)
            ->where('type', 'sale')
            ->whereBetween('created_at', [$periodStart, $periodEnd])
            ->sum('qty_changed');
        
        // Expected closing qty
        $expectedClosing = $openingQty - $qtyDispensed;
        
        // Get sales history for this period
        $salesHistory = ProductHistory::where('product_id', $product->id)
            ->where('type', 'sale')
            ->whereBetween('created_at', [$periodStart, $periodEnd])
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();
        
        // Check if closing already exists for this period
        $existingClosing = StockClosing::where('product_id', $product->id)
            ->where('period_end', $periodEnd->toDateString())
            ->first();
        
        return view('stock-closing.create', compact(
            'product',
            'periodStart',
            'periodEnd',
            'openingQty',
            'qtyDispensed',
            'expectedClosing',
            'salesHistory',
            'existingClosing',
            'month',
            'year'
        ));
    }

    /**
     * Store stock closing
     */
    public function store(Product $product, Request $request)
    {
        $month = $request->get('month', now()->month);
        $year = $request->get('year', now()->year);
        
        $periodStart = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $periodEnd = $periodStart->copy()->endOfMonth();
        
        $request->validate([
            'closing_qty' => 'required|integer|min:0',
            'notes' => 'nullable|string|max:1000',
        ]);
        
        // Get previous closing qty
        $previousClosing = StockClosing::where('product_id', $product->id)
            ->where('period_end', '<', $periodStart)
            ->orderBy('period_end', 'desc')
            ->first();
        
        $openingQty = $previousClosing ? $previousClosing->closing_qty : 0;
        
        // Calculate dispensed qty
        $qtyDispensed = ProductHistory::where('product_id', $product->id)
            ->where('type', 'sale')
            ->whereBetween('created_at', [$periodStart, $periodEnd])
            ->sum('qty_changed');
        
        $closingQty = $request->input('closing_qty');
        $expectedClosing = $openingQty - $qtyDispensed;
        $variance = $closingQty - $expectedClosing;
        
        // Delete existing closing if updating
        StockClosing::where('product_id', $product->id)
            ->where('period_end', $periodEnd->toDateString())
            ->delete();
        
        // Create new closing
        StockClosing::create([
            'product_id' => $product->id,
            'user_id' => auth()->user()->id,
            'opening_qty' => $openingQty,
            'closing_qty' => $closingQty,
            'qty_dispensed' => $qtyDispensed,
            'expected_closing' => $expectedClosing,
            'variance' => $variance,
            'period_start' => $periodStart,
            'period_end' => $periodEnd,
            'notes' => $request->input('notes'),
        ]);
        
        return redirect()->route('app.stock-closing.show', ['product' => $product->id, 'month' => $month, 'year' => $year])
            ->with('success', 'Stock closing recorded successfully');
    }

    /**
     * Show stock closing details with sales history
     */
    public function show(Product $product, Request $request)
    {
        $month = $request->get('month', now()->month);
        $year = $request->get('year', now()->year);
        
        $periodStart = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $periodEnd = $periodStart->copy()->endOfMonth();
        
        $closing = StockClosing::where('product_id', $product->id)
            ->where('period_end', $periodEnd->toDateString())
            ->with('user')
            ->firstOrFail();
        
        $salesHistory = ProductHistory::where('product_id', $product->id)
            ->where('type', 'sale')
            ->whereBetween('created_at', [$periodStart, $periodEnd])
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('stock-closing.show', compact('product', 'closing', 'salesHistory', 'month', 'year', 'periodStart', 'periodEnd'));
    }

    /**
     * Display report of all stock closings for a month
     */
    public function report(Request $request)
    {
        $month = $request->get('month', now()->month);
        $year = $request->get('year', now()->year);
        
        $periodStart = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $periodEnd = $periodStart->copy()->endOfMonth();
        
        $closings = StockClosing::with(['product', 'user'])
            ->whereMonth('period_end', $month)
            ->whereYear('period_end', $year)
            ->orderBy('created_at', 'desc')
            ->get();
        
        $totalVariance = $closings->sum('variance');
        $varianceByProduct = $closings->pluck('variance', 'product.name');
        
        return view('stock-closing.report', compact('closings', 'month', 'year', 'periodStart', 'periodEnd', 'totalVariance'));
    }
}
