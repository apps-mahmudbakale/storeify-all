<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductBatch;
use App\Models\ProductHistory;
use App\Models\Sale;
use App\Models\SaleBatch;
use Illuminate\Support\Facades\DB;

/**
 * FIFO stock management.
 *
 * Stock is tracked in batches. When stock is sold (or reduced), the
 * quantity is consumed from the oldest batches first. Returns are credited
 * back to the batches they were originally fulfilled from.
 */
class FifoBatchService
{
    /**
     * Allocate a quantity of a product from its batches, oldest first.
     *
     * @return array<int, array{batch: ProductBatch, qty: int}>
     */
    public static function allocate(Product $product, int $qty): array
    {
        $allocations = [];
        $remaining = $qty;

        $batches = ProductBatch::query()
            ->where('product_id', $product->id)
            ->where('qty_remaining', '>', 0)
            ->orderBy('received_at')
            ->orderBy('id')
            ->get();

        foreach ($batches as $batch) {
            if ($remaining <= 0) {
                break;
            }

            $take = min((int) $batch->qty_remaining, $remaining);
            $allocations[] = ['batch' => $batch, 'qty' => $take];
            $remaining -= $take;
        }

        return $allocations;
    }

    /**
     * Dispense stock from a specific batch for a sale, recording the allocation.
     */
    public static function dispense(ProductBatch $batch, int $qty, Sale $sale): void
    {
        if ($qty <= 0) {
            return;
        }

        DB::table('product_batches')
            ->where('id', $batch->id)
            ->decrement('qty_remaining', $qty);

        SaleBatch::create([
            'sale_id' => $sale->id,
            'product_batch_id' => $batch->id,
            'quantity' => $qty,
        ]);
    }

    /**
     * Add more stock into an existing batch and record a restock history entry.
     */
    public static function stockBatch(ProductBatch $batch, int $qty, ?int $qtyBefore = null): ProductBatch
    {
        if ($qty <= 0) {
            return $batch;
        }

        $product = $batch->product;
        $qtyBefore = $qtyBefore ?? (int) $product->qty;

        $product->increment('qty', $qty);
        $batch->increment('initial_qty', $qty);
        $batch->increment('qty_remaining', $qty);

        ProductHistory::create([
            'product_id' => $product->id,
            'user_id' => self::resolveUserId(),
            'type' => 'restock',
            'qty_before' => $qtyBefore,
            'qty_after' => $qtyBefore + $qty,
            'qty_changed' => $qty,
            'notes' => 'Stocked batch ' . $batch->batch_no,
        ]);

        return $batch->fresh();
    }

    /**
     * Reconcile a product's stock to a counted quantity (stock take).
     *
     * Any surplus lands in a new "Stock take" batch so it is still FIFO-tracked;
     * any shortfall is consumed from the oldest batches first. Keeps products.qty
     * and the sum of batch quantities in agreement.
     *
     * @return int absolute size of the correction applied
     */
    public static function reconcile(Product $product, int $countedQty): int
    {
        $countedQty = max(0, $countedQty);

        // Read the current figure from the database, not the in-memory model:
        // callers may hold a product instance loaded before other changes.
        $product = Product::findOrFail($product->id);

        $qtyBefore = (int) $product->qty;
        $delta = $countedQty - $qtyBefore;

        if ($delta === 0) {
            return 0;
        }

        DB::transaction(function () use ($product, $countedQty, $delta, $qtyBefore) {
            DB::table('products')
                ->where('id', $product->id)
                ->update(['qty' => $countedQty]);

            if ($delta > 0) {
                $batch = ProductBatch::query()
                    ->where('product_id', $product->id)
                    ->orderByDesc('received_at')
                    ->orderByDesc('id')
                    ->first();

                // Prefer topping up the newest batch so the count stays on stock
                // already received rather than inventing a new expiry window.
                if ($batch) {
                    DB::table('product_batches')
                        ->where('id', $batch->id)
                        ->increment('qty_remaining', $delta);
                    DB::table('product_batches')
                        ->where('id', $batch->id)
                        ->increment('initial_qty', $delta);
                } else {
                    ProductBatch::create([
                        'product_id' => $product->id,
                        'batch_no' => 'TAKE-' . now()->format('Ymd-His'),
                        'initial_qty' => $delta,
                        'qty_remaining' => $delta,
                        'buying_price' => $product->buying_price,
                        'expiry_date' => $product->expiry_date,
                        'received_at' => now()->format('Y-m-d'),
                        'notes' => 'Stock take surplus',
                    ]);
                }
            } else {
                // Surplus on hand is a write-off: pull it out of the oldest batches.
                $shortfall = abs($delta);

                foreach (self::allocate($product, $shortfall) as $allocation) {
                    DB::table('product_batches')
                        ->where('id', $allocation['batch']->id)
                        ->decrement('qty_remaining', $allocation['qty']);

                    $shortfall -= $allocation['qty'];

                    if ($shortfall <= 0) {
                        break;
                    }
                }
            }

            ProductHistory::create([
                'product_id' => $product->id,
                'user_id' => self::resolveUserId(),
                'type' => 'adjustment',
                'qty_before' => $qtyBefore,
                'qty_after' => $countedQty,
                'qty_changed' => $delta,
                'notes' => 'Stock take counted ' . $countedQty,
            ]);
        });

        return abs($delta);
    }

