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
        Schema::create('reservation_passengers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reservation_id');
            $table->string('title');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('pax_type');
            $table->date('birth_date');
            $table->string('email');
            $table->string('phone');
            $table->string('nationality');
            $table->string('gender');
            $table->string('address');
            $table->string('passport_number');
            $table->date('passport_expiry_date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservation_passengers');
    }
};
