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
        Schema::table('reservations', function (Blueprint $table) {
            $table->string('callback_nonce', 64)->nullable()->after('payment_id');
            $table->timestamp('payment_processed_at')->nullable()->after('paid_at');
            $table->unsignedInteger('reconcile_attempt_count')->default(0)->after('booking_error');
            $table->timestamp('last_reconciled_at')->nullable()->after('reconcile_attempt_count');
            $table->string('reconciliation_status', 40)->nullable()->after('last_reconciled_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->dropColumn([
                'callback_nonce',
                'payment_processed_at',
                'reconcile_attempt_count',
                'last_reconciled_at',
                'reconciliation_status',
            ]);
        });
    }
};
