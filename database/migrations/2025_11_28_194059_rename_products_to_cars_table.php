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
        // Rename the table
        Schema::rename('products', 'cars');
        
        // Modify the columns
        Schema::table('cars', function (Blueprint $table) {
            $table->dropColumn(['name', 'buying_price', 'selling_price', 'qty', 'expiry_date']);
            
            // Add new columns
            $table->string('make');
            $table->string('bodyType');
            $table->decimal('minPrice', 10, 2);
            $table->decimal('maxPrice', 10, 2);
            $table->string('transmission');
            $table->string('fuelType');
            $table->json('features')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Revert column changes
        Schema::table('cars', function (Blueprint $table) {
            $table->dropColumn(['make', 'bodyType', 'minPrice', 'maxPrice', 'transmission', 'fuelType', 'features']);
            
            // Add back original columns
            $table->string('name');
            $table->float('buying_price');
            $table->float('selling_price');
            $table->integer('qty')->default(0);
            $table->date('expiry_date');
        });
        
        // Rename table back to products
        Schema::rename('cars', 'products');
    }
};
