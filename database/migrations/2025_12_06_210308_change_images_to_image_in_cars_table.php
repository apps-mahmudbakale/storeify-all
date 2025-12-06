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
        Schema::table('cars', function (Blueprint $table) {
            $table->dropColumn('images');
            $table->text('image')->nullable()->after('features');
        });
    }

    public function down()
    {
        Schema::table('cars', function (Blueprint $table) {
            $table->dropColumn('image');
            $table->json('images')->nullable()->after('features');
        });
    }
};
