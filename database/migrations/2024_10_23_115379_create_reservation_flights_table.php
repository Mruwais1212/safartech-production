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
        Schema::create('reservation_flights', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reservation_id');
            $table->text('result_index');
            $table->string('trace_id');
            $table->boolean('is_lcc');
            $table->boolean('is_refundable');
            $table->string('last_ticket_date')->nullable();
            $table->decimal('total_price', 10, 2);
            $table->boolean('is_direct');
            $table->string('flight_class')->nullable();
            $table->string('pnr')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservation_flights');
    }
};
