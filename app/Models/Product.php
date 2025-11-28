<?php

namespace App\Models;

/**
 * @deprecated Use Car model instead. This class is maintained for backward compatibility.
 */
class Product extends Car
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'cars';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'make',
        'bodyType',
        'minPrice',
        'maxPrice',
        'transmission',
        'fuelType',
        'features'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'features' => 'array',
        'minPrice' => 'float',
        'maxPrice' => 'float'
    ];
}
