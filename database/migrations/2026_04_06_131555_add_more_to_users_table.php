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
            if (!Schema::hasColumn('users', 'address')) {
                $table->string('address')->nullable();
            }
            if (!Schema::hasColumn('users', 'avatar')) {
                $table->string('avatar')->nullable();
            }
            if (!Schema::hasColumn('users', 'zipcode')) {
                $table->string('zipcode')->nullable();
            }
            if (!Schema::hasColumn('users', 'country')) {
                $table->string('country')->nullable();
            }
            if (!Schema::hasColumn('users', 'state')) {
                $table->string('state')->nullable();
            }
            if (!Schema::hasColumn('users', 'city')) {
                $table->string('city')->nullable();
            }
            if (!Schema::hasColumn('users', 'daily_report')) {
                $table->enum('daily_report', ['yes', 'no'])->default('no');
            }
            if (!Schema::hasColumn('users', 'weekly_report')) {
                $table->enum('weekly_report', ['yes', 'no'])->default('no');
            }
            if (!Schema::hasColumn('users', 'monthly_report')) {
                $table->enum('monthly_report', ['yes', 'no'])->default('no');
            }
            if (!Schema::hasColumn('users', 'yearly_report')) {
                $table->enum('yearly_report', ['yes', 'no'])->default('no');
            }
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
