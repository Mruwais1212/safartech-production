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
        Schema::create('flight_segments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('flight_id');
            $table->string('baggage');
            $table->string('cabin_baggage');
            $table->string('cabin_class');
            $table->integer('trip_indicator');
            $table->integer('segment_indicator');
            $table->string('duration');
            $table->string('no_of_seat_available');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('flight_segments');
    }
};
