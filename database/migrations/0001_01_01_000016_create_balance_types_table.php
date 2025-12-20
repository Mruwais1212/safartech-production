<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('balance_types', function (Blueprint $table) {
            $table->id();
            $table->string('title_ar')->unique();
            $table->string('title_en')->unique();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('balance_types');
    }
};
