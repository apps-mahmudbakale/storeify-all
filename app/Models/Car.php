<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Car extends Model
{
    use HasFactory;

    protected $fillable = [
        'make',
        'bodyType',
        'minPrice',
        'maxPrice',
        'transmission',
        'fuelType',
        'features',
        'image',
        'user_id'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'features' => 'array',
        // image stored as base64 string
        'image' => 'string',
        'minPrice' => 'decimal:2',
        'maxPrice' => 'decimal:2'
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
