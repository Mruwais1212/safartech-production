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
        Schema::create('transaction_cards', function (Blueprint $table) {
            $table->id();
            $table->string('number')->nullable();
            $table->string('name')->nullable();
            $table->string('reference_number')->nullable();
            $table->string('company')->nullable();
            $table->foreignId('reservation_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaction_cards');
    }
};
