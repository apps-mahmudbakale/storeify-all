<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Check if the bigint columns exist (from failed migration)
        $columns = DB::select("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'products'");
        $columnNames = array_column($columns, 'COLUMN_NAME');
        
        $hasBigintSelling = in_array('selling_price_bigint', $columnNames);
        $hasBigintBuying = in_array('buying_price_bigint', $columnNames);
        
        if ($hasBigintSelling && $hasBigintBuying) {
            // Rename the bigint columns back to original names
            Schema::table('products', function (Blueprint $table) {
                $table->renameColumn('selling_price_bigint', 'selling_price');
                $table->renameColumn('buying_price_bigint', 'buying_price');
            });
        } elseif (!in_array('selling_price', $columnNames) && !in_array('buying_price', $columnNames)) {
            // Both columns are missing, recreate them
            Schema::table('products', function (Blueprint $table) {
                $table->unsignedBigInteger('selling_price')->default(0);
                $table->unsignedBigInteger('buying_price')->default(0);
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
};
