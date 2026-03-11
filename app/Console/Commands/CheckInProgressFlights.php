<?php

namespace App\Console\Commands;

use App\Jobs\FetchFlightBookingDetailsJob;
use App\Models\ReservationFlight;
use App\Services\TBOFlightBookingService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CheckInProgressFlights extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'flights:check-inprogress {--force : Force check all InProgress flights} {--immediate : Check immediately without delay} {--pnr= : Check specific PNR}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check InProgress flight bookings and fetch their details from TBO API';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 Checking InProgress flight bookings...');

        $query = ReservationFlight::query();

        if ($this->option('pnr')) {
            // Check specific PNR
            $query->where('pnr', $this->option('pnr'));
            $this->info('🎯 Checking specific PNR: '.$this->option('pnr'));
        } else {
            // Check InProgress flights
            $query->where('is_in_progress', true)
                ->where('status', 5); // InProgress status

            if (! $this->option('force')) {
                // Only check flights that haven't been checked in the last 5 minutes
                $query->where(function ($q) {
                    $q->whereNull('booking_details_fetched_at')
                        ->orWhere('booking_details_fetched_at', '<', now()->subMinutes(5));
                });
            }
        }

        $flights = $query->get();

        if ($flights->isEmpty()) {
            $this->info('✅ No InProgress flights to check');

            return 0;
        }

        $this->info("📋 Found {$flights->count()} InProgress flight(s) to check");

        $checked = 0;
        $updated = 0;
        $errors = 0;

        foreach ($flights as $flight) {
            $checked++;

            $this->line("🔄 [{$checked}/{$flights->count()}] Checking flight ID: {$flight->id}, PNR: {$flight->pnr}");

            try {
                if ($this->option('immediate')) {
                    // Check immediately
                    $result = $this->checkFlightImmediately($flight);
                    if ($result) {
                        $updated++;
                        $this->info("✅ Updated flight {$flight->id} status");
                    } else {
                        $this->warn("⚠️  No update available for flight {$flight->id}");
                    }
                } else {
                    // Dispatch job to check (with minimal delay)
                    FetchFlightBookingDetailsJob::dispatch($flight->id, $flight->pnr, $flight->tbo_booking_id, 0);
                    $this->info("📤 Dispatched job for flight {$flight->id}");
                    $updated++;
                }

            } catch (\Exception $e) {
                $errors++;
                $this->error("❌ Error checking flight {$flight->id}: ".$e->getMessage());
                Log::error('CheckInProgressFlights command error', [
                    'flight_id' => $flight->id,
                    'pnr' => $flight->pnr,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        // Summary
        $this->newLine();
        $this->info('📊 Summary:');
        $this->info("   Checked: {$checked}");
        $this->info("   Updated/Dispatched: {$updated}");
        $this->info("   Errors: {$errors}");

        if ($errors > 0) {
            $this->warn('⚠️  Some flights had errors. Check logs for details.');
        }

        return 0;
    }

    /**
     * Check flight status immediately using TBO API
     */
    private function checkFlightImmediately(ReservationFlight $flight)
    {
        $this->line("   🔍 Checking TBO API for PNR: {$flight->pnr}");

        $tboService = new TBOFlightBookingService;
        $result = $tboService->getBookingDetails($flight->pnr, $flight->tbo_booking_id);

        if ($result['status'] === 'success') {
            $bookingData = $result['booking_details'];

            $updateData = [
                'booking_details_fetched_at' => now(),
                'booking_details_response' => json_encode($bookingData),
                'booking_details_fetched' => true,
            ];

            // Update status based on TBO response
            if (isset($result['booking_status'])) {
                $updateData['booking_status'] = $result['booking_status'];
                $updateData['status'] = $this->mapTboStatusToInternal($result['booking_status']);

                $this->line("   📈 Status update: {$result['booking_status']} -> {$updateData['status']}");
            }

            // Update ticket status if available
            if (isset($result['ticket_status'])) {
                $updateData['ticket_status'] = $result['ticket_status'];

                if (in_array($result['ticket_status'], [1, 'Confirmed', 'Ticketed'])) {
                    $updateData['is_ticketed'] = true;
                    $updateData['ticketed_at'] = now();
                    $updateData['is_in_progress'] = false; // No longer in progress
                    $this->line('   🎫 Flight is now ticketed!');
                }
            }

            // Update the flight
            $flight->update($updateData);

            // Update related flight if exists
            $this->updateRelatedFlight($flight, $updateData);

            return true;

        } else {
            $this->line('   ❌ API call failed: '.($result['error'] ?? 'Unknown error'));

            // Mark as fetch failed
            $flight->update([
                'booking_details_fetched_at' => now(),
                'booking_details_fetch_failed' => true,
                'last_fetch_error' => $result['error'] ?? 'API call failed',
            ]);

            return false;
        }
    }

    /**
     * Map TBO status to internal status
     */
    private function mapTboStatusToInternal($tboStatus)
    {
        $statusMap = [
            1 => 1,        // Confirmed -> Confirmed
            2 => 2,        // Cancelled -> Cancelled
            3 => 3,        // Failed -> Failed
            4 => 4,        // Pending -> Pending
            5 => 5,        // InProgress -> InProgress
            6 => 1,        // Ticketed -> Confirmed
            'Confirmed' => 1,
            'Cancelled' => 2,
            'Failed' => 3,
            'Pending' => 4,
            'InProgress' => 5,
            'Ticketed' => 1,
        ];

        return $statusMap[$tboStatus] ?? 5;
    }

    /**
     * Update related flight with same data
     */
    private function updateRelatedFlight(ReservationFlight $flight, array $updateData)
    {
        try {
            $relatedFlight = null;

            if ($flight->outbound_id == 0) {
                $relatedFlight = ReservationFlight::where('outbound_id', $flight->id)->first();
            } else {
                $relatedFlight = ReservationFlight::find($flight->outbound_id);
            }

            if ($relatedFlight && $relatedFlight->pnr === $flight->pnr) {
                $relatedFlight->update($updateData);
                $this->line("   🔗 Updated related flight ID: {$relatedFlight->id}");
            }

        } catch (\Exception $e) {
            $this->error('   ❌ Error updating related flight: '.$e->getMessage());
        }
    }
}
