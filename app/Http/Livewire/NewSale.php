<?php

namespace App\Http\Livewire;

use App\Models\Staff;
use Livewire\Component;
use App\Models\Department;
use Illuminate\Support\Facades\DB;

class NewSale extends Component
{
    public function render()
    {
        $invoice = session()->get('invoice');
        $carts = DB::table('sales_order')
            ->select('sales_order.*','cars.id as product_id','cars.make', 'cars.bodyType')
            ->join('cars', 'cars.id', '=', 'sales_order.product_id')
            ->where('sales_order.invoice', $invoice)
            ->where('sales_order.user_id', auth()->user()->id)
            ->get();
        $getSum = DB::table('sales_order')
            ->selectRaw('sum(amount) as total')
            ->where('invoice', $invoice)
            ->where('sales_order.user_id', auth()->user()->id)
            ->first();

        return view('livewire.new-sale', [
            'carts' => $carts,
            'getSum' => $getSum,
        ]);
    }
}
