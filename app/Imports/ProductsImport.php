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
            // Skip rows with empty essential fields
            if (empty($row['product']) || empty($row['cost']) || empty($row['quantity'])) {
                continue;
            }

            $productName = trim($row['product']);
            $sellingPrice = isset($row['selling_price']) && !empty($row['selling_price'])
                ? floatval($row['selling_price'])
                : floatval($row['cost']) * app(StoreSettings::class)->sell_margin;

            // Cast prices to ensure proper decimal format
            $buyingPrice = floatval($row['cost']);
            $quantity = intval($row['quantity']);

            Product::updateOrCreate(
                ['name' => $productName],
                [
                    'buying_price' => $buyingPrice,
                    'selling_price' => $sellingPrice,
                    'expiry_date' => $row['expiry'],
                    'product_category' => $row['category'],
                ]
            );

            Product::where('name', $productName)->increment('qty', $quantity);
        }
    }
}
