<?php

namespace App\Http\Livewire;

use App\Models\Sale;
use Livewire\Component;
use Illuminate\Support\Facades\DB;

class Sales extends Base
{
    public $sortBy = 'products.name';
    public function render()
    {
        if(auth()->user()->hasRole('admin|store')){
            if ($this->search) {
                $sales = DB::table('sales')
                    ->select('sales.*','products.name as product','users.name as user')
                    ->join('products', 'products.id', '=', 'sales.product_id')
                    ->join('users', 'users.id', '=', 'sales.user_id')
                    ->where(function($q) {
                        $q->where('products.name', 'like', '%' . $this->search . '%')
                          ->orWhere('sales.invoice', 'like', '%' . $this->search . '%');
                    })
                    ->paginate(10);

                return view(
                    'livewire.sales',
                    ['sales' => $sales]
                );
            } else {
                $sales = DB::table('sales')
                    ->select('sales.*','products.name as product','users.name as user')
                    ->join('products', 'products.id', '=', 'sales.product_id')
                    ->join('users', 'users.id', '=', 'sales.user_id')
                    ->orderBy($this->sortBy, $this->sortDirection)
                    ->paginate($this->perPage);
                return view(
                    'livewire.sales',
                    ['sales' => $sales]
                );
            }
        }else{
            if ($this->search) {
                $sales = DB::table('sales')
                    ->select('sales.*','products.name as product','users.name as user')
                    ->join('products', 'products.id', '=', 'sales.product_id')
                    ->join('users', 'users.id', '=', 'sales.user_id')
                    ->where('sales.user_id', auth()->user()->id)
                    ->where(function($q) {
                        $q->where('products.name', 'like', '%' . $this->search . '%')
                          ->orWhere('sales.invoice', 'like', '%' . $this->search . '%');
                    })
                    ->paginate(10);
                return view(
                    'livewire.sales',
                    ['sales' => $sales]
                );
            } else {
                $sales = DB::table('sales')
                    ->select('sales.*','products.name as product','users.name as user')
                    ->join('products', 'products.id', '=', 'sales.product_id')
                    ->join('users', 'users.id', '=', 'sales.user_id')
                    ->where('sales.user_id', auth()->user()->id)
                    ->orderBy($this->sortBy, $this->sortDirection)
                    ->paginate($this->perPage);
                return view(
                    'livewire.sales',
                    ['sales' => $sales]
                );
            }
        }
    }
}
