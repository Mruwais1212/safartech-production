<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('destination_travel_interests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('destination_id');
            $table->foreignId('travel_interest_id');
            $table->unique(['destination_id', 'travel_interest_id'], 'destination_travel_interests_unique');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('destination_travel_interests');
    }
};
