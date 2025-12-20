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
        Schema::table('reservation_hotels', function (Blueprint $table) {
            $table->string('booking_status')->nullable()->after('client_reference_id');
            $table->timestamp('booking_details_fetched_at')->nullable()->after('booking_status');
            $table->longText('booking_details_response')->nullable()->after('booking_details_fetched_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reservation_hotels', function (Blueprint $table) {
            $table->dropColumn(['booking_status', 'booking_details_fetched_at', 'booking_details_response']);
        });
    }
};
