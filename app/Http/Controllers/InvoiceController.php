<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\InvoiceOrder;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InvoiceController extends Controller
{
     /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        // $this->authorize('read-roles');
        // $invoice = Invoice::all();

        return view('invoices.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

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
     * @param  \App\Models\Invoice  $invoice
     * @return \Illuminate\Http\Response
     */
    public function show(Invoice $invoice)
    {
        $items = DB::table('sales')
            ->select('sales.*', 'products.name as product', 'products.selling_price')
            ->join('products', 'products.id', '=', 'sales.product_id')
            ->where('sales.invoice', $invoice->invoice)
            // ->where('sales.user_id', auth()->user()->id)
            ->get();
        $sum = DB::table('sales')
            ->select(DB::raw('SUM(amount) as sum'))
            ->where('invoice', $invoice->invoice)
            // ->where('user_id', auth()->user()->id)
            ->first();
        $user = DB::table('sales')
            ->select('users.name')
            ->join('users', 'users.id', '=', 'sales.user_id')
            ->where('sales.invoice', $invoice->invoice)
            ->first();
            // dd($items);
        return view('invoices.show', compact('items', 'invoice', 'sum', 'user'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Invoice  $invoice
     * @return \Illuminate\Http\Response
     */
    public function edit(Invoice $invoice)
    {

    }

    public function invoice($invoice)
    {
        $sales_order = DB::table('sales_order')
            ->where('invoice', $invoice)
            ->get();
        // dd($sales_order);
        foreach ($sales_order as $order) {
            $sales = InvoiceOrder::create([
                'invoice' => $invoice,
                'product_id' => $order->product_id,
                'quantity' => $order->quantity,
                'amount' => $order->amount,
                'user_id' => auth()->user()->id,
                'price' => $order->price
            ]);

        }
        $invoice = Invoice::create([
            'invoice' => $invoice,
            'created_at' => now(),
        ]);
        $delete = DB::table('sales_order')
            ->where('invoice', $invoice)
            ->where('user_id', auth()->user()->id)
            ->delete();
        session()->forget('invoice');
        return redirect()->route('app.invoice.print', $invoice->invoice);
    }

    public function invoicePrint($invoice)
    {
        $items = DB::table('invoice_orders')
            ->select('invoice_orders.*', 'products.name as product', 'products.selling_price')
            ->join('products', 'products.id', '=', 'invoice_orders.product_id')
            ->where('invoice_orders.invoice', $invoice)
            ->where('invoice_orders.user_id', auth()->user()->id)
            ->get();
        $sum = DB::table('invoice_orders')
            ->select(DB::raw('SUM(amount) as sum'))
            ->where('invoice', $invoice)
            ->where('user_id', auth()->user()->id)
            ->first();
        $user = DB::table('invoice_orders')
            ->select('users.name')
            ->join('users', 'users.id', '=', 'invoice_orders.user_id')
            ->where('invoice_orders.invoice', $invoice)
            ->first();

        return view('invoices.print', compact('items', 'invoice', 'sum', 'user'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Invoice  $invoice
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Invoice $invoice)
    {


    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Invoice  $invoice
     * @return \Illuminate\Http\Response
     */
    public function destroy(Invoice $invoice)
    {
        $invoice->delete();
        return back()->with('success', 'Invoice Deleted');

    }
}
