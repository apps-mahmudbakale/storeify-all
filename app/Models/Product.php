<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'product_category',
        'buying_price',
        'selling_price',
        'qty',
        'min_qty',
        'unit',
        'expiry_date'
    ];

}
