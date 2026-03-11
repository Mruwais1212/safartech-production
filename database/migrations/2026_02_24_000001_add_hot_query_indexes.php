<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! $this->indexExists('reservations', 'reservations_payment_id_idx')) {
            Schema::table('reservations', function (Blueprint $table) {
                $table->index('payment_id', 'reservations_payment_id_idx');
            });
        }

        if (! $this->indexExists('reservations', 'reservations_user_id_idx')) {
            Schema::table('reservations', function (Blueprint $table) {
                $table->index('user_id', 'reservations_user_id_idx');
            });
        }

        if (! $this->indexExists('reservations', 'reservations_status_idx')) {
            Schema::table('reservations', function (Blueprint $table) {
                $table->index('status', 'reservations_status_idx');
            });
        }

        if (! $this->indexExists('reservations', 'reservations_payment_method_idx')) {
            Schema::table('reservations', function (Blueprint $table) {
                $table->index('payment_method', 'reservations_payment_method_idx');
            });
        }

        if (! $this->indexExists('reservation_flights', 'reservation_flights_pnr_idx')) {
            Schema::table('reservation_flights', function (Blueprint $table) {
                $table->index('pnr', 'reservation_flights_pnr_idx');
            });
        }

        if (! $this->indexExists('reservation_hotels', 'reservation_hotels_booking_code_idx')) {
            Schema::table('reservation_hotels', function (Blueprint $table) {
                $table->index('booking_code', 'reservation_hotels_booking_code_idx');
            });
        }
    }

    public function down(): void
    {
        if ($this->indexExists('reservations', 'reservations_payment_id_idx')) {
            Schema::table('reservations', function (Blueprint $table) {
                $table->dropIndex('reservations_payment_id_idx');
            });
        }

        if ($this->indexExists('reservations', 'reservations_user_id_idx')) {
            Schema::table('reservations', function (Blueprint $table) {
                $table->dropIndex('reservations_user_id_idx');
            });
        }

        if ($this->indexExists('reservations', 'reservations_status_idx')) {
            Schema::table('reservations', function (Blueprint $table) {
                $table->dropIndex('reservations_status_idx');
            });
        }

        if ($this->indexExists('reservations', 'reservations_payment_method_idx')) {
            Schema::table('reservations', function (Blueprint $table) {
                $table->dropIndex('reservations_payment_method_idx');
            });
        }

        if ($this->indexExists('reservation_flights', 'reservation_flights_pnr_idx')) {
            Schema::table('reservation_flights', function (Blueprint $table) {
                $table->dropIndex('reservation_flights_pnr_idx');
            });
        }

        if ($this->indexExists('reservation_hotels', 'reservation_hotels_booking_code_idx')) {
            Schema::table('reservation_hotels', function (Blueprint $table) {
                $table->dropIndex('reservation_hotels_booking_code_idx');
            });
        }
    }

    private function indexExists(string $table, string $index): bool
    {
        $driver = DB::getDriverName();

        if (in_array($driver, ['mysql', 'mariadb'], true)) {
            $result = DB::select('SHOW INDEX FROM `'.$table.'` WHERE Key_name = ?', [$index]);

            return ! empty($result);
        }

        if ($driver === 'pgsql') {
            $result = DB::select(
                'SELECT 1 FROM pg_indexes WHERE schemaname = current_schema() AND tablename = ? AND indexname = ? LIMIT 1',
                [$table, $index]
            );

            return ! empty($result);
        }

        return false;
    }
};
