<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockClosing extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'user_id',
        'opening_qty',
        'closing_qty',
        'qty_dispensed',
        'expected_closing',
        'variance',
        'period_start',
        'period_end',
        'notes',
    ];

    protected $casts = [
        'period_start' => 'date',
        'period_end' => 'date',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function productHistories()
    {
        return $this->hasMany(ProductHistory::class)
            ->whereBetween('created_at', [$this->period_start, $this->period_end->addDay()])
            ->where('type', 'sale');
    }
}
