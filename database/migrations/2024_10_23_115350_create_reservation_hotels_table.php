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
        Schema::create('reservation_hotels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reservation_id');
            $table->string('hotel_id');
            $table->date('check_in');
            $table->date('check_out');
            $table->integer('rooms');
            $table->integer('adults');
            $table->integer('children');
            $table->decimal('price', 10, 2);
            $table->string('currency');
            $table->string('hotel_name');
            $table->string('hotel_image');
            $table->string('hotel_address');
            $table->string('hotel_rate');
            $table->string('booking_code');
            $table->integer('status')->default(0);
            $table->string('confirmation_number')->nullable();
            $table->dateTime('canceled_at')->nullable();
            $table->string('canceled_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservation_hotels');
    }
};
