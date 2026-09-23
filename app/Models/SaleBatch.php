<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaleBatch extends Model
{
    use HasFactory;

    protected $fillable = [
        'sale_id',
        'product_batch_id',
        'quantity',
    ];

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    public function batch()
    {
        return $this->belongsTo(ProductBatch::class, 'product_batch_id');
    }
}