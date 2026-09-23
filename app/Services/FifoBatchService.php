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