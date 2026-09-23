<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductBatch;
use App\Models\ProductHistory;
use App\Services\FifoBatchService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BatchController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * List all batches with their current stock levels.
     */
    public function index(Request $request)
    {
        $batches = ProductBatch::query()
            ->with('product')
            ->when($request->search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('batch_no', 'like', '%' . $search . '%')
                        ->orWhereHas('product', function ($p) use ($search) {
                            $p->where('name', 'like', '%' . $search . '%');
                        });
                });
            })
            ->orderBy('received_at', 'desc')
            ->orderBy('batch_no')
            ->paginate(50);

        return view('batches.index', compact('batches'));
    }

    /**
     * Show the form to create a new batch for an existing product.
     */
    public function create()
    {
        $products = Product::query()->orderBy('name')->get(['id', 'name', 'buying_price', 'expiry_date']);

        return view('batches.create', compact('products'));
    }

    /**
     * Store a newly created batch, adding its qty to the product stock.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'product_id' => 'required|exists:products,id',
            'batch_no' => 'required|string|max:255',
            'qty' => 'required|integer|min:1',
            'buying_price' => 'nullable|numeric|min:0',
            'expiry_date' => 'nullable|date',
            'received_at' => 'nullable|date',
        ]);

        $product = Product::findOrFail($data['product_id']);
        $qtyBefore = (int) $product->qty;

        $product->increment('qty', (int) $data['qty']);

        FifoBatchService::addBatch(
            $product,
            (int) $data['qty'],
            [
                'batch_no' => $data['batch_no'],
                'buying_price' => $data['buying_price'] ?: $product->buying_price,
                'expiry_date' => $data['expiry_date'] ?: $product->expiry_date,
                'received_at' => $data['received_at'] ?: now()->format('Y-m-d'),
            ],
            $qtyBefore
        );

        return redirect()->route('app.batches.index')->with('success', 'Batch "' . $data['batch_no'] . '" created and stock added');
    }

    /**
     * Add more stock into an existing batch.
     */
    public function stock(Request $request)
    {
        $data = $request->validate([
            'batch_id' => 'required|exists:product_batches,id',
            'qty' => 'required|integer|min:1',
        ]);

        $batch = ProductBatch::with('product')->findOrFail($data['batch_id']);
        $product = $batch->product;

        FifoBatchService::stockBatch($batch, (int) $data['qty'], (int) $product->qty);

        return redirect()->route('app.batches.index')->with('success', 'Stocked ' . $data['qty'] . ' onto batch "' . $batch->batch_no . '"');
    }

    /**
     * Delete a batch. Any remaining qty is removed from the product stock.
     */
    public function destroy($id)
    {
        $batch = ProductBatch::with('product')->findOrFail($id);
        $product = $batch->product;

        if ($batch->qty_remaining > 0) {
            $qtyBefore = (int) $product->qty;
            $qtyAfter = $qtyBefore - (int) $batch->qty_remaining;

            DB::table('products')
                ->where('id', $product->id)
                ->decrement('qty', (int) $batch->qty_remaining);

            ProductHistory::create([
                'product_id' => $product->id,
                'user_id' => auth()->id(),
                'type' => 'adjustment',
                'qty_before' => $qtyBefore,
                'qty_after' => $qtyAfter,
                'qty_changed' => (int) $batch->qty_remaining,
                'notes' => 'Batch "' . $batch->batch_no . '" deleted',
            ]);
        }

        $batch->delete();

        return redirect()->route('app.batches.index')->with('success', 'Batch "' . $batch->batch_no . '" deleted');
    }
}