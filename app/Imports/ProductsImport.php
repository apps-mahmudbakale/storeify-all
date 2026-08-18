<?php

namespace App\Imports;

use App\Models\Product;
use App\Settings\StoreSettings;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ProductsImport implements ToCollection,  WithHeadingRow
{
    /**
     * List of MySQL reserved words to avoid in product names
     */
    private static $reservedWords = [
        'DOUBLE', 'FLOAT', 'INT', 'INTEGER', 'BIGINT', 'SMALLINT', 'TINYINT',
        'DECIMAL', 'NUMERIC', 'CHAR', 'VARCHAR', 'TEXT', 'BLOB', 'BOOLEAN',
        'DATE', 'TIME', 'DATETIME', 'TIMESTAMP', 'YEAR', 'SELECT', 'INSERT',
        'UPDATE', 'DELETE', 'CREATE', 'DROP', 'ALTER', 'TABLE', 'DATABASE',
        'FROM', 'WHERE', 'AND', 'OR', 'NOT', 'NULL', 'TRUE', 'FALSE'
    ];

    public function collection(Collection $rows)
    {
        $errors = [];
        $imported = 0;
        
        foreach ($rows as $index => $row) {
            try {
                // Skip rows with empty essential fields
                if (empty($row['product']) || empty($row['cost']) || empty($row['quantity'])) {
                    continue;
                }

                $productName = trim($row['product']);
                
                // Validate product name
                $validation = $this->validateProductName($productName);
                if (!$validation['valid']) {
                    $errors[] = "Row " . ($index + 2) . ": " . $validation['error'];
                    continue;
                }
                
                // Sanitize product name
                $productName = $this->sanitizeProductName($productName);
                
                // Validate and cast prices
                try {
                    $buyingPrice = floatval($row['cost']);
                    if ($buyingPrice <= 0) {
                        throw new \Exception("Buying price must be greater than 0");
                    }
                    
                    $sellingPrice = isset($row['selling_price']) && !empty($row['selling_price'])
                        ? floatval($row['selling_price'])
                        : $buyingPrice * app(StoreSettings::class)->sell_margin;
                    
                    if ($sellingPrice <= 0) {
                        throw new \Exception("Selling price must be greater than 0");
                    }
                } catch (\Exception $e) {
                    $errors[] = "Row " . ($index + 2) . ": Invalid price - " . $e->getMessage();
                    continue;
                }
                
                // Validate quantity
                $quantity = intval($row['quantity']);
                if ($quantity < 0) {
                    $errors[] = "Row " . ($index + 2) . ": Quantity cannot be negative";
                    continue;
                }

                // Create or update product
                Product::updateOrCreate(
                    ['name' => $productName],
                    [
                        'buying_price' => round($buyingPrice, 2),
                        'selling_price' => round($sellingPrice, 2),
                        'expiry_date' => $row['expiry'] ?? null,
                        'product_category' => $row['category'] ?? null,
                    ]
                );

                if ($quantity > 0) {
                    Product::where('name', $productName)->increment('qty', $quantity);
                }
                
                $imported++;
            } catch (\Exception $e) {
                $errors[] = "Row " . ($index + 2) . ": " . $e->getMessage();
            }
        }
        
        // Store results in session for display
        if (count($errors) > 0) {
            session()->flash('import_errors', $errors);
        }
        session()->flash('import_success', "$imported products imported successfully");
    }

    /**
     * Validate product name for SQL reserved words and special characters
     */
    private function validateProductName($name)
    {
        // Check length
        if (strlen($name) < 2 || strlen($name) > 255) {
            return [
                'valid' => false,
                'error' => 'Product name must be between 2 and 255 characters'
            ];
        }

        // Check for SQL reserved words (case-insensitive)
        $upperName = strtoupper($name);
        foreach (self::$reservedWords as $reserved) {
            if ($upperName === $reserved || strpos($upperName, ' ' . $reserved . ' ') !== false) {
                return [
                    'valid' => false,
                    'error' => "Product name contains SQL reserved word: '$reserved'. Please rename the product."
                ];
            }
        }

        // Check for SQL injection patterns
        $dangerous_patterns = [
            '/(\bOR\b|\bAND\b).*(/i',
            '/(DROP|DELETE|INSERT|UPDATE|CREATE|ALTER)\s+(TABLE|DATABASE)/i',
            '/;.*--.*/i',
            '/\/\*.*\*\//i',
        ];
        
        foreach ($dangerous_patterns as $pattern) {
            if (preg_match($pattern, $name)) {
                return [
                    'valid' => false,
                    'error' => 'Product name contains invalid characters or SQL patterns'
                ];
            }
        }

        return ['valid' => true];
    }

    /**
     * Sanitize product name to prevent issues
     */
    private function sanitizeProductName($name)
    {
        // Trim whitespace
        $name = trim($name);
        
        // Remove leading/trailing quotes if present
        $name = trim($name, '\'"');
        
        // Normalize whitespace (replace multiple spaces with single)
        $name = preg_replace('/\s+/', ' ', $name);
        
        return $name;
    }
}
