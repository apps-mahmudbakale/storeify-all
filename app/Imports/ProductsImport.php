<?php

namespace App\Imports;

use App\Models\Product;
use App\Settings\StoreSettings;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ProductsImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            // WithHeadingRow converts headers to snake_case
            // "cost price" -> "cost_price", "selling price" -> "selling_price", etc.
            $name = $row['product'] ?? $row['product_name'] ?? null;
            $cost = $row['cost'] ?? $row['cost_price'] ?? null;
            $qty  = $row['quantity'] ?? null;

            if (empty($name) || empty($cost) || !is_numeric($cost) || empty($qty)) {
                continue;
            }

            $cost = (float) $cost;
            $qty  = (int) $qty;

            $sellingPrice = $row['selling_price'] ?? null;
            // If it's an Excel formula or non-numeric, calculate from cost
            if (empty($sellingPrice) || !is_numeric($sellingPrice)) {
                $sellingPrice = $cost * app(StoreSettings::class)->sell_margin;
            }

            $expiry   = $row['expiry_date'] ?? $row['expiry'] ?? null;
            $category = $row['category'] ?? null;
            $barcode  = !empty($row['barcode']) ? (string) $row['barcode'] : null;
            $vat      = $row['vat_percentage'] ?? $row['vat'] ?? 7.50;

            // Parse expiry: all formats are MM/YY, MM/YYYY — always use last day of month
            $parsedExpiry = null;
            if (!empty($expiry)) {
                try {
                    $expiry = trim((string) $expiry);
                    $month = null;
                    $year  = null;

                    if (preg_match('/^(\d{1,2})\/(\d{2})$/', $expiry, $m)) {
                        $month = (int) $m[1];
                        $year  = '20' . $m[2];
                    } elseif (preg_match('/^(\d{1,2})\/(\d{4})$/', $expiry, $m)) {
                        $month = (int) $m[1];
                        $year  = $m[2];
                    } elseif (preg_match('/^(\d{1,2})\/(\d{1,2})\/(\d{2,4})$/', $expiry, $m)) {
                        $month = (int) $m[2];
                        $year  = strlen($m[3]) === 2 ? '20' . $m[3] : $m[3];
                    }

                    if ($month && $year && $month >= 1 && $month <= 12) {
                        $parsedExpiry = Carbon::createFromDate($year, $month, 1)->endOfMonth()->format('Y-m-d');
                    } else {
                        $parsedExpiry = Carbon::parse($expiry)->endOfMonth()->format('Y-m-d');
                    }
                } catch (\Exception $e) {
                    $parsedExpiry = null;
                }
            }

            Product::updateOrCreate(
                ['name' => trim(ucfirst(strtolower($name)))],
                [
                    'buying_price'     => $cost,
                    'selling_price'    => $sellingPrice,
                    'expiry_date'      => $parsedExpiry ?? now()->addYear()->format('Y-m-d'),
                    'product_category' => $category,
                    'vat_percentage'   => $vat,
                    'barcode'          => $barcode,
                ]
            );

            Product::where('name', trim(ucfirst(strtolower($name))))->increment('qty', (int) $qty);
        }
    }
}
