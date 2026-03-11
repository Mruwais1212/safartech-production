<?php

namespace App\Console\Commands;

use App\Models\ReservationHotel;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CheckBookingStatusCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'booking:check-status {hotel_id?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check booking details status for hotel reservations';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $hotelId = $this->argument('hotel_id');

        if ($hotelId) {
            $this->checkSingleBooking($hotelId);
        } else {
            $this->checkAllBookings();
        }

        return 0;
    }

    private function checkSingleBooking($hotelId)
    {
        $hotel = ReservationHotel::find($hotelId);

        if (! $hotel) {
            $this->error("Hotel reservation with ID {$hotelId} not found.");

            return;
        }

        $this->info('=== BOOKING STATUS REPORT ===');
        $this->displayBookingInfo($hotel);

        // Check failed jobs
        $this->checkFailedJobs($hotelId);

        // Check pending jobs
        $this->checkPendingJobs($hotelId);
    }

    private function checkAllBookings()
    {
        $this->info('=== ALL BOOKINGS STATUS ===');

        $hotels = ReservationHotel::whereNotNull('confirmation_number')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        if ($hotels->count() === 0) {
            $this->warn('No hotel bookings found.');

            return;
        }

        $headers = ['ID', 'Booking Ref', 'Confirmation', 'Status', 'Details Fetched', 'Created'];
        $rows = [];

        foreach ($hotels as $hotel) {
            $rows[] = [
                $hotel->id,
                $hotel->client_reference_id ?? 'N/A',
                $hotel->confirmation_number ?? 'N/A',
                $hotel->booking_status ?? 'Unknown',
                $hotel->booking_details_fetched_at ? $hotel->booking_details_fetched_at->format('Y-m-d H:i:s') : 'Never',
                $hotel->created_at->format('Y-m-d H:i:s'),
            ];
        }

        $this->table($headers, $rows);

        // Summary statistics
        $this->showSummaryStats();
    }

    private function displayBookingInfo($hotel)
    {
        $this->info("Hotel ID: {$hotel->id}");
        $this->info('Booking Reference: '.($hotel->client_reference_id ?? 'N/A'));
        $this->info('Confirmation Number: '.($hotel->confirmation_number ?? 'N/A'));
        $this->info('Booking Status: '.($hotel->booking_status ?? 'Not Set'));
        $this->info('Details Fetched At: '.($hotel->booking_details_fetched_at ?? 'Never'));
        $this->info('Created At: '.$hotel->created_at->format('Y-m-d H:i:s'));

        if ($hotel->booking_details_response) {
            $response = json_decode($hotel->booking_details_response, true);
            $this->info("\n=== API RESPONSE ===");
            $this->info('Status Code: '.($response['Status']['Code'] ?? 'Unknown'));
            $this->info('Status Message: '.($response['Status']['Description'] ?? 'No message'));

            if (isset($response['BookingStatus'])) {
                $this->info('TBO Booking Status: '.$response['BookingStatus']);
            }
        } else {
            $this->warn("\nNo API response data found.");
        }

        // Check if details should have been fetched by now
        $shouldHaveFetched = $hotel->created_at->addMinutes(3); // 2 min delay + 1 min buffer
        if (now()->isAfter($shouldHaveFetched) && ! $hotel->booking_details_fetched_at) {
            $this->error('⚠️  ALERT: Booking details should have been fetched by now!');
        }
    }

    private function checkFailedJobs($hotelId)
    {
        $failedJobs = DB::table('failed_jobs')
            ->where('payload', 'like', '%FetchBookingDetailsJob%')
            ->where('payload', 'like', "%{$hotelId}%")
            ->orderBy('failed_at', 'desc')
            ->get();

        if ($failedJobs->count() > 0) {
            $this->error("\n⚠️  FAILED JOBS FOUND: ".$failedJobs->count());
            foreach ($failedJobs as $job) {
                $this->error("Failed at: {$job->failed_at}");
                $this->error('Exception: '.substr($job->exception, 0, 200).'...');
            }
        } else {
            $this->info("\n✅ No failed jobs found for this booking.");
        }
    }

    private function checkPendingJobs($hotelId)
    {
        $pendingJobs = DB::table('jobs')
            ->where('payload', 'like', '%FetchBookingDetailsJob%')
            ->where('payload', 'like', "%{$hotelId}%")
            ->get();

        if ($pendingJobs->count() > 0) {
            $this->info("\n⏳ PENDING JOBS: ".$pendingJobs->count());
            foreach ($pendingJobs as $job) {
                $availableAt = \Carbon\Carbon::createFromTimestamp($job->available_at);
                $this->info('Scheduled to run at: '.$availableAt->format('Y-m-d H:i:s'));
                $this->info('Time remaining: '.$availableAt->diffForHumans());
            }
        } else {
            $this->info("\n✅ No pending jobs found for this booking.");
        }
    }

    private function showSummaryStats()
    {
        $total = ReservationHotel::whereNotNull('confirmation_number')->count();
        $fetched = ReservationHotel::whereNotNull('booking_details_fetched_at')->count();
        $failed = DB::table('failed_jobs')->where('payload', 'like', '%FetchBookingDetailsJob%')->count();
        $pending = DB::table('jobs')->where('payload', 'like', '%FetchBookingDetailsJob%')->count();

        $this->info("\n=== SUMMARY STATISTICS ===");
        $this->info("Total Bookings: {$total}");
        $this->info("Details Fetched: {$fetched}");
        $this->info("Failed Jobs: {$failed}");
        $this->info("Pending Jobs: {$pending}");

        if ($total > 0) {
            $successRate = round(($fetched / $total) * 100, 2);
            $this->info("Success Rate: {$successRate}%");
        }
    }
}
