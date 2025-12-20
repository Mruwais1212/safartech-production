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
        Schema::table('reservation_flights', function (Blueprint $table) {
            // Check if columns don't exist before adding them
            if (!Schema::hasColumn('reservation_flights', 'booking_details_fetched')) {
                // Booking details fetching tracking
                $table->boolean('booking_details_fetched')->default(false)->after('booking_json');
                $table->timestamp('booking_details_fetched_at')->nullable()->after('booking_details_fetched');
                $table->longText('booking_details_response')->nullable()->after('booking_details_fetched_at');
                $table->boolean('booking_details_fetch_failed')->default(false)->after('booking_details_response');
                $table->text('last_fetch_error')->nullable()->after('booking_details_fetch_failed');
            }
            
            if (!Schema::hasColumn('reservation_flights', 'booking_status')) {
                // Status tracking
                $table->string('booking_status')->nullable()->after('last_fetch_error'); // TBO booking status
                $table->string('ticket_status')->nullable()->after('booking_status'); // TBO ticket status
                $table->boolean('booking_status_updated')->default(false)->after('ticket_status');
                $table->boolean('is_ticketed')->default(false)->after('booking_status_updated');
                $table->timestamp('ticketed_at')->nullable()->after('is_ticketed');
            }
            
            if (!Schema::hasColumn('reservation_flights', 'needs_manual_review')) {
                // Manual review flag
                $table->boolean('needs_manual_review')->default(false)->after('ticketed_at');
            }
            
            // Add is_in_progress column if it doesn't exist (needed for index)
            if (!Schema::hasColumn('reservation_flights', 'is_in_progress')) {
                $table->boolean('is_in_progress')->default(false)->after('needs_manual_review');
            }
            
            // Add status column if it doesn't exist (needed for index)
            if (!Schema::hasColumn('reservation_flights', 'status')) {
                $table->string('status')->default('pending')->after('is_in_progress');
            }
            
            // Add tbo_booking_id column if it doesn't exist (needed for index)
            if (!Schema::hasColumn('reservation_flights', 'tbo_booking_id')) {
                $table->string('tbo_booking_id')->nullable()->after('status');
            }
            
            // Add pnr column if it doesn't exist (needed for index)
            if (!Schema::hasColumn('reservation_flights', 'pnr')) {
                $table->string('pnr')->nullable()->after('tbo_booking_id');
            }
        });
        
        // Add indexes separately to avoid conflicts
        Schema::table('reservation_flights', function (Blueprint $table) {
            // Check if indexes don't exist before adding them
            $indexes = Schema::getIndexes('reservation_flights');
            $indexNames = array_column($indexes, 'name');
            
            if (!in_array('rf_progress_status_review_idx', $indexNames)) {
                $table->index(['is_in_progress', 'status', 'needs_manual_review'], 'rf_progress_status_review_idx');
            }
            
            if (!in_array('rf_pnr_booking_id_idx', $indexNames)) {
                $table->index(['pnr', 'tbo_booking_id'], 'rf_pnr_booking_id_idx');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reservation_flights', function (Blueprint $table) {
            // Drop indexes first
            try {
                $table->dropIndex('rf_progress_status_review_idx');
            } catch (\Exception $e) {
                // Index might not exist
            }
            
            try {
                $table->dropIndex('rf_pnr_booking_id_idx');
            } catch (\Exception $e) {
                // Index might not exist
            }
            
            // Only drop columns that we added in this migration
            $columnsToCheck = [
                'booking_details_fetched',
                'booking_details_fetched_at',
                'booking_details_response',
                'booking_details_fetch_failed',
                'last_fetch_error',
                'booking_status',
                'ticket_status',
                'booking_status_updated',
                'is_ticketed',
                'ticketed_at',
                'needs_manual_review',
                'is_in_progress',
                'status',
                'tbo_booking_id',
                'pnr'
            ];
            
            foreach ($columnsToCheck as $column) {
                if (Schema::hasColumn('reservation_flights', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
