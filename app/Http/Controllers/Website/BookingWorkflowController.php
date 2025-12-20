<?php

namespace App\Http\Controllers\Website;

use App\Services\AffiliateService;
use Illuminate\Http\Request;

class BookingWorkflowController extends Controller
{
    protected $tboAuthController;

    protected $tboFlightSearchController;

    protected $tboFareQuoteController;

    protected $tboBookingController;

    protected $gptController;

    protected $affiliateService;

    public function __construct(
        TBOFlightAuthController $tboAuthController,
        TBOFlightSearchController $tboFlightSearchController,
        TBOFlightFareQuoteController $tboFareQuoteController,
        TBOFlightBookingController $tboBookingController,
        DestinationController $gptController,
        AffiliateService $affiliateService
    ) {
        $this->tboAuthController = $tboAuthController;
        $this->tboFlightSearchController = $tboFlightSearchController;
        $this->tboFareQuoteController = $tboFareQuoteController;
        $this->tboBookingController = $tboBookingController;
        $this->gptController = $gptController;
        $this->affiliateService = $affiliateService;
    }

    public function getPreferences(Request $request)
    {
        $preferences = $request->validate([
            'departure_city' => 'required',
            'destination' => 'required',
            'dates' => 'required',
            'budget' => 'required',
            'interests' => 'required',
        ]);

        session(['preferences' => $preferences]);

        return response()->json(['message' => 'Preferences saved successfully']);
    }

    public function generateDestinations()
    {
        $preferences = session('preferences');
        $destinations = $this->gptController->generateDestinations($preferences);

        session(['suggested_destinations' => $destinations]);

        return response()->json(['destinations' => $destinations]);
    }

    public function selectDestination(Request $request)
    {
        $destination = $request->validate(['destination' => 'required'])['destination'];
        session(['selected_destination' => $destination]);

        return response()->json(['message' => 'Destination selected successfully']);
    }

    public function checkWalletBalance()
    {
        $balance = $this->tboAuthController->getAgencyBalance();
        $threshold = config('services.tbo.threshold');

        if ($balance < $threshold) {
            $this->switchToAffiliate();

            return response()->json(['message' => 'Switched to affiliate due to low balance', 'balance' => $balance]);
        }

        return response()->json(['message' => 'Sufficient balance', 'balance' => $balance]);
    }

    public function searchInventory()
    {
        $destination = session('selected_destination');
        $dates = session('preferences')['dates'];

        if (session('use_affiliate', false)) {
            $flights = $this->affiliateService->searchFlights($destination, $dates);
            $hotels = $this->affiliateService->searchHotels($destination, $dates);
        } else {
            $flights = $this->tboFlightSearchController->search($destination);
            $hotels = $this->searchTBOHotels($destination, $dates);
        }

        session(['available_flights' => $flights, 'available_hotels' => $hotels]);

        return response()->json([
            'flights' => $flights,
            'hotels' => $hotels,
        ]);
    }

    public function selectFlightAndHotel(Request $request)
    {
        $validated = $request->validate([
            'flight_id' => 'required',
            'hotel_id' => 'required',
        ]);

        $selectedFlight = collect(session('available_flights'))->firstWhere('id', $validated['flight_id']);
        $selectedHotel = collect(session('available_hotels'))->firstWhere('id', $validated['hotel_id']);

        session(['selected_flight' => $selectedFlight, 'selected_hotel' => $selectedHotel]);

        return response()->json(['message' => 'Flight and hotel selected successfully']);
    }

    public function getSummary()
    {
        $flight = session('selected_flight');
        $hotel = session('hotel.selected_hotel');

        if (session('use_affiliate', false)) {
            return view('summary-page', compact('flight', 'hotel', 'affiliateFlightLink', 'affiliateHotelLink'));
        }

        return view('summary-page', compact('flight', 'hotel'));
    }

    public function processPayment(Request $request)
    {
        if (session('use_affiliate', false)) {
            return $this->redirectToAffiliate();
        }
        // Process TBO booking
        $bookingResponse = $this->tboBookingController->bookFlight($request);
        if ($bookingResponse['success']) {
            return $this->redirectToPaymentGateway($bookingResponse['data']['BookingId']);
        }

        return response()->json(['error' => 'Booking failed'], 400);
    }

    public function getConfirmation(Request $request)
    {
        if (session('use_affiliate', false)) {
            return view('confirmation-page', ['message' => 'Booking completed via affiliate']);
        }
        $pnr = $request->query('pnr');
        $hotelBookingReference = $request->query('hotel_reference');

        return view('confirmation-page', compact('pnr', 'hotelBookingReference'));
    }

    protected function switchToAffiliate()
    {
        session(['use_affiliate' => true]);
    }

    protected function redirectToPaymentGateway($bookingId)
    {
        // Implement Moyasar payment gateway integration
        return redirect()->away('https://moyasar.com/checkout?booking='.$bookingId);
    }

    protected function redirectToAffiliate()
    {
        $affiliateFlightLink = session('selected_flight')['affiliate_link'];
        $affiliateHotelLink = session('hotel.selected_hotel')['affiliate_link'];

        return response()->json([
            'flight_link' => $affiliateFlightLink,
            'hotel_link' => $affiliateHotelLink,
        ]);
    }
}
