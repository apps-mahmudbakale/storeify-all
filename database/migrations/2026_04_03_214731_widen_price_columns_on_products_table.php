<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
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
        DB::statement('ALTER TABLE products MODIFY buying_price DECIMAL(15,2) NOT NULL');
        DB::statement('ALTER TABLE products MODIFY selling_price DECIMAL(15,2) NOT NULL');
    }

    public function down()
    {
        DB::statement('ALTER TABLE products MODIFY buying_price DOUBLE(8,2) NOT NULL');
        DB::statement('ALTER TABLE products MODIFY selling_price DOUBLE(8,2) NOT NULL');
    }
};
