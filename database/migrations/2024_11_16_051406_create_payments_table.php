<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('flight_order_id')->nullable()->constrained('flight_orders')->onDelete('cascade');
            $table->foreignId('hotel_order_id')->nullable()->constrained('hotel_orders')->onDelete('cascade');
            $table->string('payment_method')->nullable();
            $table->decimal('amount', 10, 2);
            $table->string('channel')->nullable();
            $table->string('provider')->nullable();
            $table->string('payment_type')->nullable();
            $table->string('email');
            $table->timestamp('payment_date');
            $table->string('transaction_id')->nullable();
            $table->string('commission')->nullable();
            $table->string('reference')->unique();
            $table->string('payment_status')->default('PENDING'); // Status: 'PENDING', 'COMPLETED', 'FAILED', etc.
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
