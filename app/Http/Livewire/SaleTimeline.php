<?php

namespace App\Http\Livewire;

use App\Models\Sale;
use App\Models\SalePayment;
use Livewire\Component;
use Illuminate\Support\Facades\DB;

class SaleTimeline extends Component
{
    public $saleId;
    public $amount;
    public $notes;
    public $payment_date;

    protected $rules = [
        'amount' => 'required|numeric|min:1',
        'payment_date' => 'required|date',
        'notes' => 'nullable|string|max:255',
    ];

    public function mount($saleId)
    {
        $this->saleId = $saleId;
        $this->payment_date = date('Y-m-d');
    }

    public function addPayment()
    {
        $this->validate();

        DB::transaction(function () {
            $sale = Sale::findOrFail($this->saleId);

            SalePayment::create([
                'sale_id' => $this->saleId,
                'amount' => $this->amount,
                'payment_date' => $this->payment_date,
                'notes' => $this->notes,
            ]);

            $sale->increment('deposit', $this->amount);
            $sale->decrement('balance_remaining', $this->amount);
        });

        $this->reset(['amount', 'notes']);
        $this->payment_date = date('Y-m-d');
        session()->flash('success', 'Payment added successfully.');
    }

    public function render()
    {
        $sale = Sale::with(['product', 'payments', 'user'])->findOrFail($this->saleId);
        
        // Re-join for car details if needed since Sale model uses Product
        $saleDetails = DB::table('sales')
            ->select('sales.*', 'products.make as car_make', 'products.bodyType as car_body', 'seller.name as owner_name')
            ->leftJoin('cars as products', 'products.id', '=', 'sales.product_id')
            ->leftJoin('users as seller', 'seller.id', '=', 'products.user_id')
            ->where('sales.id', $this->saleId)
            ->first();

        return view('livewire.sale-timeline', [
            'sale' => $sale,
            'saleDetails' => $saleDetails,
            'payments' => $sale->payments()->orderBy('payment_date', 'desc')->get()
        ])->extends('layouts.app')->section('content');
    }
}
