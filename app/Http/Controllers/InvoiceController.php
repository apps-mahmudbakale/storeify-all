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
        $invoiceNumber = $invoice->invoice;

        // Try invoice_orders first, fall back to sales table
        $items = DB::table('invoice_orders')
            ->select('invoice_orders.*', 'products.name as product', 'products.selling_price')
            ->join('products', 'products.id', '=', 'invoice_orders.product_id')
            ->where('invoice_orders.invoice', $invoiceNumber)
            ->get();

        if ($items->isEmpty()) {
            $items = DB::table('sales')
                ->select('sales.*', 'products.name as product', 'products.selling_price')
                ->join('products', 'products.id', '=', 'sales.product_id')
                ->where('sales.invoice', $invoiceNumber)
                ->get();
        }

        $sum  = (object)['sum' => $items->sum('amount')];
        $user = DB::table('users')
            ->join('sales', 'users.id', '=', 'sales.user_id')
            ->where('sales.invoice', $invoiceNumber)
            ->select('users.name')
            ->first();

        $hasUnpaidOrders = DB::table('invoice_orders')->where('invoice', $invoiceNumber)->exists();
        $invoice = $invoiceNumber;

        return view('invoices.print', compact('items', 'invoice', 'sum', 'user', 'hasUnpaidOrders'));
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

    public function invoice(Request $request, $invoice)
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
            'buyer_name' => $request->input('buyer_name'),
            'buyer_dept' => $request->input('buyer_dept'),
            'created_at' => now(),
        ]);
        $delete = DB::table('sales_order')
            ->where('invoice', $invoice->invoice)
            ->where('user_id', auth()->user()->id)
            ->delete();
        session()->forget('invoice');
        return redirect()->route('app.invoice.print', $invoice->invoice);
    }

    public function invoicePrint($invoice)
    {
        // Try invoice_orders first, fall back to sales table
        $items = DB::table('invoice_orders')
            ->select('invoice_orders.*', 'products.name as product', 'products.selling_price')
            ->join('products', 'products.id', '=', 'invoice_orders.product_id')
            ->where('invoice_orders.invoice', $invoice)
            ->get();

        if ($items->isEmpty()) {
            $items = DB::table('sales')
                ->select('sales.*', 'products.name as product', 'products.selling_price')
                ->join('products', 'products.id', '=', 'sales.product_id')
                ->where('sales.invoice', $invoice)
                ->get();
        }

        $sum  = (object)['sum' => $items->sum('amount')];
        $user = DB::table('users')
            ->join('sales', 'users.id', '=', 'sales.user_id')
            ->where('sales.invoice', $invoice)
            ->select('users.name')
            ->first();

        $hasUnpaidOrders = DB::table('invoice_orders')->where('invoice', $invoice)->exists();

        return view('invoices.print', compact('items', 'invoice', 'sum', 'user', 'hasUnpaidOrders'));
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
