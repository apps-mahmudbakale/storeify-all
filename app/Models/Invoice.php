<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
            'invoice',
    ];

    public function sales()
    {
        return $this->hasMany(Sale::class, 'invoice', 'invoice');
    }

    public function invoiceOrders()
    {
        return $this->hasMany(InvoiceOrder::class, 'invoice', 'invoice');
    }
}
