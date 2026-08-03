<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use OwenIt\Auditing\Contracts\Auditable;

class Product extends Model implements Auditable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;

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

    public function histories()
    {
        return $this->hasMany(ProductHistory::class);
    }

}
