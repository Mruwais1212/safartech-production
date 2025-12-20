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
        Schema::table('reservations', function (Blueprint $table) {
            $table->integer('from_id')->nullable();
            $table->integer('to_id')->nullable();
            $table->string('from_city')->nullable();
            $table->string('to_city')->nullable();

            $table->string('uuid')->nullable();
            $table->string('reservation_type')->nullable();
            $table->decimal('unit_price', 10, 2)->nullable();
            $table->string('payment_type')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->dropColumn(['from_id', 'to_id', 'from_city', 'to_city', 'uuid', 'reservation_type', 'unit_price', 'payment_type']);
        });
    }
};
