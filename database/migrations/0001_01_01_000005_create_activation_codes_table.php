<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('activation_codes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->string('phone');
            $table->string('phone_code')->default(20);
            $table->string('activation_code')->nullable();
            $table->integer('activate')->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('activation_codes');
    }
};
