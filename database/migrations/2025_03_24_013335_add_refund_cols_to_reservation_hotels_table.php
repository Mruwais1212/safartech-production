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
        Schema::table('reservation_hotels', function (Blueprint $table) {
            $table->enum('chargeType', ['Percentage', 'Amount'])->nullable()->default('Percentage');
            $table->decimal('cancellationCharge', 10, 2)->nullable()->default(0.00);
            $table->boolean('refunded')->nullable()->default(false);
            $table->boolean('is_refundable')->nullable()->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reservation_hotels', function (Blueprint $table) {
            $table->dropColumn(['chargeType', 'cancellationCharge', 'refunded', 'is_refundable']);
        });
    }
};
