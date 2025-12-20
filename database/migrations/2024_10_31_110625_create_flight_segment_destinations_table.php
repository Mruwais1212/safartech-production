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
        Schema::create('flight_segment_destinations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('flight_segment_id');
            $table->string('airport_code');
            $table->string('airport_name');
            $table->string('city_code');
            $table->string('city_name');
            $table->string('country_code');
            $table->string('country_name');
            $table->timestamp('arr_time');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('flight_segment_destinations');
    }
};
