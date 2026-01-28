<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReturnSaleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $requests = DB::table('return_request')
        ->selectRaw('DISTINCT invoice, status, date(created_at) as date')
        ->get();
        return view('sales-return.index', compact('requests'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
       return view('sales-return.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'invoice' => 'required',
            'items' => 'required|array',
            'rqty' => 'required|array',
        ]);

        if (auth()->user()->hasRole('admin')) {
            foreach ($request->items as $index => $productId) {
                $returnQty = $request->rqty[$index];
                
                if ($returnQty <= 0) continue;

                $sale = DB::table('sales')
                    ->where('invoice', $request->invoice)
                    ->where('product_id', $productId)
                    ->first();

                if (!$sale) continue;

                // Validate return quantity against sold quantity
                if ($returnQty > $sale->quantity) {
                    return back()->with('error', "Return quantity for product ID {$productId} exceeds sold quantity.");
                }

                // Update or Insert return request
                DB::table('return_request')->updateOrInsert(
                    ['invoice' => $request->invoice, 'product_id' => $productId],
                    [
                        'return_qty' => DB::raw('return_qty + ' . $returnQty),
                        'status' => true,
                        'updated_at' => now(),
                        'created_at' => now()
                    ]
                );

                // Update Sales Record
                $newQty = $sale->quantity - $returnQty;
                if ($newQty <= 0) {
                    DB::table('sales')
                        ->where('invoice', $request->invoice)
                        ->where('product_id', $productId)
                        ->delete();
                } else {
                    $newAmount = $newQty * $sale->price;
                    DB::table('sales')
                        ->where('invoice', $request->invoice)
                        ->where('product_id', $productId)
                        ->update([
                            'quantity' => $newQty,
                            'amount' => $newAmount,
                            'updated_at' => now()
                        ]);
                }

                // Update Product Stock
                DB::table('products')
                    ->where('id', $productId)
                    ->update(['qty' => DB::raw('qty + ' . $returnQty)]);
            }
            return redirect()->route('app.returns.index')->with('success', 'Return Processed Successfully');
        } else {
            foreach ($request->items as $index => $productId) {
                $returnQty = $request->rqty[$index];
                if ($returnQty <= 0) continue;

                $sale = DB::table('sales')
                    ->where('invoice', $request->invoice)
                    ->where('product_id', $productId)
                    ->first();

                if (!$sale || $returnQty > $sale->quantity) continue;

                DB::table('return_request')->insert([
                    'invoice' => $request->invoice,
                    'product_id' => $productId,
                    'return_qty' => $returnQty,
                    'status' => false,
                    'created_at' => now()
                ]);
            }
            return redirect()->route('app.returns.index')->with('success', 'Return Request Sent for Approval');
        }
    }



    public function approve(Request $request)
    {
        $request->validate([
            'invoice' => 'required',
        ]);

        $pendingReturns = DB::table('return_request')
            ->where('invoice', $request->invoice)
            ->where('status', false)
            ->get();

        if ($pendingReturns->isEmpty()) {
            return back()->with('error', 'No pending return requests found for this invoice.');
        }

        DB::beginTransaction();
        try {
            foreach ($pendingReturns as $return) {
                $sale = DB::table('sales')
                    ->where('invoice', $return->invoice)
                    ->where('product_id', $return->product_id)
                    ->first();

                if (!$sale) continue;

                // Update Sales Record
                $newQty = $sale->quantity - $return->return_qty;
                if ($newQty <= 0) {
                    DB::table('sales')
                        ->where('invoice', $return->invoice)
                        ->where('product_id', $return->product_id)
                        ->delete();
                } else {
                    $newAmount = $newQty * $sale->price;
                    DB::table('sales')
                        ->where('invoice', $return->invoice)
                        ->where('product_id', $return->product_id)
                        ->update([
                            'quantity' => $newQty,
                            'amount' => $newAmount,
                            'updated_at' => now()
                        ]);
                }

                // Update Product Stock
                DB::table('products')
                    ->where('id', $return->product_id)
                    ->update(['qty' => DB::raw('qty + ' . $return->return_qty)]);

                // Mark as approved
                DB::table('return_request')
                    ->where('id', $return->id)
                    ->update(['status' => true, 'updated_at' => now()]);
            }
            DB::commit();
            return redirect()->route('app.returns.index')->with('success', 'Return Request(s) Approved and Processed');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to approve returns: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  string  $invoice
     * @return \Illuminate\Http\Response
     */
    public function show($invoice)
    {
        $items = DB::table('sales')
            ->select('sales.*', 'products.name as product', 'products.selling_price')
            ->join('products', 'products.id', '=', 'sales.product_id')
            ->where('sales.invoice', $invoice)
            ->get();

        $returnRequests = DB::table('return_request')
            ->where('invoice', $invoice)
            ->get()
            ->keyBy('product_id');

        $isDone = $returnRequests->isNotEmpty() && $returnRequests->every(fn($r) => $r->status);

        return view('sales-return.show', compact('items', 'invoice', 'returnRequests', 'isDone'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($invoice)
    {
        $items = DB::table('sales')
        ->select('sales.*','products.name as product', 'products.selling_price')
        ->join('products', 'products.id', '=', 'sales.product_id')
        ->where('sales.invoice', $invoice)
        ->get();

        return view('sales-return.edit', compact('items'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
