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
        // Check if invoice has items in invoice_orders table first
        $invoiceOrdersCount = DB::table('invoice_orders')
            ->where('invoice', $invoice->invoice)
            ->count();

        $hasUnpaidOrders = $invoiceOrdersCount > 0;

        if ($invoiceOrdersCount > 0) {
            // Use invoice_orders table
            $items = DB::table('invoice_orders')
                ->select('invoice_orders.*', 'products.name as product', 'products.selling_price')
                ->join('products', 'products.id', '=', 'invoice_orders.product_id')
                ->where('invoice_orders.invoice', $invoice->invoice)
                ->get();
            $sum = DB::table('invoice_orders')
                ->select(DB::raw('SUM(amount) as sum'))
                ->where('invoice', $invoice->invoice)
                ->first();
            $user = DB::table('invoice_orders')
                ->select('users.name')
                ->join('users', 'users.id', '=', 'invoice_orders.user_id')
                ->where('invoice_orders.invoice', $invoice->invoice)
                ->first();
        } else {
            // Use sales table (for invoices generated from sales page)
            $items = DB::table('sales')
                ->select('sales.*', 'products.name as product', 'products.selling_price')
                ->join('products', 'products.id', '=', 'sales.product_id')
                ->where('sales.invoice', $invoice->invoice)
                ->get();
            $sum = DB::table('sales')
                ->select(DB::raw('SUM(amount) as sum'))
                ->where('invoice', $invoice->invoice)
                ->first();
            $user = DB::table('sales')
                ->select('users.name')
                ->join('users', 'users.id', '=', 'sales.user_id')
                ->where('sales.invoice', $invoice->invoice)
                ->first();
        }

        $invoice = $invoice->invoice;

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
        // Check if invoice has items in invoice_orders table first
        $invoiceOrdersCount = DB::table('invoice_orders')
            ->where('invoice', $invoice)
            ->count();

        $hasUnpaidOrders = $invoiceOrdersCount > 0;

        if ($invoiceOrdersCount > 0) {
            // Use invoice_orders table
            $items = DB::table('invoice_orders')
                ->select('invoice_orders.*', 'products.name as product', 'products.selling_price')
                ->join('products', 'products.id', '=', 'invoice_orders.product_id')
                ->where('invoice_orders.invoice', $invoice)
                ->get();
            $sum = DB::table('invoice_orders')
                ->select(DB::raw('SUM(amount) as sum'))
                ->where('invoice', $invoice)
                ->first();
            $user = DB::table('invoice_orders')
                ->select('users.name')
                ->join('users', 'users.id', '=', 'invoice_orders.user_id')
                ->where('invoice_orders.invoice', $invoice)
                ->first();
        } else {
            // Use sales table (for invoices generated from sales page)
            $items = DB::table('sales')
                ->select('sales.*', 'products.name as product', 'products.selling_price')
                ->join('products', 'products.id', '=', 'sales.product_id')
                ->where('sales.invoice', $invoice)
                ->get();
            $sum = DB::table('sales')
                ->select(DB::raw('SUM(amount) as sum'))
                ->where('invoice', $invoice)
                ->first();
            $user = DB::table('sales')
                ->select('users.name')
                ->join('users', 'users.id', '=', 'sales.user_id')
                ->where('sales.invoice', $invoice)
                ->first();
        }

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
     * Confirm payment and move invoice orders to sales
     *
     * @param  string  $invoiceNumber
     * @return \Illuminate\Http\Response
     */
    public function confirmPayment($invoiceNumber)
    {
        // Check if invoice has items in invoice_orders table
        $invoiceOrders = DB::table('invoice_orders')
            ->where('invoice', $invoiceNumber)
            ->get();

        if ($invoiceOrders->isEmpty()) {
            return back()->with('error', 'No invoice orders found for this invoice.');
        }

        // Move each invoice order to sales table
        foreach ($invoiceOrders as $order) {
            Sale::create([
                'invoice' => $invoiceNumber,
                'product_id' => $order->product_id,
                'quantity' => $order->quantity,
                'amount' => $order->amount,
                'user_id' => $order->user_id,
                'price' => $order->price
            ]);

            // Update product quantity
            DB::table('products')
                ->where('id', $order->product_id)
                ->update(['qty' => DB::raw('qty - ' . $order->quantity)]);
        }

        // Delete invoice orders after moving to sales
        DB::table('invoice_orders')
            ->where('invoice', $invoiceNumber)
            ->delete();

        return back()->with('success', 'Payment confirmed! Invoice items moved to sales.');
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
