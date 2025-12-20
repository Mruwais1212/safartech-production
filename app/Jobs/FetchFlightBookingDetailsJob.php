<?php

namespace App\Jobs;

use App\Models\ReservationFlight;
use App\Services\TBOFlightBookingService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class FetchFlightBookingDetailsJob implements ShouldQueue
{
    use Queueable;

    protected $flightReservationId;
    protected $pnr;
    protected $bookingId;
    protected $delayMinutes;

    /**
     * Create a new job instance.
     */
    public function __construct($flightReservationId, $pnr, $bookingId = null, $delayMinutes = 15)
    {
        $this->flightReservationId = $flightReservationId;
        $this->pnr = $pnr;
        $this->bookingId = $bookingId;
        $this->delayMinutes = $delayMinutes;
        
        // Set job to execute after specified delay (default 15 minutes for InProgress bookings)
        $this->delay(now()->addMinutes($this->delayMinutes));
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info("FetchFlightBookingDetailsJob: Starting job for flight reservation ID: {$this->flightReservationId}, PNR: {$this->pnr}");
        
        try {
            // Find the flight reservation
            $flight = ReservationFlight::find($this->flightReservationId);
            
            if (!$flight) {
                Log::error("FetchFlightBookingDetailsJob: Flight reservation not found with ID: {$this->flightReservationId}");
                return;
            }
            
            if (!$flight->pnr) {
                Log::error("FetchFlightBookingDetailsJob: No PNR found for flight reservation ID: {$this->flightReservationId}");
                return;
            }
            
            Log::info("FetchFlightBookingDetailsJob: Fetching booking details for PNR: {$this->pnr}, BookingId: {$this->bookingId}");
            
            // Call the TBO flight booking details API
            $bookingDetailsService = new TBOFlightBookingService();
            $bookingDetails = $bookingDetailsService->getBookingDetails($this->pnr, $this->bookingId);
            
            Log::info("FetchFlightBookingDetailsJob: Booking details response", ['response' => $bookingDetails]);
            
            // Update flight record based on booking details response
            if (isset($bookingDetails['status']) && $bookingDetails['status'] === 'success') {
                $this->updateFlightReservation($flight, $bookingDetails);
            } else {
                $this->handleBookingDetailsFailure($flight, $bookingDetails);
            }
            
        } catch (\Exception $e) {
            Log::error("FetchFlightBookingDetailsJob: Error processing flight booking details job", [
                'flight_reservation_id' => $this->flightReservationId,
                'pnr' => $this->pnr,
                'booking_id' => $this->bookingId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Optionally retry the job for temporary failures
            if ($this->attempts() < 3) {
                Log::info("FetchFlightBookingDetailsJob: Retrying job in 10 minutes (attempt {$this->attempts()}/3)");
                $this->release(600); // Retry in 10 minutes
            }
        }
    }

    /**
     * Update flight reservation with successful booking details
     */
    private function updateFlightReservation($flight, $bookingDetails)
    {
        try {
            $bookingData = $bookingDetails['booking_details'];
            
            $updateData = [
                'booking_details_fetched_at' => now(),
                'booking_details_response' => json_encode($bookingData),
                'booking_status_updated' => true
            ];
            
            // Update booking status based on TBO response
            if (isset($bookingDetails['booking_status'])) {
                $updateData['booking_status'] = $bookingDetails['booking_status'];
                
                // Map TBO booking status to our internal status
                $updateData['status'] = $this->mapTboStatusToInternal($bookingDetails['booking_status']);
            }
            
            // Update ticket status if available
            if (isset($bookingDetails['ticket_status'])) {
                $updateData['ticket_status'] = $bookingDetails['ticket_status'];
                
                // If ticket status shows confirmed, mark as ticketed
                if (in_array($bookingDetails['ticket_status'], [1, 'Confirmed', 'Ticketed'])) {
                    $updateData['is_ticketed'] = true;
                    $updateData['ticketed_at'] = now();
                }
            }
            
            // Update PNR if it changed (shouldn't happen but just in case)
            if (isset($bookingDetails['pnr']) && $bookingDetails['pnr'] !== $flight->pnr) {
                $updateData['pnr'] = $bookingDetails['pnr'];
                Log::warning("PNR changed during booking details fetch", [
                    'flight_id' => $flight->id,
                    'old_pnr' => $flight->pnr,
                    'new_pnr' => $bookingDetails['pnr']
                ]);
            }
            
            // Update booking ID if available and different
            if (isset($bookingDetails['booking_id']) && $bookingDetails['booking_id'] !== $this->bookingId) {
                $updateData['tbo_booking_id'] = $bookingDetails['booking_id'];
            }
            
            $flight->update($updateData);
            
            Log::info("FetchFlightBookingDetailsJob: Successfully updated flight reservation", [
                'flight_id' => $flight->id,
                'pnr' => $this->pnr,
                'booking_status' => $bookingDetails['booking_status'] ?? 'unknown',
                'ticket_status' => $bookingDetails['ticket_status'] ?? 'unknown',
                'internal_status' => $updateData['status'] ?? 'unchanged'
            ]);
            
            // Update related inbound/outbound flight if exists
            $this->updateRelatedFlight($flight, $updateData);
            
        } catch (\Exception $e) {
            Log::error("FetchFlightBookingDetailsJob: Error updating flight reservation", [
                'flight_id' => $flight->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Handle booking details fetch failure
     */
    private function handleBookingDetailsFailure($flight, $bookingDetails)
    {
        Log::warning("FetchFlightBookingDetailsJob: Failed to fetch booking details", [
            'flight_id' => $flight->id,
            'pnr' => $this->pnr,
            'booking_details' => $bookingDetails
        ]);
        
        $updateData = [
            'booking_details_fetched_at' => now(),
            'booking_details_response' => json_encode($bookingDetails),
            'booking_details_fetch_failed' => true
        ];
        
        // If it's been more than 30 minutes since booking and we still can't get details,
        // we might need to mark it as failed or needs manual review
        $bookingAge = $flight->created_at->diffInMinutes(now());
        if ($bookingAge > 30) {
            Log::warning("FetchFlightBookingDetailsJob: Booking details still unavailable after 30+ minutes", [
                'flight_id' => $flight->id,
                'pnr' => $this->pnr,
                'booking_age_minutes' => $bookingAge
            ]);
            
            $updateData['needs_manual_review'] = true;
        }
        
        $flight->update($updateData);
    }

    /**
     * Map TBO booking status to internal status codes
     */
    private function mapTboStatusToInternal($tboStatus)
    {
        // Map TBO status codes to our internal status
        // Adjust these mappings based on your internal status system
        $statusMap = [
            1 => 1,        // Confirmed -> Confirmed
            2 => 2,        // Cancelled -> Cancelled  
            3 => 3,        // Failed -> Failed
            4 => 4,        // Pending -> Pending
            5 => 5,        // InProgress -> InProgress
            6 => 1,        // Ticketed -> Confirmed (successful)
            'Confirmed' => 1,
            'Cancelled' => 2,
            'Failed' => 3,
            'Pending' => 4,
            'InProgress' => 5,
            'Ticketed' => 1
        ];
        
        return $statusMap[$tboStatus] ?? 5; // Default to InProgress if unknown
    }

    /**
     * Update related flight (inbound/outbound) with same data
     */
    private function updateRelatedFlight($flight, $updateData)
    {
        try {
            // Find related flight (inbound if this is outbound, outbound if this is inbound)
            $relatedFlight = null;
            
            if ($flight->outbound_id == 0) {
                // This is an outbound flight, find its inbound
                $relatedFlight = ReservationFlight::where('outbound_id', $flight->id)->first();
            } else {
                // This is an inbound flight, find its outbound
                $relatedFlight = ReservationFlight::find($flight->outbound_id);
            }
            
            if ($relatedFlight && $relatedFlight->pnr === $flight->pnr) {
                // Update related flight with same booking details (they share the same PNR)
                $relatedFlight->update($updateData);
                
                Log::info("FetchFlightBookingDetailsJob: Updated related flight", [
                    'original_flight_id' => $flight->id,
                    'related_flight_id' => $relatedFlight->id,
                    'shared_pnr' => $flight->pnr
                ]);
            }
            
        } catch (\Exception $e) {
            Log::error("FetchFlightBookingDetailsJob: Error updating related flight", [
                'flight_id' => $flight->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Handle job failure
     */
    public function failed(\Throwable $exception): void
    {
        Log::error("FetchFlightBookingDetailsJob: Job failed permanently", [
            'flight_reservation_id' => $this->flightReservationId,
            'pnr' => $this->pnr,
            'booking_id' => $this->bookingId,
            'exception' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString()
        ]);
        
        // Mark the flight as needing manual review
        try {
            $flight = ReservationFlight::find($this->flightReservationId);
            if ($flight) {
                $flight->update([
                    'needs_manual_review' => true,
                    'booking_details_fetch_failed' => true,
                    'last_fetch_error' => $exception->getMessage(),
                    'booking_details_fetched_at' => now()
                ]);
            }
        } catch (\Exception $e) {
            Log::error("FetchFlightBookingDetailsJob: Failed to mark flight for manual review", [
                'flight_id' => $this->flightReservationId,
                'error' => $e->getMessage()
            ]);
        }
    }
}