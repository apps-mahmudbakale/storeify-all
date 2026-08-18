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
        $skipped = 0;
        
        \Log::info('Product Import Started', ['total_rows' => $rows->count()]);
        
        foreach ($rows as $index => $row) {
            try {
                // Skip completely empty rows
                if (empty($row['product']) && empty($row['cost']) && empty($row['quantity'])) {
                    $skipped++;
                    continue;
                }

                // Check for empty essential fields
                if (empty($row['product'])) {
                    $errors[] = "Row " . ($index + 2) . ": Product name is required";
                    continue;
                }
                if (empty($row['cost'])) {
                    $errors[] = "Row " . ($index + 2) . ": Cost is required";
                    continue;
                }
                if (empty($row['quantity'])) {
                    $errors[] = "Row " . ($index + 2) . ": Quantity is required";
                    continue;
                }

                $productName = trim($row['product']);
                
                \Log::debug('Processing product', [
                    'row' => $index + 2,
                    'product_name' => $productName,
                    'cost' => $row['cost'],
                    'quantity' => $row['quantity']
                ]);
                
                // Validate product name
                $validation = $this->validateProductName($productName);
                if (!$validation['valid']) {
                    $errors[] = "Row " . ($index + 2) . ": " . $validation['error'];
                    \Log::warning('Product validation failed', [
                        'row' => $index + 2,
                        'product' => $productName,
                        'error' => $validation['error']
                    ]);
                    continue;
                }
                
                // Sanitize product name
                $productName = $this->sanitizeProductName($productName);
                
                // Validate and cast prices
                try {
                    $buyingPrice = floatval($row['cost']);
                    if ($buyingPrice < 0) {
                        throw new \Exception("Buying price cannot be negative (got: " . $row['cost'] . ")");
                    }
                    if ($buyingPrice == 0) {
                        throw new \Exception("Buying price must be greater than 0");
                    }
                    
                    $sellingPrice = isset($row['selling_price']) && !empty($row['selling_price'])
                        ? floatval($row['selling_price'])
                        : $buyingPrice * app(StoreSettings::class)->sell_margin;
                    
                    if ($sellingPrice < 0) {
                        throw new \Exception("Selling price cannot be negative");
                    }
                    if ($sellingPrice == 0) {
                        throw new \Exception("Selling price must be greater than 0");
                    }
                } catch (\Exception $e) {
                    $errors[] = "Row " . ($index + 2) . ": Invalid price - " . $e->getMessage();
                    \Log::warning('Price validation failed', [
                        'row' => $index + 2,
                        'error' => $e->getMessage()
                    ]);
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
                
                \Log::info('Product imported successfully', [
                    'row' => $index + 2,
                    'product' => $productName,
                    'buying_price' => round($buyingPrice, 2),
                    'selling_price' => round($sellingPrice, 2),
                    'quantity' => $quantity
                ]);
                
                $imported++;
            } catch (\Exception $e) {
                $errors[] = "Row " . ($index + 2) . ": " . $e->getMessage();
                \Log::error('Product import exception', [
                    'row' => $index + 2,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
            }
        }
        
        \Log::info('Product Import Completed', [
            'imported' => $imported,
            'skipped' => $skipped,
            'errors' => count($errors)
        ]);
        
        // Store results in session for display
        if (count($errors) > 0) {
            session()->flash('import_errors', $errors);
        }
        
        if ($imported > 0) {
            session()->flash('import_success', "$imported products imported successfully");
        } elseif (count($errors) == 0 && $skipped > 0) {
            session()->flash('import_info', "$skipped rows were empty and skipped");
        }
    }

    /**
     * Validate product name for SQL reserved words and special characters
     */
    private function validateProductName($name)
    {
        // Check length
        if (strlen($name) < 2) {
            return [
                'valid' => false,
                'error' => 'Product name must be at least 2 characters (got: ' . strlen($name) . ')'
            ];
        }
        
        if (strlen($name) > 255) {
            return [
                'valid' => false,
                'error' => 'Product name must not exceed 255 characters (got: ' . strlen($name) . ')'
            ];
        }

        // Check for SQL reserved words ONLY if they are standalone (not part of a longer word)
        $upperName = strtoupper($name);
        $words = preg_split('/[\s\-,()]+/', $upperName);
        
        foreach ($words as $word) {
            if (in_array($word, self::$reservedWords) && strlen($word) > 1) {
                // Only reject if it's truly a reserved word, not part of another word
                if (preg_match('/\b' . $word . '\b/i', $name)) {
                    return [
                        'valid' => false,
                        'error' => "Product name contains SQL reserved word: '$word'. Please rename the product."
                    ];
                }
            }
        }

        // Check for SQL injection patterns
        $dangerous_patterns = [
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
