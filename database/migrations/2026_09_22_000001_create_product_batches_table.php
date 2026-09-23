<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('product_batches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->string('batch_no')->index();
            $table->integer('initial_qty');
            $table->integer('qty_remaining');
            $table->decimal('buying_price', 15, 2)->nullable();
            $table->date('expiry_date')->nullable();
            $table->date('received_at');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['product_id', 'received_at']);
            $table->index('created_at');
        });

        // Backfill an opening batch for every existing product that has stock.
        // This gives FIFO a starting point so existing inventory is consumed
        // oldest-first just like newly received batches.
        $rows = DB::select(
            "SELECT id, buying_price, expiry_date, qty, created_at
             FROM products
             WHERE qty > 0"
        );

        foreach ($rows as $row) {
            $receivedAt = $row->created_at ?? now();
            DB::table('product_batches')->insert([
                'product_id' => $row->id,
                'batch_no' => 'OPENING-' . $row->id,
                'initial_qty' => $row->qty,
                'qty_remaining' => $row->qty,
                'buying_price' => $row->buying_price,
                'expiry_date' => $row->expiry_date,
                'received_at' => substr((string) $receivedAt, 0, 10),
                'notes' => 'Opening stock from existing inventory',
                'created_at' => $receivedAt,
                'updated_at' => $receivedAt,
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_batches');
    }
};