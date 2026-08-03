<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        try {
            // This migration cleans up the database after the failed price column migration
            
            // Get the current columns
            $columns = DB::select("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'products'");
            $columnNames = collect($columns)->pluck('COLUMN_NAME')->toArray();
            
            // Drop the old _new columns if they exist (from failed migrations)
            if (in_array('selling_price_new', $columnNames)) {
                DB::statement('ALTER TABLE products DROP COLUMN selling_price_new');
            }
            
            if (in_array('buying_price_new', $columnNames)) {
                DB::statement('ALTER TABLE products DROP COLUMN buying_price_new');
            }
            
            // Ensure selling_price and buying_price exist with correct type
            if (!in_array('selling_price', $columnNames) && !in_array('selling_price_bigint', $columnNames)) {
                Schema::table('products', function (Blueprint $table) {
                    $table->unsignedBigInteger('selling_price')->default(0);
                });
            }
            
            if (!in_array('buying_price', $columnNames) && !in_array('buying_price_bigint', $columnNames)) {
                Schema::table('products', function (Blueprint $table) {
                    $table->unsignedBigInteger('buying_price')->default(0);
                });
            }
            
        } catch (\Exception $e) {
            // Silently skip if tables/columns don't exist
            \Log::debug('Migration cleanup skipped: ' . $e->getMessage());
        }
    }

    public function down()
    {
        //
    }
};