    /**
     * Remove stock from a specific batch (manual adjustments, damage, write-offs).
     *
     * Keeps the product stock in sync with the batch and records an adjustment
     * history entry. Throws when the requested qty exceeds what the batch holds.
     *
     * @throws \InvalidArgumentException
     */
    public static function reduceBatch(ProductBatch $batch, int $qty): ProductBatch
    {
        if ($qty <= 0) {
            throw new \InvalidArgumentException('Quantity must be at least 1.');
        }

        $available = (int) $batch->fresh()->qty_remaining;

        if ($qty > $available) {
            throw new \InvalidArgumentException(
                'Batch "' . $batch->batch_no . '" only has ' . $available . ' left.'
            );
        }

        $product = $batch->product;
        $qtyBefore = (int) $product->qty;

        DB::transaction(function () use ($batch, $product, $qty, $qtyBefore) {
            DB::table('product_batches')
                ->where('id', $batch->id)
                ->decrement('qty_remaining', $qty);

            DB::table('products')
                ->where('id', $product->id)
                ->decrement('qty', $qty);

            ProductHistory::create([
                'product_id' => $product->id,
                'user_id' => self::resolveUserId(),
                'type' => 'adjustment',
                'qty_before' => $qtyBefore,
                'qty_after' => $qtyBefore - $qty,
                'qty_changed' => $qty,
                'notes' => 'Removed ' . $qty . ' from batch ' . $batch->batch_no,
            ]);
        });

        return $batch->fresh();
    }

    /**
     * Credit a returned quantity back into the batches it was sold from.
     *
     * @return int quantity actually restored to batches
     */
    public static function credit(Sale $sale, int $qty): int
    {
        if ($qty <= 0) {
            return 0;
        }

        $restored = 0;

        $rows = SaleBatch::query()
            ->where('sale_id', $sale->id)
            ->orderBy('id')
            ->get();

        foreach ($rows as $row) {
            if ($restored >= $qty) {
                break;
            }

            $take = min((int) $row->quantity, $qty - $restored);

            DB::table('product_batches')
                ->where('id', $row->product_batch_id)
                ->increment('qty_remaining', $take);

            $restored += $take;
        }

        return $restored;
    }

    /**
     * Create a new incoming stock batch and record a restock history entry.
     */
    public static function addBatch(
        Product $product,
        int $qty,
        array $attributes = [],
        ?int $qtyBefore = null,
        bool $recordHistory = true
    ): ?ProductBatch {
        if ($qty <= 0) {
            return null;
        }

        $qtyBefore = $qtyBefore ?? (int) $product->qty;

        $batch = ProductBatch::create(array_merge([
            'product_id' => $product->id,
            'batch_no' => self::generateBatchNo($product),
            'initial_qty' => $qty,
            'qty_remaining' => $qty,
            'buying_price' => $product->buying_price,
            'expiry_date' => $product->expiry_date,
            'received_at' => now()->format('Y-m-d'),
            'notes' => null,
        ], $attributes));

        if ($recordHistory) {
            ProductHistory::create([
                'product_id' => $product->id,
                'user_id' => self::resolveUserId(),
                'type' => 'restock',
                'qty_before' => $qtyBefore,
                'qty_after' => $qtyBefore + $qty,
                'qty_changed' => $qty,
                'notes' => 'New batch ' . $batch->batch_no . ' received',
            ]);
        }

        return $batch;
    }

    /**
     * Remove stock from batches FIFO without a sale (manual adjustments).
     *
     * @return int quantity removed from batches
     */
    public static function reduceStock(Product $product, int $qty, ?int $qtyBefore = null): int
    {
        if ($qty <= 0) {
            return 0;
        }

        $consumed = 0;

        foreach (self::allocate($product, $qty) as $allocation) {
            DB::table('product_batches')
                ->where('id', $allocation['batch']->id)
                ->decrement('qty_remaining', $allocation['qty']);

            $consumed += $allocation['qty'];
        }

        if ($consumed > 0) {
            $qtyAfter = (int) $product->qty;

            ProductHistory::create([
                'product_id' => $product->id,
                'user_id' => self::resolveUserId(),
                'type' => 'adjustment',
                'qty_before' => $qtyBefore ?? ($qtyAfter + $consumed),
                'qty_after' => $qtyAfter,
                'qty_changed' => $consumed,
                'notes' => 'Manual stock reduction',
            ]);
        }

        return $consumed;
    }

    /**
     * Total quantity currently held across all batches of a product.
     */
    public static function batchTotal(int $productId): int
    {
        return (int) ProductBatch::query()
            ->where('product_id', $productId)
            ->sum('qty_remaining');
    }

    private static function generateBatchNo(Product $product): string
    {
        $count = ProductBatch::query()
            ->where('product_id', $product->id)
            ->count();

        return 'B-' . $product->id . '-' . now()->format('Ymd') . '-' . str_pad((string) ($count + 1), 4, '0', STR_PAD_LEFT);
    }

    private static function resolveUserId(): int
    {
        $user = auth()->user();

        if ($user) {
            return (int) $user->id;
        }

        return (int) \App\Models\User::query()->orderBy('id')->value('id') ?? 1;
    }
}