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
        Schema::create('t_b_o_hotels', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name_ar')->nullable();
            $table->string('name_en')->nullable();
            $table->text('description_ar')->nullable();
            $table->text('description_en')->nullable();
            $table->string('address')->nullable();
            $table->json('facilities_ar')->nullable();
            $table->json('facilities_en')->nullable();
            $table->json('images')->nullable();
            $table->json('attractions')->nullable();
            $table->string('latitude')->nullable();
            $table->string('longitude')->nullable();
            $table->string('rating')->nullable();
            $table->string('pin_code')->nullable();
            $table->string('phone')->nullable();
            $table->string('fax')->nullable();
            $table->string('city_code')->nullable();
            $table->string('city_name')->nullable();
            $table->string('country_code')->nullable();
            $table->string('country_name')->nullable();
            $table->string('check_in_time')->nullable();
            $table->string('check_out_time')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('t_b_o_hotels');
    }
};
