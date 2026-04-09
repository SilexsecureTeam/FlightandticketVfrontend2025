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
        Schema::table('users', function (Blueprint $table) {
            $table->string('address')->nullable();
            $table->string('avatar')->nullable();
            $table->string('zipcode')->nullable();
            $table->string('country')->nullable();
            $table->string('state')->nullable();
            $table->string('city')->nullable();
            $table->string('city')->nullable();
            $table->enum('daily_report', ['yes', 'no'])->default('no');
            $table->enum('weekly_report', ['yes', 'no'])->default('no');
            $table->enum('monthly_report', ['yes', 'no'])->default('no');
            $table->enum('yearly_report', ['yes', 'no'])->default('no');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('address');
            $table->dropColumn('avatar');
            $table->dropColumn('zipcode');
            $table->dropColumn('country');
            $table->dropColumn('state');
            $table->dropColumn('city');
            $table->dropColumn('daily_report');
            $table->dropColumn('weekly_report');
            $table->dropColumn('monthly_report');
            $table->dropColumn('yearly_report');
        });
    }
};
