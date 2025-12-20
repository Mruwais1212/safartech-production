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
            $table->date('date_from')->nullable();
            $table->date('date_to')->nullable();
            $table->foreignId('country_id')->nullable();
            $table->foreignId('city_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reservation_hotels', function (Blueprint $table) {
            //
        });
    }
};
