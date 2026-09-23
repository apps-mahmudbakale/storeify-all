<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductBatch extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'batch_no',
        'initial_qty',
        'qty_remaining',
        'buying_price',
        'expiry_date',
        'received_at',
        'notes',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function saleAllocations()
    {
        return $this->hasMany(SaleBatch::class, 'product_batch_id');
    }
}