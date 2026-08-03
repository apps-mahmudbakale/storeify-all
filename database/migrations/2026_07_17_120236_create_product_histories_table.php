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
        Schema::create('product_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('type'); // 'sale', 'restock', 'adjustment', 'damage'
            $table->integer('qty_before');
            $table->integer('qty_after');
            $table->integer('qty_changed');
            $table->string('invoice')->nullable(); // for sales
            $table->string('buyer_name')->nullable(); // for sales
            $table->string('buyer_dept')->nullable(); // for sales
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->index('product_id');
            $table->index('user_id');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_histories');
    }
};
