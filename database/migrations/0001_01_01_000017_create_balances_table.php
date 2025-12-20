<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('balances', function (Blueprint $table) {
            $table->id();
            $table->string('title_ar');
            $table->string('title_en');
            $table->text('description_ar')->nullable();
            $table->text('description_en')->nullable();
            $table->foreignId('user_id');
            $table->unsignedBigInteger('item_id')->nullable();
            $table->string('item_type')->nullable();
            $table->foreignId('balance_type_id');
            $table->integer('amount');
            $table->integer('status')->default('0')->comment('0=blocked,1=available');
            $table->string('currency')->default('SAR');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('balances');
    }
};
