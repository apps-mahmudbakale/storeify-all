<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Car extends Model
{
    use HasFactory;

    protected $table = 'cars';

    protected $fillable = [
        'make',
        'bodyType',
        'minPrice',
        'maxPrice',
        'transmission',
        'fuelType',
        'features'
    ];

    protected $casts = [
        'features' => 'array',
        'minPrice' => 'float',
        'maxPrice' => 'float'
    ];
}
