<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('payment_settings', function (Blueprint $table) {
            $table->id();
            $table->string('name_ar')->nullable();
            $table->string('name_en')->nullable();
            $table->string('code')->unique();
            $table->integer('status')->default(0);
            $table->string('public_key')->nullable();
            $table->string('secret_key')->nullable();
            $table->string('merchant_code')->nullable();
            $table->string('auth_token')->nullable();
            $table->string('tranportal_id')->nullable();
            $table->string('tranportal_password')->nullable();
            $table->string('resource_key')->nullable();
            $table->string('notification_token')->nullable();
            $table->string('currency')->nullable();
            $table->string('country_code')->nullable();
            $table->string('lang')->nullable();
            $table->string('photo')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('payment_settings');
    }
};
