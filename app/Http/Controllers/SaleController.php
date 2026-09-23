<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Invoice;
use App\Models\Product;
use App\Models\ProductBatch;
use App\Models\ProductHistory;
use App\Services\FifoBatchService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class  SaleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('sales.index');
    }

    public function createRandomPassword()
    {
        $station = 'SAHAD';
        $sum = DB::table('sales')->count() + 1;
        
        // Get current logged-in user's initials
        $user = auth()->user();
        $userInitials = '';
        if ($user && $user->name) {
            $nameParts = explode(' ', $user->name);
            $userInitials = strtoupper($nameParts[0][0] ?? '');
            if (isset($nameParts[1])) {
                $userInitials .= strtoupper($nameParts[1][0] ?? '');
            }
        }
        
        $pass = substr($station, 0, 3) . "" . $userInitials . "" . date('d') . "" . date('m') . "" . date('y') . "-" . sprintf('%04d', $sum);
        return strtoupper($pass);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // Always generate a fresh invoice for each new sale
        $invoice = $this->createRandomPassword();
        session()->put('invoice', $invoice);
        return view('sales.create', compact('invoice'));
    }

    public function searchItem(Request $request)
    {

        $keyword = trim($request->search_keyword, "");

        $products = DB::table('products')
            ->where(DB::raw('lower(name)'), 'like', '%' . strtolower($keyword) . '%')
            ->where('qty', '>=', '1')
            ->get();
        // dd($products);
        echo '<ul class="nav flex-column">';
        if ($products) {
            foreach ($products as $product) {
                // Default to the batch holding the most stock so a single
                // dispense can usually come from one batch.
                $defaultBatch = DB::table('product_batches')
                    ->where('product_id', $product->id)
                    ->where('qty_remaining', '>', 0)
                    ->orderByDesc('qty_remaining')
                    ->orderBy('received_at')
                    ->first();
                $url = base64_encode($product->id . ',' . session()->get('invoice') . ',' . $product->buying_price . ',' . ($defaultBatch ? $defaultBatch->id : 0));
                echo '<li class="nav-item">
                <a href="' . route('app.sales.cart', $url) . '" class="nav-link">
                  <strong>' . $product->name . '</strong>
                  <span class="float-right badge bg-primary">&#8358; ' . number_format($product->buying_price, 2) . '</span>
                  <span class="badge bg-info">Available: ' . $product->qty . '</span>
                </a>
              </li>';
            }
            echo '</ul>';
        }else{
            echo '<p>No products found</p>';
        }
    }


    public function cart($invoice)
    {
        $data = explode(',', base64_decode($invoice));
        // data: [product_id, invoice, buying_price, batch_id]
        $productId = (int) ($data[0] ?? 0);
        $salesInvoice = $data[1] ?? null;
        $price = $data[2] ?? null;
        $batchId = isset($data[3]) ? (int) $data[3] : 0;

        $product = DB::table('products')
            ->where('id', $productId)
            ->first();

        $batch = $batchId
            ? DB::table('product_batches')->where('id', $batchId)->where('product_id', $productId)->first()
            : null;

        $existing = DB::table('sales_order')
            ->where('product_id', $productId)
            ->where('invoice', $salesInvoice)
            ->where('user_id', auth()->user()->id)
            ->where('product_batch_id', $batch ? $batch->id : null)
            ->first();

        if ($existing) {
            $max = $batch ? (int) $batch->qty_remaining : PHP_INT_MAX;
            $newQty = min($existing->quantity + 1, $max);

            DB::table('sales_order')
                ->where('id', $existing->id)
                ->update([
                    'quantity' => $newQty,
                    'amount' => $existing->price * $newQty,
                    'updated_at' => DB::raw('CURRENT_TIMESTAMP'),
                ]);
        } else {
            DB::table('sales_order')->insert([
                'invoice' => $salesInvoice,
                'product_id' => $productId,
                'product_batch_id' => $batch ? $batch->id : null,
                'quantity' => 1,
                'price' => $price,
                'amount' => $price,
                'product_category' => $product->product_category,
                'user_id' => auth()->user()->id,
                'created_at' => DB::raw('CURRENT_TIMESTAMP'),
                'updated_at' => DB::raw('CURRENT_TIMESTAMP'),
            ]);
        }

        return redirect()->route('app.sales.create');
    }


    public function saveSale(Request $request, $sale)
    {

        $sales_order = DB::table('sales_order')
            ->where('invoice', $sale)
            ->where('user_id', auth()->user()->id)
            ->get();

        try {
            DB::transaction(function () use ($sales_order, $request, $sale) {
                foreach ($sales_order as $order) {
                    $batch = $order->product_batch_id ? ProductBatch::find($order->product_batch_id) : null;

                    // Each line must be fully available in its selected batch.
                    if ($batch && (int) $batch->qty_remaining < (int) $order->quantity) {
                        throw new \Exception(
                            "Insufficient stock in batch '{$batch->batch_no}' for product #{$order->product_id}. " .
                            "Available: {$batch->qty_remaining}, requested: {$order->quantity}"
                        );
                    }

                    $sales = Sale::create([
                        'invoice' => $sale,
                        'product_id' => $order->product_id,
                        'quantity' => $order->quantity,
                        'amount' => $order->amount,
                        'user_id' => auth()->user()->id,
                        'price' => $order->price,
                        'buyer_name' => $request->input('buyer_name'),
                        'buyer_dept' => $request->input('buyer_dept')
                    ]);

                    // Get product before update
                    $product = Product::find($order->product_id);
                    $qtyBefore = $product->qty;
                    $qtyAfter = $qtyBefore - $order->quantity;
                    
                    // Update product quantity
                    DB::table('products')
                        ->where('id',  $order->product_id)
                        ->update(['qty' => DB::raw('qty - ' . $order->quantity)]);
                    
                    // Dispense from the selected batch and record which batch was used
                    if ($batch) {
                        FifoBatchService::dispense($batch, (int) $order->quantity, $sales);
                    }
                    
                    // Record product history
                    ProductHistory::create([
                        'product_id' => $order->product_id,
                        'user_id' => auth()->user()->id,
                        'type' => 'sale',
                        'qty_before' => $qtyBefore,
                        'qty_after' => $qtyAfter,
                        'qty_changed' => $order->quantity,
                        'invoice' => $sale,
                        'buyer_name' => $request->input('buyer_name'),
                        'buyer_dept' => $request->input('buyer_dept'),
                    ]);
                }
                $invoice = Invoice::create([
                    'invoice' => $sale,
                    'buyer_name' => $request->input('buyer_name'),
                    'buyer_dept' => $request->input('buyer_dept'),
                    'created_at' => now(),
                ]);
               $delete = DB::table('sales_order')
                ->where('invoice', $sale)
                ->where('user_id', auth()->user()->id)
                ->delete();
            });
        } catch (\Throwable $e) {
            return redirect()->route('app.sales.create')->with('error', 'Sale could not be saved: ' . $e->getMessage());
        }

        session()->forget('invoice');
        return redirect()->route('app.sales.create')->with('success', 'Sales Saved');
    }
    public function saveSalePrint(Request $request, $invoice)
    {
        $sales_order = DB::table('sales_order')
            ->where('invoice', $invoice)
            ->where('user_id', auth()->user()->id)
            ->get();
        try {
            DB::transaction(function () use ($sales_order, $request, $invoice) {
                foreach ($sales_order as $order) {
                    $batch = $order->product_batch_id ? ProductBatch::find($order->product_batch_id) : null;

                    if ($batch && (int) $batch->qty_remaining < (int) $order->quantity) {
                        throw new \Exception(
                            "Insufficient stock in batch '{$batch->batch_no}' for product #{$order->product_id}. " .
                            "Available: {$batch->qty_remaining}, requested: {$order->quantity}"
                        );
                    }

                    $sales = Sale::create([
                        'invoice' => $invoice,
                        'product_id' => $order->product_id,
                        'quantity' => $order->quantity,
                        'amount' => $order->amount,
                        'user_id' => auth()->user()->id,
                        'price' => $order->price,
                        'buyer_name' => $request->input('buyer_name'),
                        'buyer_dept' => $request->input('buyer_dept')
                    ]);
                    
                    // Get product before update
                    $product = Product::find($order->product_id);
                    $qtyBefore = $product->qty;
                    $qtyAfter = $qtyBefore - $order->quantity;
                    
                    // Update product quantity
                    DB::table('products')
                        ->where('id',  $order->product_id)
                        ->update(['qty' => DB::raw('qty - ' . $order->quantity)]);
                    
                    // Dispense from the selected batch and record which batch was used
                    if ($batch) {
                        FifoBatchService::dispense($batch, (int) $order->quantity, $sales);
                    }
                    
                    // Record product history
                    ProductHistory::create([
                        'product_id' => $order->product_id,
                        'user_id' => auth()->user()->id,
                        'type' => 'sale',
                        'qty_before' => $qtyBefore,
                        'qty_after' => $qtyAfter,
                        'qty_changed' => $order->quantity,
                        'invoice' => $invoice,
                        'buyer_name' => $request->input('buyer_name'),
                        'buyer_dept' => $request->input('buyer_dept'),
                    ]);
                }
                $invoices = Invoice::create([
                    'invoice' => $invoice,
                    'buyer_name' => $request->input('buyer_name'),
                    'buyer_dept' => $request->input('buyer_dept'),
                    'created_at' => now(),
                ]);
                DB::table('sales_order')->where('invoice', $invoice)->where('user_id',auth()->user()->id)->delete();
            });
        } catch (\Throwable $e) {
            return redirect()->route('app.sales.create')->with('error', 'Sale could not be saved: ' . $e->getMessage());
        }
        session()->forget('invoice');
        return redirect()->route('app.sales.print', $invoice);
    }

    public function cancelSale($invoice)
    {
        DB::table('sales_order')->where('invoice', $invoice)->delete();
        session()->forget('invoice');
        return redirect()->route('app.sales.create');
    }

    public function removeProduct($salesOrder)
    {
        DB::table('sales_order')->where('id', $salesOrder)->delete();
        return redirect()->route('app.sales.create');
    }

    public function printInvoice($invoice)
    {
        $items = DB::table('sales')
            ->select('sales.*', 'products.name as product', 'products.selling_price')
            ->join('products', 'products.id', '=', 'sales.product_id')
            ->where('sales.invoice', $invoice)
            ->where('sales.user_id', auth()->user()->id)
            ->get();
        $sum = DB::table('sales')
            ->select(DB::raw('SUM(amount) as sum'))
            ->where('invoice', $invoice)
            ->where('user_id', auth()->user()->id)
            ->first();
        $user = DB::table('sales')
            ->select('users.name')
            ->join('users', 'users.id', '=', 'sales.user_id')
            ->where('sales.invoice', $invoice)
            ->first();
        $buyer = DB::table('sales')
            ->select('buyer_name', 'buyer_dept')
            ->where('invoice', $invoice)
            ->where('user_id', auth()->user()->id)
            ->first();

        return view('sales.print', compact('items', 'invoice', 'sum', 'user', 'buyer'));
    }

    public function returnShow($invoice)
    {

        $items = DB::table('sales')
            ->select('sales.*', 'products.name as product', 'products.selling_price')
            ->join('products', 'products.id', '=', 'sales.product_id')
            ->where('sales.invoice', $invoice)
            ->get();
        // dd($items);
        return view('sales.return-show', compact('items'));
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function syncStore(Request $request)
    {
        $products = Product::updateOrCreate(
            ['name' => $request->name, 'store_id' => $request->store_id],
            [
                'buying_price' => $request->buying_price,
                'selling_price' => $request->selling_price,
                'qty' => $request->qty,
                'expiry_date' => $request->expiry_date
            ]
        );

        if ($products) {
            return response()->json([
                'success' => true,
                'message' => 'Activity successfully created.',
            ]);
        }
    }


    public function store(Request $request)
    {

        $sales = DB::table('sales')->insert([
            'invoice' => $request->invoice,
            'product_id' => $request->product_id,
            'user_id' => $request->user_id,
            'amount'  => $request->amount,
            'quantity' => $request->qty,
            'station_id' => $request->station_id,
            'created_at' => now(),
        ]);
        if ($sales) {
            return response()->json([
                'success' => true,
                'message' => 'Activity successfully created.',
            ]);
        }
    }


    public function synced(Request $request)
    {
        DB::table('sales')
            ->where('invoice', $request->invoice)
            ->where('product_id', $request->product_id)
            ->update(['synced' => true]);
    }
    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Sale  $sale
     * @return \Illuminate\Http\Response
     */
    public function show(Sale $sale)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Sale  $sale
     * @return \Illuminate\Http\Response
     */
    public function edit(Sale $sale)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Sale  $sale
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Sale $sale)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Sale  $sale
     * @return \Illuminate\Http\Response
     */
    public function destroy(Sale $sale)
    {
        $sale->delete();

        return back()->with('Sale Deleted');
    }
}
