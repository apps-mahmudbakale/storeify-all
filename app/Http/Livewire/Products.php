<?php

namespace App\Http\Livewire;

use App\Models\Car;
use Livewire\Component;
use Illuminate\Support\Facades\DB;

class Products extends Base
{
    public $sortBy = 'cars.make';
    public function render()
    {
        if ($this->search) {
            $query = Car::query()->with('user');

            if (auth()->user()->hasRole('car-owner') && !auth()->user()->hasRole('admin')) {
                $query->where('user_id', auth()->id());
            }

            $products = $query->where(function ($q) {
                $q->where('make', 'like', '%' . $this->search . '%')
                ->Orwhere('bodyType', 'like', '%' . $this->search . '%')
                ->Orwhere('minPrice', 'like', '%' . $this->search . '%')
                ->Orwhere('maxPrice', 'like', '%' . $this->search . '%')
                ->Orwhere('transmission', 'like', '%' . $this->search . '%')
                ->Orwhere('fuelType', 'like', '%' . $this->search . '%');
            })->paginate(10);

            return view(
                'livewire.products',
                ['products' => $products]
            );
        } else {
            $query = Car::query()->with('user');
            
            if (auth()->user()->hasRole('car-owner') && !auth()->user()->hasRole('admin')) {
                $query->where('user_id', auth()->id());
            }

            $products = $query->orderBy($this->sortBy, $this->sortDirection)
                ->paginate($this->perPage);
            return view(
                'livewire.products',
                ['products' => $products]
            );
        }
    }
}
