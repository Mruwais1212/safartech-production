<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('privilege_groups', function (Blueprint $table) {
            $table->id();
            $table->string('name_ar');
            $table->string('name_en');
            $table->integer('guard')->default(1);
            $table->foreignId('provider_id')->nullable()->on('users');
            $table->unique(['name_ar', 'name_en', 'provider_id', 'guard']);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('privilege_groups');
    }
};
