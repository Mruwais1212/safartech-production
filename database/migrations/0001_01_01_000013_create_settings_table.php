<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('name_ar')->unique();
            $table->string('name_en')->unique();
            $table->string('code')->unique();
            $table->foreignId('setting_type_id');
            $table->string('value');
            $table->string('note_ar')->nullable();
            $table->string('note_en')->nullable();
            $table->string('input_type')->default('text');
            $table->integer('sort')->default(0);
            $table->integer('status')->default(1);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('settings');
    }
};
