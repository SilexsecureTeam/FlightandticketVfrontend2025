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
        Schema::table('flight_searches', function (Blueprint $table) {
            if (!Schema::hasColumn('flight_searches', 'user_id')) {
                $table->integer('user_id')->nullable();
            }
            if (!Schema::hasColumn('flight_searches', 'clientRef')) {
                $table->string('clientRef')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('flight_searches', function (Blueprint $table) {
            $table->dropColumn('user_id');
            $table->dropColumn('clientRef');
        });
    }
};
