<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('group_privileges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('privilege_group_id');
            $table->foreignId('privilege_id');
            $table->unique(['privilege_group_id', 'privilege_id']);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('group_privileges');
    }
};
