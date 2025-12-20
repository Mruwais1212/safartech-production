<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transportation', function (Blueprint $table) {
            $table->id();
            $table->string('name_ar');
            $table->string('name_en');
            $table->float('min_price');
            $table->float('max_price');
            $table->foreignId('destination_id');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transportations');
    }
};
