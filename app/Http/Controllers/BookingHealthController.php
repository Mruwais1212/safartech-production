<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Website\Controller;
use App\Models\ReservationHotel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BookingHealthController extends Controller
{
    public function systemHealth()
    {
        $health = [
            'status' => 'healthy',
            'timestamp' => now()->toISOString(),
            'checks' => []
        ];
        
        // Add basic system check
        $health['checks']['system'] = [
            'status' => 'ok',
            'message' => 'System is running'
        ];
        
        try {
            // Check 1: Recent booking details fetches
            $recentFetches = ReservationHotel::where('booking_details_fetched_at', '>=', now()->subHour())
                ->count();
            $health['checks']['recent_fetches'] = [
                'status' => 'ok',
                'count' => $recentFetches,
                'message' => "Successfully fetched {$recentFetches} booking details in the last hour"
            ];
            
            // Check 2: Overdue bookings
            $overdueBookings = ReservationHotel::whereNotNull('confirmation_number')
            ->whereNull('booking_details_fetched_at')
            ->where('created_at', '<', now()->subMinutes(10))
            ->count();
            
        $health['checks']['overdue_bookings'] = [
            'status' => $overdueBookings > 0 ? 'warning' : 'ok',
            'count' => $overdueBookings,
            'message' => $overdueBookings > 0 ? "Found {$overdueBookings} overdue bookings" : "No overdue bookings"
        ];
        
        // Check 3: Failed jobs
        $failedJobs = DB::table('failed_jobs')
            ->where('payload', 'like', "%FetchBookingDetailsJob%")
            ->where('failed_at', '>=', now()->subHour())
            ->count();
            
        $health['checks']['failed_jobs'] = [
            'status' => $failedJobs > 0 ? 'error' : 'ok',
            'count' => $failedJobs,
            'message' => $failedJobs > 0 ? "Found {$failedJobs} failed jobs in the last hour" : "No recent failed jobs"
        ];
        
        // Check 4: Queue health
        $stuckJobs = DB::table('jobs')
            ->where('available_at', '<', now()->subMinutes(5)->timestamp)
            ->count();
            
        $health['checks']['queue_health'] = [
            'status' => $stuckJobs > 0 ? 'warning' : 'ok',
            'count' => $stuckJobs,
            'message' => $stuckJobs > 0 ? "Found {$stuckJobs} stuck jobs in queue" : "Queue processing normally"
        ];
        
        // Overall status
        $hasErrors = collect($health['checks'])->contains('status', 'error');
        $hasWarnings = collect($health['checks'])->contains('status', 'warning');
        
        if ($hasErrors) {
            $health['status'] = 'error';
        } elseif ($hasWarnings) {
            $health['status'] = 'warning';
        }
        
        } catch (\Exception $e) {
            $health['checks']['database'] = [
                'status' => 'error',
                'message' => 'Database connection failed: ' . $e->getMessage()
            ];
            $health['status'] = 'error';
        }
        
        $httpStatus = $health['status'] === 'error' ? 500 : ($health['status'] === 'warning' ? 200 : 200);
        
        return response()->json($health, $httpStatus);
    }
    
    public function stats()
    {
        $stats = [
            'timestamp' => now()->toISOString(),
            'totals' => [
                'bookings_with_confirmation' => ReservationHotel::whereNotNull('confirmation_number')->count(),
                'details_fetched' => ReservationHotel::whereNotNull('booking_details_fetched_at')->count(),
                'pending_details' => ReservationHotel::whereNotNull('confirmation_number')
                    ->whereNull('booking_details_fetched_at')->count(),
            ],
            'last_24_hours' => [
                'new_bookings' => ReservationHotel::where('created_at', '>=', now()->subDay())->count(),
                'details_fetched' => ReservationHotel::where('booking_details_fetched_at', '>=', now()->subDay())->count(),
                'failed_jobs' => DB::table('failed_jobs')
                    ->where('payload', 'like', "%FetchBookingDetailsJob%")
                    ->where('failed_at', '>=', now()->subDay())
                    ->count(),
            ]
        ];
        
        // Calculate success rate
        if ($stats['totals']['bookings_with_confirmation'] > 0) {
            $stats['success_rate'] = round(
                ($stats['totals']['details_fetched'] / $stats['totals']['bookings_with_confirmation']) * 100, 
                2
            );
        } else {
            $stats['success_rate'] = 0;
        }
        
        return response()->json($stats);
    }
    
    public function bookingDetails($id)
    {
        $booking = ReservationHotel::find($id);
        
        if (!$booking) {
            return response()->json([
                'error' => 'Booking not found',
                'booking_id' => $id
            ], 404);
        }
        
        $status = [
            'booking_id' => $id,
            'booking_reference_id' => $booking->booking_reference_id,
            'confirmation_number' => $booking->confirmation_number,
            'created_at' => $booking->created_at->toISOString(),
            'booking_details_fetched_at' => $booking->booking_details_fetched_at?->toISOString(),
            'booking_status' => $booking->booking_status,
            'compliance' => [
                'has_booking_reference_id' => !is_null($booking->booking_reference_id),
                'has_details_fetched' => !is_null($booking->booking_details_fetched_at),
                'is_overdue' => false
            ]
        ];
        
        // Check if booking is overdue for details fetch (5+ minutes old with confirmation but no details)
        if ($booking->confirmation_number && 
            !$booking->booking_details_fetched_at && 
            $booking->created_at < now()->subMinutes(5)) {
            $status['compliance']['is_overdue'] = true;
        }
        
        return response()->json($status);
    }
    
    public function recentBookingsStatus()
    {
        $bookings = ReservationHotel::where('created_at', '>=', now()->subHours(24))
            ->orderBy('created_at', 'desc')
            ->get(['id', 'booking_reference_id', 'confirmation_number', 'created_at', 'booking_details_fetched_at', 'booking_status']);
        
        $summary = [
            'timestamp' => now()->toISOString(),
            'period' => 'last_24_hours',
            'total_bookings' => $bookings->count(),
            'compliance' => [
                'with_booking_reference_id' => $bookings->whereNotNull('booking_reference_id')->count(),
                'with_details_fetched' => $bookings->whereNotNull('booking_details_fetched_at')->count(),
                'overdue_for_details' => 0
            ],
            'bookings' => []
        ];
        
        foreach ($bookings as $booking) {
            $isOverdue = $booking->confirmation_number && 
                        !$booking->booking_details_fetched_at && 
                        $booking->created_at < now()->subMinutes(5);
            
            if ($isOverdue) {
                $summary['compliance']['overdue_for_details']++;
            }
            
            $summary['bookings'][] = [
                'id' => $booking->id,
                'booking_reference_id' => $booking->booking_reference_id,
                'confirmation_number' => $booking->confirmation_number,
                'created_at' => $booking->created_at->toISOString(),
                'booking_details_fetched_at' => $booking->booking_details_fetched_at?->toISOString(),
                'compliance' => [
                    'has_booking_reference_id' => !is_null($booking->booking_reference_id),
                    'has_details_fetched' => !is_null($booking->booking_details_fetched_at),
                    'is_overdue' => $isOverdue
                ]
            ];
        }
        
        return response()->json($summary);
    }
}
