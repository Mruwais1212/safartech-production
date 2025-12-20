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
        Schema::create('flight_segment_airlines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('flight_segment_id');
            $table->string('airline_code');
            $table->string('airline_name');
            $table->string('flight_number');
            $table->string('fare_class');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('flight_segment_airlines');
    }
};
