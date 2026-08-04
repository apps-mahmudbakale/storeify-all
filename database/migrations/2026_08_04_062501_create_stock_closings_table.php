<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stock_closings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->integer('opening_qty'); // Qty at start of period
            $table->integer('closing_qty'); // Qty at end of period
            $table->integer('qty_dispensed'); // Total qty sold/dispensed
            $table->integer('expected_closing'); // What we expected (opening - dispensed)
            $table->integer('variance'); // Difference (closing - expected)
            $table->date('period_start');
            $table->date('period_end');
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->index('product_id');
            $table->index('user_id');
            $table->index('period_end');
            $table->unique(['product_id', 'period_end'], 'unique_product_period');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('stock_closings');
    }
};
