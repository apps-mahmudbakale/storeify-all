<?php

namespace App\Imports;

use App\Models\Product;
use App\Settings\StoreSettings;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ProductsImport implements ToCollection,  WithHeadingRow
{
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            $sellingPrice = isset($row['selling_price']) && !empty($row['selling_price']) 
                ? $row['selling_price'] 
                : $row['cost'] * app(StoreSettings::class)->sell_margin;

            Product::updateOrCreate(
                ['name' => ucfirst($row['product'])],
                [
                    'buying_price' => $row['cost'],
                    'selling_price' => $sellingPrice,
                    'expiry_date' => $row['expiry'],
                ]
            );
            
            Product::where('name', ucfirst($row['product']))->increment('qty', $row['quantity']);
        }
    }
}
