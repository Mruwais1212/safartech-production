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
            $table->longText('fare_rule_json')->nullable()->after('flight_json')->comment('Stores FareRule API response data');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reservation_flights', function (Blueprint $table) {
            $table->dropColumn('fare_rule_json');
        });
    }
};
