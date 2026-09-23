<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->string('source')->nullable()->after('provider'); // web | api
            $table->string('gateway_status')->nullable()->after('payment_status');
            $table->string('alatpay_order_id')->nullable()->after('transaction_id');
            $table->json('metadata')->nullable()->after('reference');
            $table->json('gateway_payload')->nullable()->after('metadata');
            $table->timestamp('status_notified_at')->nullable()->after('gateway_payload');
        });

        Schema::create('alatpay_payment_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment_id')->nullable()->constrained('payments')->nullOnDelete();
            $table->string('event_type', 50);
            $table->string('source', 50)->nullable();
            $table->string('reference', 191)->nullable()->index();
            $table->string('alatpay_order_id', 191)->nullable()->index();
            $table->string('status', 50)->nullable();
            $table->json('payload')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->boolean('signature_valid')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alatpay_payment_events');

        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn([
                'source',
                'gateway_status',
                'alatpay_order_id',
                'metadata',
                'gateway_payload',
                'status_notified_at',
            ]);
        });
    }
};
