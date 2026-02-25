<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasTable('transaction_cards')) {
            return;
        }

        $hasNumber = Schema::hasColumn('transaction_cards', 'number');
        $hasName = Schema::hasColumn('transaction_cards', 'name');

        if ($hasNumber || $hasName) {
            $query = DB::table('transaction_cards');

            if ($hasNumber) {
                $query->update(['number' => null]);
            }

            if ($hasName) {
                DB::table('transaction_cards')->update(['name' => null]);
            }
        }

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Intentionally no-op: do not restore historical PII.
    }
};
