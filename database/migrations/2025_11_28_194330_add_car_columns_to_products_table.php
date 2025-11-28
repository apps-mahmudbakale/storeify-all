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
        Schema::table('products', function (Blueprint $table) {
            $table->string('make')->nullable()->after('id');
            $table->string('bodyType')->nullable()->after('make');
            $table->decimal('minPrice', 10, 2)->nullable()->after('bodyType');
            $table->decimal('maxPrice', 10, 2)->nullable()->after('minPrice');
            $table->string('transmission')->nullable()->after('maxPrice');
            $table->string('fuelType')->nullable()->after('transmission');
            $table->json('features')->nullable()->after('fuelType');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn([
                'make',
                'bodyType',
                'minPrice',
                'maxPrice',
                'transmission',
                'fuelType',
                'features'
            ]);
        });
    }
};
