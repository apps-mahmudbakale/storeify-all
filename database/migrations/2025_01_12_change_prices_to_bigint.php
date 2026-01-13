<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {

    public function up(): void
    {
        // 1. Add new BIGINT columns
        Schema::table('products', function (Blueprint $table) {
            $table->unsignedBigInteger('selling_price_new')->after('selling_price');
            $table->unsignedBigInteger('buying_price_new')->after('buying_price');
        });

        // 2. Copy data
        DB::statement('UPDATE products SET selling_price_new = selling_price, buying_price_new = buying_price');

        // 3. Drop old columns
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['selling_price', 'buying_price']);
        });

        // 4. Rename new columns
        Schema::table('products', function (Blueprint $table) {
            $table->renameColumn('selling_price_new', 'selling_price');
            $table->renameColumn('buying_price_new', 'buying_price');
        });
    }

    public function down(): void
    {
        // 1. Recreate INT columns
        Schema::table('products', function (Blueprint $table) {
            $table->unsignedInteger('selling_price_old')->after('selling_price');
            $table->unsignedInteger('buying_price_old')->after('buying_price');
        });

        // 2. Copy data back (⚠ possible overflow if values > INT)
        DB::statement('UPDATE products SET selling_price_old = selling_price, buying_price_old = buying_price');

        // 3. Drop BIGINT columns
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['selling_price', 'buying_price']);
        });

        // 4. Rename back
        Schema::table('products', function (Blueprint $table) {
            $table->renameColumn('selling_price_old', 'selling_price');
            $table->renameColumn('buying_price_old', 'buying_price');
        });
    }
};
