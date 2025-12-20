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
        Schema::table('reservation_flights', function (Blueprint $table) {
            $table->boolean('is_price_changed')->default(false)->after('fare_quote_json');
            $table->string('result_fare_type')->nullable()->after('is_price_changed');
            $table->boolean('is_pan_required_at_book')->default(false)->after('result_fare_type');
            $table->boolean('is_pan_required_at_ticket')->default(false)->after('is_pan_required_at_book');
            $table->boolean('is_passport_required_at_book')->default(false)->after('is_pan_required_at_ticket');
            $table->boolean('is_passport_required_at_ticket')->default(true)->after('is_passport_required_at_book');
            $table->text('airline_remark')->nullable()->after('is_passport_required_at_ticket');
            $table->boolean('is_bookable_if_seat_not_available')->default(false)->after('airline_remark');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reservation_flights', function (Blueprint $table) {
            $table->dropColumn([
                'is_price_changed',
                'result_fare_type',
                'is_pan_required_at_book',
                'is_pan_required_at_ticket',
                'is_passport_required_at_book',
                'is_passport_required_at_ticket',
                'airline_remark',
                'is_bookable_if_seat_not_available'
            ]);
        });
    }
};
