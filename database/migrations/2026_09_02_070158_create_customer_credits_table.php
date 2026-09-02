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
        Schema::create('customer_credits', function (Blueprint $table) {
            $table->id();
            $table->string('customer_name')->nullable();
            $table->string('customer_phone')->nullable();
            $table->string('invoice')->nullable();
            $table->decimal('amount', 15, 2);
            $table->string('reason')->default('Change not available'); // e.g., "Change not available", "To be deducted from next purchase"
            $table->string('status')->default('pending'); // pending, partially_cleared, fully_cleared
            $table->decimal('amount_cleared', 15, 2)->default(0);
            $table->date('due_date')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Staff who recorded it
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('customer_credits');
    }
};
