<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->boolean('booking_in_progress')->default(false)->after('payment_processed_at');
            $table->timestamp('booking_processing_started_at')->nullable()->after('booking_in_progress');
            $table->timestamp('booking_completed_at')->nullable()->after('booking_processing_started_at');
        });
    }

    public function down(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->dropColumn(['booking_in_progress', 'booking_processing_started_at', 'booking_completed_at']);
        });
    }
};
