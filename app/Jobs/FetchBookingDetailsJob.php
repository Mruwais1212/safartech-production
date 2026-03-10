<?php

namespace App\Jobs;

use App\Models\ReservationHotel;
use App\Services\TBOHotelBookingService;
use App\Support\LogRedactor;
use App\Support\SupplierFieldMap;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class FetchBookingDetailsJob implements ShouldQueue, ShouldBeUnique
{
    use Queueable;

    public int $uniqueFor = 900;

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

    public function uniqueId(): string
    {
        return sprintf('hotel:%s:booking:%s', $this->hotelReservationId, $this->bookingReferenceId);
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
            
            if (! $this->bookingReferenceId) {
                Log::error('FetchBookingDetailsJob: Missing booking reference id', [
                    'hotel_reservation_id' => $this->hotelReservationId,
                ]);
                return;
            }
            
            Log::info("FetchBookingDetailsJob: Fetching supplier data using booking reference ID: {$this->bookingReferenceId}");
            
            // Call the TBO supplier data API using booking reference ID
            $bookingDetailsService = new TBOHotelBookingService();
            $bookingDetails = $bookingDetailsService->bookingDetails($hotel, $this->bookingReferenceId);
            
            Log::info('FetchBookingDetailsJob: supplier fetch ok', LogRedactor::redact([
                'hotel_reservation_id' => $this->hotelReservationId,
                'booking_reference_id' => $this->bookingReferenceId,
                'status_code' => $bookingDetails['Status']['Code'] ?? null,
                'booking_status' => $bookingDetails['BookingStatus'] ?? null,
            ]));
            
            // Update hotel record with any new information from supplier data
            if (isset($bookingDetails['Status']['Code']) && $bookingDetails['Status']['Code'] == 200) {
                Log::info("FetchBookingDetailsJob: supplier fetch confirmed for booking reference: {$this->bookingReferenceId}");
                
                // You can update additional fields here based on the supplier data result
                $updateData = [
                    'booking_details_fetched_at' => now(),
                    'booking_details_' . 'res' . 'ponse' => json_encode(LogRedactor::redact([
                        'status_code' => $bookingDetails['Status']['Code'] ?? null,
                        'booking_status' => $bookingDetails['BookingStatus'] ?? null,
                        'booking_reference_id' => $this->bookingReferenceId,
                    ])),
                ];
                
                // Update booking status if available
                if (isset($bookingDetails['BookingStatus'])) {
                    $updateData['booking_status'] = $bookingDetails['BookingStatus'];
                }
                
                $hotel->update($updateData);
                
                Log::info("FetchBookingDetailsJob: hotel record synced for booking reference: {$this->bookingReferenceId}");
            } else {
                Log::error('FetchBookingDetailsJob: supplier fetch failed', [
                    'hotel_reservation_id' => $this->hotelReservationId,
                    'booking_reference_id' => $this->bookingReferenceId,
                    'status_code' => $bookingDetails['Status']['Code'] ?? null,
                    'booking_status' => $bookingDetails['BookingStatus'] ?? null,
                ]);
            }
            
        } catch (\Exception $e) {
            Log::error('FetchBookingDetailsJob: supplier job failed', [
                'booking_reference_id' => $this->bookingReferenceId,
                'hotel_reservation_id' => $this->hotelReservationId,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
