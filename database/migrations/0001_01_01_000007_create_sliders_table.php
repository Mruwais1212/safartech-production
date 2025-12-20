<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('sliders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('main_slider_id');
            $table->string('photo');
            $table->string('mobile_photo')->nullable();
            $table->string('title_ar');
            $table->string('title_en');
            $table->string('description_ar')->nullable();
            $table->string('description_en')->nullable();
            $table->string('button_text_ar')->nullable();
            $table->string('button_text_en')->nullable();
            $table->string('button_url')->nullable();
            $table->string('text_color')->nullable();
            $table->integer('type')->default(1)->comment('1 => photo , 2 => video');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('sliders');
    }
};
