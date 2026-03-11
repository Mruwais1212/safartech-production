<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update existing reservation hotels with phone numbers from TBOHotel table.
        // MySQL-style JOIN-UPDATE is not portable; use a portable subquery instead.
        if (DB::getDriverName() === 'mysql') {
            DB::statement('
                UPDATE reservation_hotels rh
                JOIN t_b_o_hotels th ON rh.hotel_id = th.code
                SET rh.phone = th.phone
                WHERE rh.phone IS NULL AND th.phone IS NOT NULL
            ');
        } else {
            // Portable equivalent for SQLite / PostgreSQL
            DB::table('reservation_hotels')
                ->whereNull('phone')
                ->whereExists(function ($query) {
                    $query->from('t_b_o_hotels')
                        ->whereColumn('t_b_o_hotels.code', 'reservation_hotels.hotel_id')
                        ->whereNotNull('t_b_o_hotels.phone');
                })
                ->update([
                    'phone' => DB::raw('(SELECT th.phone FROM t_b_o_hotels th WHERE th.code = reservation_hotels.hotel_id LIMIT 1)'),
                ]);
        }
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
