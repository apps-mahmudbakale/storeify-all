<?php

namespace App\Http\Livewire;

use App\Models\Product;
use Livewire\Component;
use Illuminate\Support\Facades\DB;

class Products extends Base
{
    public $sortBy = 'products.name';
    public $perPage ='100';
    public function render()
    {
        if ($this->search) {
            $products = Product::query()
                ->where('name', 'like', '%' . $this->search . '%')
                ->Orwhere('buying_price', 'like', '%' . $this->search . '%')
                ->Orwhere('product_category', 'like', '%' . $this->search . '%')
                ->Orwhere('qty', 'like', '%' . $this->search . '%')
                ->Orwhere('expiry_date', 'like', '%' . $this->search . '%')
                ->paginate(100);

            return view(
                'livewire.products',
                ['products' => $products]
            );
        } else {
            $products = Product::query()
                ->orderBy($this->sortBy, $this->sortDirection)
                ->paginate($this->perPage);
            return view(
                'livewire.products',
                ['products' => $products]
            );
        }
    }
}
