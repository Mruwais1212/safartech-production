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
            $table->integer('first_tax_rate')->nullable()->default(0);
            $table->decimal('first_tax_amount', 10, 2)->nullable()->default(0);

            $table->integer('second_tax_rate')->nullable()->default(0);
            $table->decimal('second_tax_amount', 10, 2)->nullable()->default(0);

            $table->integer('administrative_tax_rate')->nullable()->default(0);
            $table->decimal('administrative_tax_amount', 10, 2)->nullable()->default(0);

            $table->decimal('price_with_tax', 10, 2)->nullable()->default(0);
            $table->decimal('price_without_tax', 10, 2)->nullable()->default(0);
            $table->decimal('tax_amount', 10, 2)->nullable()->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reservation_hotels', function (Blueprint $table) {
            $table->dropColumn(['first_tax_rate', 'first_tax_amount', 'second_tax_rate', 'second_tax_amount', 'administrative_tax_rate', 'administrative_tax_amount', 'price_with_tax', 'price_without_tax', 'tax_amount']);

        });
    }
};
