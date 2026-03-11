<?php

namespace App\Console\Commands;

use App\Jobs\FetchBookingDetailsJob;
use App\Models\ReservationHotel;
use Illuminate\Console\Command;

class TestBookingDetailsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'booking:test-details {hotel_id} {booking_ref_id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test the booking details job for a specific hotel reservation';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $hotelId = $this->argument('hotel_id');
        $bookingRefId = $this->argument('booking_ref_id');

        $hotel = ReservationHotel::find($hotelId);

        if (! $hotel) {
            $this->error("Hotel reservation with ID {$hotelId} not found.");

            return 1;
        }

        $this->info('=== BOOKING DETAILS TEST ===');
        $this->info("Hotel ID: {$hotelId}");
        $this->info("Booking Reference: {$bookingRefId}");
        $this->info('Confirmation Number: '.($hotel->confirmation_number ?? 'N/A'));

        // Show current status
        $this->info("\n=== CURRENT STATUS ===");
        $this->info('Booking Status: '.($hotel->booking_status ?? 'Not Set'));
        $this->info('Details Fetched At: '.($hotel->booking_details_fetched_at ?? 'Never'));

        // Ask user for test type
        $testType = $this->choice('Select test type:', [
            'immediate' => 'Run immediately (for testing)',
            'delayed' => 'Run with 120 second delay (production-like)',
            'background' => 'Dispatch to background and monitor',
        ], 'immediate');

        switch ($testType) {
            case 'immediate':
                $this->testImmediate($hotelId, $bookingRefId);
                break;
            case 'delayed':
                $this->testDelayed($hotelId, $bookingRefId);
                break;
            case 'background':
                $this->testBackground($hotelId, $bookingRefId);
                break;
        }

        return 0;
    }

    private function testImmediate($hotelId, $bookingRefId)
    {
        $this->info("\n=== IMMEDIATE TEST ===");

        // Dispatch the job without delay for testing
        FetchBookingDetailsJob::dispatch($hotelId, $bookingRefId)->delay(now());

        $this->info('Job dispatched! Waiting for execution...');

        // Wait a moment and check results
        sleep(3);

        $this->checkResults($hotelId);
    }

    private function testDelayed($hotelId, $bookingRefId)
    {
        $this->info("\n=== DELAYED TEST (120 seconds) ===");

        // Dispatch with actual delay
        FetchBookingDetailsJob::dispatch($hotelId, $bookingRefId);

        $this->info('Job dispatched with 120-second delay!');
        $this->info('The job will execute at: '.now()->addSeconds(120)->format('Y-m-d H:i:s'));
        $this->info("Use 'php artisan queue:work' to process the job when ready.");

        if ($this->confirm('Do you want to monitor the job status?')) {
            $this->monitorJob($hotelId, 120);
        }
    }

    private function testBackground($hotelId, $bookingRefId)
    {
        $this->info("\n=== BACKGROUND TEST ===");

        // Dispatch with actual delay
        FetchBookingDetailsJob::dispatch($hotelId, $bookingRefId);

        $this->info('Job dispatched to background queue!');
        $this->info('Make sure queue worker is running: php artisan queue:work');

        // Show how to monitor
        $this->info("\n=== MONITORING COMMANDS ===");
        $this->info('1. Check queue status: php artisan queue:monitor');
        $this->info('2. Check logs: tail -f storage/logs/laravel.log');
        $this->info("3. Check job result: php artisan booking:check-status {$hotelId}");
    }

    private function monitorJob($hotelId, $delaySeconds)
    {
        $endTime = now()->addSeconds($delaySeconds + 10); // Add buffer
        $this->info('Monitoring until: '.$endTime->format('Y-m-d H:i:s'));

        $bar = $this->output->createProgressBar($delaySeconds);
        $bar->start();

        for ($i = 0; $i < $delaySeconds; $i++) {
            sleep(1);
            $bar->advance();

            // Check if job executed early
            $hotel = ReservationHotel::find($hotelId);
            if ($hotel->booking_details_fetched_at && $hotel->booking_details_fetched_at->isAfter(now()->subMinutes(1))) {
                $bar->finish();
                $this->info("\n✅ Job executed successfully!");
                $this->checkResults($hotelId);

                return;
            }
        }

        $bar->finish();
        $this->info("\n⏰ Waiting period completed. Checking results...");
        $this->checkResults($hotelId);
    }

    private function checkResults($hotelId)
    {
        $hotel = ReservationHotel::find($hotelId);

        $this->info("\n=== RESULTS ===");
        $this->info('Booking Status: '.($hotel->booking_status ?? 'Not Updated'));
        $this->info('Details Fetched At: '.($hotel->booking_details_fetched_at ?? 'Not Fetched'));

        if ($hotel->booking_details_response) {
            $response = json_decode($hotel->booking_details_response, true);
            $this->info('API Response Status: '.($response['Status']['Code'] ?? 'Unknown'));
            $this->info('API Response Message: '.($response['Status']['Description'] ?? 'No message'));
        } else {
            $this->warn('No API response stored yet.');
        }

        // Show recent logs
        $this->info("\n=== RECENT LOGS ===");
        $logFile = storage_path('logs/laravel.log');
        if (file_exists($logFile)) {
            $logs = shell_exec("tail -10 {$logFile} | grep 'FetchBookingDetailsJob'");
            if ($logs) {
                $this->info($logs);
            } else {
                $this->warn('No recent FetchBookingDetailsJob logs found.');
            }
        }
    }
}
