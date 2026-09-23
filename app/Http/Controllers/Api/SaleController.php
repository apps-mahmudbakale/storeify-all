<?php

namespace App\Http\Controllers\Api;

use App\Models\Sale;
use NumberFormatter;
use App\Models\Invoice;
use App\Models\Product;
use App\Models\Requests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class SaleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getPrice(Request $request)
    {
        // dd($request->user);

        $product = Product::find($request->prid);
        $items = DB::table('products')
            ->where('id', $request->prid)
            ->first();

        $salesOrderId = (int) $request->input('soid');
        $batchId = (int) $request->input('batch_id', 0);
        $batch = null;

        // Available qty is capped by the selected batch's remaining stock.
        $available = (int) $items->qty;
        if ($batchId) {
            $batch = DB::table('product_batches')
                ->where('id', $batchId)
                ->where('product_id', $request->prid)
                ->first();

            if ($batch) {
                $available = (int) $batch->qty_remaining;
            }
        }

        $qty = (int) $request->qty;
        $msg = 'success';

        // If the requested qty is more than what the selected batch holds,
        // dispense the available and let the cashier add another line from
        // a different batch.
        if ($qty > $available) {
            $qty = max($available, 0);
            $msg = 'excess';
        }

        $amount = intval($request->price * $qty);

        $update = DB::table('sales_order')
            ->where('id', $salesOrderId)
            ->update([
                'quantity' => $qty,
                'amount' => $amount,
                'price' => intval($request->price),
                'product_batch_id' => $batch ? $batch->id : null,
            ]);

        $getAmount = DB::table('sales_order')
            ->where('id', $salesOrderId)
            ->first();
        $getSum = DB::table('sales_order')
            ->selectRaw('sum(amount) as total')
            ->where('invoice', $request->invoice)
            ->where('user_id', $request->user)
            ->first();
        $a = $getAmount ? number_format($getAmount->amount, 2) : '0.00';
        $b = number_format($getSum->total, 2);
        $format = new NumberFormatter("En", NumberFormatter::SPELLOUT);
        $words = strtoupper($format->format($getSum->total)) . " NAIRA ONLY";

        return response()->json([
            'amount' => $a,
            'total' => $b,
            'text' => $words,
            'qty' => $qty,
            'available' => $available,
            'msg' => $msg,
        ]);
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getSales($station)
    {
        $sales = Sale::where('station_id', $station)
            ->where('synced', 0)
            ->get();

        return response()->json($sales);
    }


    public function getProducts()
    {
        $products = Product::get();

        return response()->json($products);
    }

public function removeProduct(Request $request)
{
    DB::table('sales_order')
            ->where('product_id', $request->prid)
            ->where('invoice', $request->invoice)
            ->delete();
}

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }










    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        dd($request->all());
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
