<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('group_notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id');
            $table->string('title_ar');
            $table->string('title_en');
            $table->text('message_ar');
            $table->text('message_en');
            $table->json('types');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('group_notifications');
    }
};
