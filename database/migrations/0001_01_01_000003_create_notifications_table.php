<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id');
            $table->string('title_ar');
            $table->string('title_en');
            $table->string('message_ar');
            $table->string('message_en');
            $table->integer('status')->default(0);
            $table->integer('type')->default(1);
            $table->integer('user_type_id')->default(5);
            $table->foreignId('item_id')->nullable();
            $table->string('item_type')->nullable();
            $table->string('notification_type')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('notifications');
    }
};
