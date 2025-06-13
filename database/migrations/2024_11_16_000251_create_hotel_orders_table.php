<?php

use App\Helpers\StatusHelper;
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
        Schema::create('hotel_orders', function (Blueprint $table) {
            $table->id();
            $table->string('hotel_id')->unique();
            $table->json('hotel_offers');
            $table->json('guest');
            $table->string('payment_status')->default('PENDING'); // PENDING, PAID, FAILED
            $table->enum('order_status', StatusHelper::ORDERSTATUSES)->default('PROCESSING');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hotel_orders');
    }
};
