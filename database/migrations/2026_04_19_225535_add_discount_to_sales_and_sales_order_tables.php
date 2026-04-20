<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        DB::statement('ALTER TABLE sales_order ADD COLUMN discount DECIMAL(10,2) NOT NULL DEFAULT 0 AFTER amount');
        DB::statement('ALTER TABLE sales ADD COLUMN discount DECIMAL(10,2) NOT NULL DEFAULT 0 AFTER amount');
    }

    public function down()
    {
        DB::statement('ALTER TABLE sales_order DROP COLUMN discount');
        DB::statement('ALTER TABLE sales DROP COLUMN discount');
    }
};
