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
        $query = DB::table('sales')
            ->select(
                'sales.*',
                'products.make as car_make',
                'products.bodyType as car_body',
                'seller.name as owner_name',
                'users.name as sold_by'
            )
            ->leftJoin('cars as products', 'products.id', '=', 'sales.product_id')
            ->leftJoin('users as seller', 'seller.id', '=', 'products.user_id')
            ->join('users', 'users.id', '=', 'sales.user_id');

        if (!auth()->user()->hasRole('admin|store')) {
            $query->where('sales.user_id', auth()->user()->id);
        }

        if ($this->search) {
            $query->where(function($q) {
                $q->where('products.make', 'like', '%' . $this->search . '%')
                  ->orWhere('sales.buyer_name', 'like', '%' . $this->search . '%')
                  ->orWhere('sales.invoice', 'like', '%' . $this->search . '%');
            });
        }

        $sales = $query->orderBy($this->sortBy == 'products.name' ? 'products.make' : $this->sortBy, $this->sortDirection)
            ->paginate($this->perPage);

        return view('livewire.sales', ['sales' => $sales]);
    }
}
