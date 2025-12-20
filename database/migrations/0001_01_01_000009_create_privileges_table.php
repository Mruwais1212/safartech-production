<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('privileges', function (Blueprint $table) {
            $table->id();
            $table->string('name_ar');
            $table->string('name_en');
            $table->string('url')->nullable();
            $table->string('code')->unique();
            $table->string('controller');
            $table->string('method')->nullable();
            $table->string('type')->default(1)->comment('1=get,2=post,put,delete');
            $table->string('icon')->nullable();
            $table->integer('parent_id')->nullable();
            $table->integer('sort')->default(0);
            $table->integer('status')->default(1);
            $table->unique(['controller', 'method']);
            $table->integer('guard')->default(1);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('privileges');
    }
};
