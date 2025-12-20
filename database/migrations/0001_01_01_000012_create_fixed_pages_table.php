<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('fixed_pages', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('name_ar')->unique();
            $table->string('name_en')->unique();
            $table->text('content_ar');
            $table->text('content_en');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('fixed_pages');
    }
};
