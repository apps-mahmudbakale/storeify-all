<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('sales_order', function (Blueprint $table) {
            if (!Schema::hasColumn('sales_order', 'product_batch_id')) {
                $table->unsignedBigInteger('product_batch_id')->nullable()->after('product_id');
                $table->index('product_batch_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales_order', function (Blueprint $table) {
            if (Schema::hasColumn('sales_order', 'product_batch_id')) {
                $table->dropColumn('product_batch_id');
            }
        });
    }
};