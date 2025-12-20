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
        // Update existing reservation hotels with phone numbers from TBOHotel table
        DB::statement("
            UPDATE reservation_hotels rh
            JOIN t_b_o_hotels th ON rh.hotel_id = th.code
            SET rh.phone = th.phone
            WHERE rh.phone IS NULL AND th.phone IS NOT NULL
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
