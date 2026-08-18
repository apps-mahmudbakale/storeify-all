<?php

namespace App\Exports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ProductsExport implements FromCollection, WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */

    public function headings(): array
    {
        return [
            'Product',
            'Cost Price',
            'Category',
            'Selling Price',
            'Quantity',
            'Expiry Date'
        ];
    }
    public function collection()
    {
        return Product::query()->get(['name', 'buying_price', 'product_category' ,'selling_price', 'qty', 'expiry_date']);
    }
}
