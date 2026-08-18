<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductUnit extends Model
{
    use HasFactory;

    protected $table = 'product_units';

    protected $fillable = [
        'code',
        'name',
        'description',
        'category',
        'sort_order',
    ];

    /**
     * Get units by category
     */
    public static function getByCategory($category)
    {
        return self::where('category', $category)
            ->orderBy('sort_order')
            ->get();
    }

    /**
     * Get all units grouped by category
     */
    public static function getGroupedByCategory()
    {
        return self::orderBy('category')
            ->orderBy('sort_order')
            ->get()
            ->groupBy('category');
    }

    /**
     * Get restaurant units only
     */
    public static function getRestaurantUnits()
    {
        return self::getByCategory('restaurant');
    }

    /**
     * Get medical units only
     */
    public static function getMedicalUnits()
    {
        return self::getByCategory('medical');
    }

    /**
     * Get general units only
     */
    public static function getGeneralUnits()
    {
        return self::getByCategory('general');
    }
}
