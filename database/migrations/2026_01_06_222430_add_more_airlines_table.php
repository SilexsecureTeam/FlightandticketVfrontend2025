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
        Schema::table('airlines', function (Blueprint $table) {
            $table->double('markprice_economy')->nullable();
            $table->enum('markstatus_economy', ['markup', 'markdown'])->nullable();
            $table->double('markprice_premium_economy')->nullable();
            $table->enum('markstatus_premium_economy', ['markup', 'markdown'])->nullable();
            $table->double('markprice_business')->nullable();
            $table->enum('markstatus_business', ['markup', 'markdown'])->nullable();
            $table->double('markprice_first')->nullable();
            $table->enum('markstatus_first', ['markup', 'markdown'])->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('airlines', function (Blueprint $table) {
            $table->dropColumn(['markprice_economy', 'markstatus_economy', 'markprice_premium_economy', 'markstatus_premium_economy', 'markprice_business',
                'markstatus_business', 'markprice_first', 'markstatus_first']);
        });
    }
};
