<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update existing reservation hotels with phone numbers from TBOHotel table.
        // Uses a correlated subquery instead of UPDATE...JOIN so it works on both
        // MySQL and SQLite (the test driver).
        DB::statement("
            UPDATE reservation_hotels
            SET phone = (
                SELECT t_b_o_hotels.phone
                FROM t_b_o_hotels
                WHERE t_b_o_hotels.code = reservation_hotels.hotel_id
                  AND t_b_o_hotels.phone IS NOT NULL
                LIMIT 1
            )
            WHERE reservation_hotels.phone IS NULL
              AND EXISTS (
                SELECT 1 FROM t_b_o_hotels
                WHERE t_b_o_hotels.code = reservation_hotels.hotel_id
                  AND t_b_o_hotels.phone IS NOT NULL
              )
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Set all phone numbers back to null for reservation hotels
        DB::table('reservation_hotels')->update(['phone' => null]);
    }
};
