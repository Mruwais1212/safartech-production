<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('t_b_o_hotels', function (Blueprint $table) {
            $table->timestamp('cached_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('t_b_o_hotels', function (Blueprint $table) {
            $table->dropColumn('cached_at');
        });
    }
};
