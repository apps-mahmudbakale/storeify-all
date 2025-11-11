<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use App\Models\Staff;

class NewSale extends Component
{
    public function render()
    {
        $invoice = session()->get('invoice');
        $carts = DB::table('sales_order')
            ->select('sales_order.*','products.id as product_id','products.name', 'products.selling_price')
            ->join('products', 'products.id', '=', 'sales_order.product_id')
            ->where('sales_order.invoice', $invoice)
            ->where('sales_order.user_id', auth()->user()->id)
            ->get();
        $getSum = DB::table('sales_order')
            ->selectRaw('sum(amount) as total')
            ->where('invoice', $invoice)
            ->where('sales_order.user_id', auth()->user()->id)
            ->first();
    // load staff list from Staff model and departments list for dropdowns
    $staff = Staff::select('id', 'name')->orderBy('name')->get();

        // departments as a simple static list (change to DB-backed if you prefer)
        $departments = [
            'Pharmacy',
            'Billing',
            'Warehousing',
            'Laboratory',
            'Front Desk',
            'Administration'
        ];

        return view('livewire.new-sale', [
            'carts' => $carts,
            'getSum' => $getSum,
            'staff' => $staff,
            'departments' => $departments,
        ]);
    }
}
