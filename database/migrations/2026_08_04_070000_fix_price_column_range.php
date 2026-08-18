<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // This migration ensures selling_price and buying_price can handle large values
        // Change from potentially problematic types to DECIMAL for better precision
        Schema::table('products', function (Blueprint $table) {
            // Use DECIMAL for currency values - more appropriate than BIGINT
            $table->decimal('selling_price', 15, 2)->change();
            $table->decimal('buying_price', 15, 2)->change();
        });
    }

    public function down()
    {
        //
    }
};
