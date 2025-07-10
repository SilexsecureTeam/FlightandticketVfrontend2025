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
        Schema::create('flight_searches', function (Blueprint $table) {
            $table->id();
            $table->string('search_id')->unique();
            $table->string('flight_offers_path')->nullable();
            $table->string('dictionaries_path')->nullable();
            $table->json('cheapest')->nullable();
            $table->json('meta')->nullable();
            $table->json('passengers')->nullable();
            $table->string('from_city_name')->nullable();
            $table->string('to_city_name')->nullable();
            $table->string('from_code', 3)->nullable();
            $table->string('to_code', 3)->nullable();
            $table->date('departure_date')->nullable();
            $table->date('return_date')->nullable();
            $table->string('trip_type')->nullable();
            $table->timestamps();
            // Auto-delete after 24 hours
            $table->timestamp('expires_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('flight_searches');
    }
};
