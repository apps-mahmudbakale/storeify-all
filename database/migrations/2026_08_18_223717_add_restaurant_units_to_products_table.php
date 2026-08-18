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
        // Add restaurant-specific units documentation
        // This is informational - actual units are stored as strings in the unit column
        Schema::table('products', function (Blueprint $table) {
            // Unit column already exists, but we're documenting the new options
        });

        // Create a units reference table for UI dropdowns
        if (!Schema::hasTable('product_units')) {
            Schema::create('product_units', function (Blueprint $table) {
                $table->id();
                $table->string('code')->unique();
                $table->string('name');
                $table->string('description')->nullable();
                $table->string('category'); // 'medical', 'restaurant', 'general'
                $table->integer('sort_order')->default(0);
                $table->timestamps();
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
        Schema::dropIfExists('product_units');
    }
};
