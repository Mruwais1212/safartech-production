<?php

namespace App\Jobs;

use App\Models\ReservationHotel;
use App\Services\TBOHotelBookingService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class FetchBookingDetailsJob implements ShouldQueue
{
    use Queueable;

    protected $hotelReservationId;
    protected $bookingReferenceId;

    /**
     * Create a new job instance.
     */
    public function __construct($hotelReservationId, $bookingReferenceId)
    {
        $this->hotelReservationId = $hotelReservationId;
        $this->bookingReferenceId = $bookingReferenceId;
        
        // Set job to execute after 120 seconds (2 minutes)
        $this->delay(now()->addSeconds(120));
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info("FetchBookingDetailsJob: Starting job for hotel reservation ID: {$this->hotelReservationId}, booking reference: {$this->bookingReferenceId}");
        
        try {
            // Find the hotel reservation
            $hotel = ReservationHotel::find($this->hotelReservationId);
            
            if (!$hotel) {
                Log::error("FetchBookingDetailsJob: Hotel reservation not found with ID: {$this->hotelReservationId}");
                return;
            }
            
            if (!$hotel->confirmation_number) {
                Log::error("FetchBookingDetailsJob: No confirmation number found for hotel reservation ID: {$this->hotelReservationId}");
                return;
            }
            
            Log::info("FetchBookingDetailsJob: Fetching booking details using booking reference ID: {$this->bookingReferenceId}");
            
            // Call the TBO booking details API using booking reference ID
            $bookingDetailsService = new TBOHotelBookingService();
            $bookingDetails = $bookingDetailsService->bookingDetails($hotel, $this->bookingReferenceId);
            
            Log::info("FetchBookingDetailsJob: Booking details response", ['response' => $bookingDetails]);
            
            // Update hotel record with any new information from booking details
            if (isset($bookingDetails['Status']['Code']) && $bookingDetails['Status']['Code'] == 200) {
                Log::info("FetchBookingDetailsJob: Successfully fetched booking details for booking reference: {$this->bookingReferenceId}");
                
                // You can update additional fields here based on the booking details response
                $updateData = [
                    'booking_details_fetched_at' => now(),
                    'booking_details_response' => json_encode($bookingDetails)
                ];
                
                // Update booking status if available
                if (isset($bookingDetails['BookingStatus'])) {
                    $updateData['booking_status'] = $bookingDetails['BookingStatus'];
                }
                
                $hotel->update($updateData);
                
                Log::info("FetchBookingDetailsJob: Updated hotel reservation with booking details for booking reference: {$this->bookingReferenceId}");
            } else {
                Log::error("FetchBookingDetailsJob: Failed to fetch booking details", [
                    'booking_reference_id' => $this->bookingReferenceId,
                    'confirmation_number' => $hotel->confirmation_number,
                    'response' => $bookingDetails
                ]);
            }
            
        } catch (\Exception $e) {
            Log::error("FetchBookingDetailsJob: Error processing booking details job", [
                'booking_reference_id' => $this->bookingReferenceId,
                'hotel_reservation_id' => $this->hotelReservationId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
}
