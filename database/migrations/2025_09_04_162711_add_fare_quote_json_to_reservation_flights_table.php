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
            $table->longText('fare_quote_json')->nullable()->after('fare_rule_json');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reservation_flights', function (Blueprint $table) {
            $table->dropColumn('fare_quote_json');
        });
    }
};
