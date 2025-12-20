<?php

namespace App\Console\Commands;

use App\Models\ReservationHotel;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MonitorBookingSystemCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'booking:monitor {--alert-threshold=10}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Monitor booking details system health and send alerts';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $alertThreshold = $this->option('alert-threshold');
        
        $this->info("=== BOOKING SYSTEM HEALTH MONITOR ===");
        $this->info("Alert Threshold: {$alertThreshold} minutes");
        $this->info("Monitoring Time: " . now()->format('Y-m-d H:i:s'));
        
        $issues = [];
        
        // Check 1: Bookings without details fetch after expected time
        $overdue = $this->checkOverdueBookings($alertThreshold);
        if ($overdue > 0) {
            $issues[] = "Overdue booking details: {$overdue}";
        }
        
        // Check 2: Failed jobs in last hour
        $failedJobs = $this->checkRecentFailedJobs();
        if ($failedJobs > 0) {
            $issues[] = "Failed jobs in last hour: {$failedJobs}";
        }
        
        // Check 3: Queue worker status
        $queueHealth = $this->checkQueueHealth();
        if (!$queueHealth) {
            $issues[] = "Queue worker may not be running";
        }
        
        // Check 4: Recent successful operations
        $recentSuccess = $this->checkRecentSuccesses();
        
        // Display results
        $this->displayHealthReport($issues, $recentSuccess);
        
        // Log monitoring results
        $this->logMonitoringResults($issues);
        
        return empty($issues) ? 0 : 1;
    }
    
    private function checkOverdueBookings($thresholdMinutes)
    {
        $cutoffTime = now()->subMinutes($thresholdMinutes);
        
        $overdueBookings = ReservationHotel::whereNotNull('confirmation_number')
            ->whereNull('booking_details_fetched_at')
            ->where('created_at', '<', $cutoffTime)
            ->count();
            
        if ($overdueBookings > 0) {
            $this->error("⚠️  {$overdueBookings} bookings are overdue for details fetch");
            
            // Show some examples
            $examples = ReservationHotel::whereNotNull('confirmation_number')
                ->whereNull('booking_details_fetched_at')
                ->where('created_at', '<', $cutoffTime)
                ->limit(5)
                ->get(['id', 'client_reference_id', 'created_at']);
                
            $this->info("Recent overdue examples:");
            foreach ($examples as $example) {
                $age = $example->created_at->diffForHumans();
                $this->info("  - ID: {$example->id}, Ref: {$example->client_reference_id}, Age: {$age}");
            }
        } else {
            $this->info("✅ No overdue bookings found");
        }
        
        return $overdueBookings;
    }
    
    private function checkRecentFailedJobs()
    {
        $oneHourAgo = now()->subHour();
        
        $failedJobs = DB::table('failed_jobs')
            ->where('payload', 'like', "%FetchBookingDetailsJob%")
            ->where('failed_at', '>=', $oneHourAgo)
            ->count();
            
        if ($failedJobs > 0) {
            $this->error("⚠️  {$failedJobs} jobs failed in the last hour");
            
            // Show recent failures
            $recentFailures = DB::table('failed_jobs')
                ->where('payload', 'like', "%FetchBookingDetailsJob%")
                ->where('failed_at', '>=', $oneHourAgo)
                ->orderBy('failed_at', 'desc')
                ->limit(3)
                ->get(['failed_at', 'exception']);
                
            $this->info("Recent failures:");
            foreach ($recentFailures as $failure) {
                $error = substr($failure->exception, 0, 100) . "...";
                $this->info("  - {$failure->failed_at}: {$error}");
            }
        } else {
            $this->info("✅ No recent failed jobs");
        }
        
        return $failedJobs;
    }
    
    private function checkQueueHealth()
    {
        // Check if there are jobs in the queue that should have been processed
        $oldJobs = DB::table('jobs')
            ->where('available_at', '<', now()->subMinutes(5)->timestamp)
            ->count();
            
        if ($oldJobs > 0) {
            $this->error("⚠️  {$oldJobs} jobs are stuck in queue (older than 5 minutes)");
            return false;
        } else {
            $this->info("✅ Queue processing appears healthy");
            return true;
        }
    }
    
    private function checkRecentSuccesses()
    {
        $oneHourAgo = now()->subHour();
        
        $successCount = ReservationHotel::where('booking_details_fetched_at', '>=', $oneHourAgo)
            ->count();
            
        $this->info("✅ {$successCount} successful details fetches in the last hour");
        
        return $successCount;
    }
    
    private function displayHealthReport($issues, $recentSuccess)
    {
        $this->info("\n=== HEALTH REPORT SUMMARY ===");
        
        if (empty($issues)) {
            $this->info("🟢 SYSTEM HEALTHY - All checks passed");
        } else {
            $this->error("🔴 SYSTEM ISSUES DETECTED:");
            foreach ($issues as $issue) {
                $this->error("  - {$issue}");
            }
        }
        
        $this->info("Recent successful operations: {$recentSuccess}");
        
        // Recommendations
        if (!empty($issues)) {
            $this->info("\n=== RECOMMENDATIONS ===");
            $this->info("1. Check if queue worker is running: php artisan queue:work");
            $this->info("2. Review failed jobs: php artisan failed:list");
            $this->info("3. Check application logs for errors");
            $this->info("4. Verify TBO API connectivity");
        }
    }
    
    private function logMonitoringResults($issues)
    {
        if (empty($issues)) {
            Log::info("BookingSystemMonitor: System healthy - all checks passed");
        } else {
            Log::warning("BookingSystemMonitor: Issues detected", [
                'issues' => $issues,
                'timestamp' => now()->toISOString()
            ]);
        }
    }
}
