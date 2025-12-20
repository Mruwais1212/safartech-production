<?php

namespace App\Http\Controllers\Website;

use App\Helpers\PriceCalculationHelper;
use App\Models\Reservation;
use App\Services\MoyasarPaymentService;
use App\Services\ReservationService;
use App\Services\TBOFlightBookingService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Log;
use App\Mail\BookingEmail;
use App\Models\Airport;
use App\Models\TransactionCard;
use App\Traits\PriceCalculationTrait;
use Illuminate\Support\Facades\Mail;

class PaymentController extends Controller implements HasMiddleware
{
    use PriceCalculationTrait;
    public function __construct(Request $request)
    {
        parent::__construct($request);
    }

    public static function middleware()
    {
        return [new Middleware(['auth:web', 'web'], except: ['summary'])];
    }

    public function summary()
    {
        $passengers = session('passengers');

        if (! session('preferences') || (! session('hotel') && ! session('flight'))) {
            return redirect('/ai-trip')->with('error', 'Please Your Trip Plan First');
        }

        if (! $passengers) {
            $passengers = [];
        }

        $passengers = collect($passengers)->map(function ($passenger) {
            return (object) $passenger;
        });

        $searchParams = session('searchParams');
        $preferences = session('preferences');
        $flight = session('flight');
        $hotel = session('hotel');

        $airPortFrom = isset($searchParams['origin']) ? Airport::find($searchParams['origin']) : null;
        $airPortTo = isset($searchParams['destination']) ? Airport::find($searchParams['destination']) : null;
        $airPortToo = isset($preferences['destination']) ? Airport::where('city_code', $preferences['destination'])->first() : null;

        $reservationType = 'inside';

        foreach ([$airPortFrom, $airPortTo, $airPortToo] as $airport) {
            if ($airport && $airport->country_code !== 'SA') {
                $reservationType = 'outside';
                break;
            }
        }

        $tripType = $preferences['trip_type'] ?? null;

        $flightAmount = $flight['flight_amount'] ?? 0;
        $flightAmount = PriceCalculationHelper::calculatePriceAmounts($flightAmount, $reservationType,'flight');
        $inboundAmount = session()->has('flight.inbound_flightSegment') ? $flight['inbound_flight_amount'] ?? 0 : 0;
        $inboundAmount = PriceCalculationHelper::calculatePriceAmounts($inboundAmount, $reservationType,'flight');

        $hotelAmount = $hotel['TotalFareHotel'] ?? 0;
        $hotelAmount = PriceCalculationHelper::calculatePriceAmounts($hotelAmount, $reservationType,'hotel');

        $totalBasePrice = match ($tripType) {
            1 =>PriceCalculationHelper::mergeCalculatePrices([$flightAmount , $inboundAmount , $hotelAmount]) ,
            2 => $hotelAmount,
            3 => PriceCalculationHelper::mergeCalculatePrices([$flightAmount + $inboundAmount]),
            default => 0,
        };

        $price = $totalBasePrice;
//Log::info($price);

        // Fetch real SSR data from session (stored by PassengerInformationController)
        $ssrData = [];
        if (in_array($tripType, [1, 3]) && $flight) {
            
            // Call SSR API to get available meal and baggage options
        $this->callFlightAPIs();
        
            // Get SSR data from session instead of making new API calls
            $ssrData = [
                'outbound' => $flight['SSRDetails'] ?? [],
                'inbound' => $flight['InboundSSRDetails'] ?? []
            ];
            
            // Check if SSR data is empty (could be due to expired session)
            $hasSSRData = !empty($ssrData['outbound']) || !empty($ssrData['inbound']);
            
            Log::info('SSR data retrieved from session for summary page', [
                'has_outbound' => !empty($ssrData['outbound']),
                'has_inbound' => !empty($ssrData['inbound']),
                'has_any_ssr_data' => $hasSSRData,
                'session_expired_detected' => !$hasSSRData && !empty($traceId)
            ]);
            
            // If no SSR data available (possibly due to expired session), 
            // we'll rely on fallback data in the view
            if (!$hasSSRData) {
                Log::warning('No SSR data available - possibly due to expired TraceId session');
            }
        }

        return view('website.summary', [
            'passengers'=> $passengers,
            'totalPrice' =>(float) number_format($price['total_price'], 2, '.', ''),
            'currency' => __('dashboard.SAR'),
            'tripType' => $tripType,
            'ssrData' => $ssrData, // Pass real SSR data to view
        ]);
    }


    private function callFlightAPIs()
    {
        try {
            $tboService = new TBOFlightBookingService();
            
            // Get current flight session data
            $traceId = session('traceId');
            $outboundResultIndex = session('flight.flightResult');
            $inboundResultIndex = session('flight.inbound_flightResult');
            
            if (!$traceId) {
                Log::warning('No traceId found in session for FareQuote/FareRules API calls');
                return;
            }

            // Process outbound flight APIs
            if ($outboundResultIndex) {
                Log::info('Calling FareQuote and FareRules APIs for outbound flight', [
                    'traceId' => $traceId,
                    'resultIndex' => $outboundResultIndex
                ]);

                // Call FareQuote API for outbound flight with fresh call (not cached)
                $fareQuoteResponse = $tboService->fareQuote($traceId, $outboundResultIndex);
                Log::info('FareQuote API response for outbound flight', $fareQuoteResponse);
                
                if (isset($fareQuoteResponse['Response']['Results'])) {
                    $flight = session('flight', []);
                    $flight['FareQuote'] = $fareQuoteResponse['Response']['Results'];
                    session(['flight' => $flight]);
                    Log::info('Successfully stored outbound FareQuote in flight session');
                } elseif (isset($fareQuoteResponse['Response']['ResponseStatus']) && $fareQuoteResponse['Response']['ResponseStatus'] == 1) {
                    // Sometimes the Results are structured differently
                    $flight = session('flight', []);
                    $flight['FareQuote'] = $fareQuoteResponse['Response'];
                    session(['flight' => $flight]);
                    Log::info('Successfully stored outbound FareQuote (alternative structure) in flight session');
                } else {
                    Log::warning('No FareQuote data returned for outbound flight', $fareQuoteResponse);
                }

                // Call FareRules API for outbound flight
                $fareRulesResponse = $tboService->fareRules($traceId, $outboundResultIndex);
                Log::info('FareRules API response for outbound flight', $fareRulesResponse);
                
                if (isset($fareRulesResponse['Response']['FareRules'])) {
                    $flight = session('flight', []);
                    $flight['FareRules'] = $fareRulesResponse['Response']['FareRules'];
                    session(['flight' => $flight]);
                    Log::info('Successfully stored outbound FareRules in flight session');
                } elseif (isset($fareRulesResponse['Response']['FareRule'])) {
                    $flight = session('flight', []);
                    $flight['FareRules'] = $fareRulesResponse['Response']['FareRule'];
                    session(['flight' => $flight]);
                    Log::info('Successfully stored outbound FareRule in flight session');
                } else {
                    Log::warning('No FareRules data returned for outbound flight', $fareRulesResponse);
                }
            }

            // Process inbound flight APIs if exists
            if ($inboundResultIndex) {
                Log::info('Calling FareQuote and FareRules APIs for inbound flight', [
                    'traceId' => $traceId,
                    'resultIndex' => $inboundResultIndex
                ]);

                // Call FareQuote API for inbound flight
                $inboundFareQuoteResponse = $tboService->fareQuote($traceId, $inboundResultIndex);
                Log::info('FareQuote API response for inbound flight', $inboundFareQuoteResponse);
                
                if (isset($inboundFareQuoteResponse['Response']['Results'])) {
                    $flight = session('flight', []);
                    $flight['InboundFareQuote'] = $inboundFareQuoteResponse['Response']['Results'];
                    session(['flight' => $flight]);
                    Log::info('Successfully stored inbound FareQuote in flight session');
                } elseif (isset($inboundFareQuoteResponse['Response']['ResponseStatus']) && $inboundFareQuoteResponse['Response']['ResponseStatus'] == 1) {
                    $flight = session('flight', []);
                    $flight['InboundFareQuote'] = $inboundFareQuoteResponse['Response'];
                    session(['flight' => $flight]);
                    Log::info('Successfully stored inbound FareQuote (alternative structure) in flight session');
                } else {
                    Log::warning('No FareQuote data returned for inbound flight', $inboundFareQuoteResponse);
                }

                // Call FareRules API for inbound flight
                $inboundFareRulesResponse = $tboService->fareRules($traceId, $inboundResultIndex);
                Log::info('FareRules API response for inbound flight', $inboundFareRulesResponse);
                
                if (isset($inboundFareRulesResponse['Response']['FareRules'])) {
                    $flight = session('flight', []);
                    $flight['InboundFareRules'] = $inboundFareRulesResponse['Response']['FareRules'];
                    session(['flight' => $flight]);
                    Log::info('Successfully stored inbound FareRules in flight session');
                } elseif (isset($inboundFareRulesResponse['Response']['FareRule'])) {
                    $flight = session('flight', []);
                    $flight['InboundFareRules'] = $inboundFareRulesResponse['Response']['FareRule'];
                    session(['flight' => $flight]);
                    Log::info('Successfully stored inbound FareRule in flight session');
                } else {
                    Log::warning('No FareRules data returned for inbound flight', $inboundFareRulesResponse);
                }
            }

            $this->callSSR();

        } catch (\Exception $e) {
            Log::error('Error calling TBO Flight APIs from passenger page', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
    public function payment(Request $request)
    {
        // Calculate SSR cost from passenger session data
        $passengers = session('passengers', []);
        $totalSSRCost = 0;
        $ssrSummary = [];
        
        foreach ($passengers as $index => $passenger) {
            $mealPrice = $passenger['meal_price'] ?? 0;
            $baggagePrice = $passenger['baggage_price'] ?? 0;
            $passengerSSRCost = $mealPrice + $baggagePrice;
            
            if ($passengerSSRCost > 0) {
                $ssrSummary[$index] = [
                    'name' => ($passenger['first_name'] ?? '') . ' ' . ($passenger['last_name'] ?? ''),
                    'meal' => $passenger['selected_meal'] ?? 'none',
                    'baggage' => $passenger['selected_baggage'] ?? 'none',
                    'meal_price' => $mealPrice,
                    'baggage_price' => $baggagePrice,
                    'total' => $passengerSSRCost
                ];
            }
            
            $totalSSRCost += $passengerSSRCost;
        }

        // Get final total from request or calculate it
        $finalTotalPrice = (float) $request->input('final_total_price', 0);
        if ($finalTotalPrice <= 0) {
            // Calculate final total if not provided using existing summary logic
            $flight = session('flight', []);
            $hotel = session('hotel', []);
            $tripType = session('preferences.trip_type', 3);
            
            $flightAmount = $flight['flight_amount'] ?? 0;
            $inboundAmount = session()->has('flight.inbound_flightSegment') ? $flight['inbound_flight_amount'] ?? 0 : 0;
            $hotelAmount = $hotel['TotalFareHotel'] ?? 0;
            
            $baseTotalPrice = match ($tripType) {
                1 => $flightAmount + $inboundAmount + $hotelAmount,
                2 => $hotelAmount,
                3 => $flightAmount + $inboundAmount,
                default => 0,
            };
            
            $finalTotalPrice = $baseTotalPrice + $totalSSRCost;
        }

        // Store SSR summary for logging and reference
        if (!empty($ssrSummary)) {
            session(['ssr_summary' => $ssrSummary]);
            session(['total_ssr_cost' => $totalSSRCost]);
            session(['final_total_price' => $finalTotalPrice]);
            
            Log::info('SSR data calculated from passenger session', [
                'ssr_summary' => $ssrSummary,
                'total_ssr_cost' => $totalSSRCost,
                'final_total_price' => $finalTotalPrice
            ]);
        }

        $reservation = ReservationService::add();

        if (! $reservation) {
            return redirect()->back()->with('error', 'Something went wrong');
        }
        
        // Use the final total price if SSR is included, otherwise use reservation price
        $price = $finalTotalPrice > 0 ? $finalTotalPrice : $reservation->price;
        $currency = $reservation->currency ?: 'SAR';

        $invoice = MoyasarPaymentService::createInvoice([
            'amount' => $price,
            'currency' => $currency,
            'description' => 'test invoice',
            'callback_url' => url('moyasar-callback'),
            'success_url' => url('moyasar-success'),
            'metadata' => collect([
                'reservation_id' => $reservation->id,
                'user_id' => auth('web')->id(),
            ]),
        ]);
        
        // Check if there was an error in invoice creation
        if (array_key_exists('error', $invoice)) {
            //Log::error('Moyasar invoice creation failed', $invoice);
            return back()->with('error', 'Payment service error: ' . ($invoice['message'] ?? 'Unknown error'));
        }
        
        // Handle successful invoice creation
        if (array_key_exists('status', $invoice) && $invoice['status'] == 'initiated') {
            $reservation->update(['payment_id' => $invoice['id']]);

            Log::info($invoice['id']);

            return redirect($invoice['url']);
        }

        return back()->with('error', 'Something went wrong');
    }

    public function success()
    {
        return view('website.success');
    }

    public function moyasarCallback(Request $request)
    {
        $payment = MoyasarPaymentService::getInvoice($request->id);

        // Check if there was an error getting the payment
        if (array_key_exists('error', $payment)) {
            Log::error('Moyasar payment retrieval failed in callback', $payment);
            return view('website.error');
        }

        if (isset($payment['status']) && $payment['status'] == 'paid') {
            $reservation = Reservation::where('payment_id', $payment['invoice_id'])->first();

            return view('website.success', compact('reservation'));
        }

        return view('website.error');
    }

    //  private function callSSR()
    // {
    //     try {
    //         $tboService = new TBOFlightBookingService();
            
    //         // Get current flight session data
    //         $traceId = session('outbound_flight.TraceId') ? session('outbound_flight.TraceId') : session('inbound_flight.TraceId');
    //         $outboundResultIndex = session('flight.flightResult');
    //         $inboundResultIndex = session('flight.inbound_flightResult');
            
    //         if (!$traceId) {
    //             Log::warning('No traceId found in session for SSR API calls');
    //             return;
    //         }
    //         Log::info('Starting SSR API calls', ['traceId' => $traceId, 'outboundResultIndex' => $outboundResultIndex, 'inboundResultIndex' => $inboundResultIndex]);
    //         // Call SSR API for outbound flight
    //         if ($outboundResultIndex) {
    //             Log::info('Calling SSR API for outbound flight', [
    //                 'traceId' => $traceId,
    //                 'resultIndex' => $outboundResultIndex
    //             ]);

    //             $ssrResponse = $tboService->ssr($traceId, $outboundResultIndex);
    //             Log::info('SSR API response for outbound flight', $ssrResponse);
                
    //             // Check for expired session or other errors
    //             if (isset($ssrResponse['Response']['ResponseStatus']) && $ssrResponse['Response']['ResponseStatus'] == 4) {
    //                 Log::warning('TraceId expired for outbound SSR call', [
    //                     'error' => $ssrResponse['Response']['Error'] ?? 'Unknown error'
    //                 ]);
    //                 // Set empty SSR data but don't fail completely
    //                 $flight = session('flight', []);
    //                 $flight['SSRDetails'] = [];
    //                 session(['flight' => $flight]);
    //             } elseif (isset($ssrResponse['Response'])) {
    //                 $flight = session('flight', []);
    //                 $flight['SSRDetails'] = $ssrResponse['Response'];
    //                 session(['flight' => $flight]);
    //                 Log::info('Successfully stored outbound SSR details in flight session');
    //             } else {
    //                 Log::warning('No SSR data returned for outbound flight', $ssrResponse);
    //                 // Set empty SSR data as fallback
    //                 $flight = session('flight', []);
    //                 $flight['SSRDetails'] = [];
    //                 session(['flight' => $flight]);
    //             }
    //         }

    //         // Call SSR API for inbound flight if exists
    //         if ($inboundResultIndex) {
    //             Log::info('Calling SSR API for inbound flight', [
    //                 'traceId' => $traceId,
    //                 'resultIndex' => $inboundResultIndex
    //             ]);

    //             $inboundSSRResponse = $tboService->ssr($traceId, $inboundResultIndex);
    //             Log::info('SSR API response for inbound flight', $inboundSSRResponse);
                
    //             // Check for expired session or other errors
    //             if (isset($inboundSSRResponse['Response']['ResponseStatus']) && $inboundSSRResponse['Response']['ResponseStatus'] == 4) {
    //                 Log::warning('TraceId expired for inbound SSR call', [
    //                     'error' => $inboundSSRResponse['Response']['Error'] ?? 'Unknown error'
    //                 ]);
    //                 // Set empty SSR data but don't fail completely
    //                 $flight = session('flight', []);
    //                 $flight['InboundSSRDetails'] = [];
    //                 session(['flight' => $flight]);
    //             } elseif (isset($inboundSSRResponse['Response'])) {
    //                 $flight = session('flight', []);
    //                 $flight['InboundSSRDetails'] = $inboundSSRResponse['Response'];
    //                 session(['flight' => $flight]);
    //                 Log::info('Successfully stored inbound SSR details in flight session');
    //             } else {
    //                 Log::warning('No SSR data returned for inbound flight', $inboundSSRResponse);
    //                 // Set empty SSR data as fallback
    //                 $flight = session('flight', []);
    //                 $flight['InboundSSRDetails'] = [];
    //                 session(['flight' => $flight]);
    //             }
    //         }

    //     } catch (\Exception $e) {
    //         Log::error('Error calling SSR API from passenger page', [
    //             'error' => $e->getMessage(),
    //             'trace' => $e->getTraceAsString()
    //         ]);
            
    //         // Set empty SSR data as fallback to prevent page crashes
    //         $flight = session('flight', []);
    //         $flight['SSRDetails'] = [];
    //         $flight['InboundSSRDetails'] = [];
    //         session(['flight' => $flight]);
    //     }
    // }

    private function callSSR()
    {
        try {
            $tboService = new TBOFlightBookingService();
            
            // Get current flight session data
            $traceId = session('traceId');
            $outboundResultIndex = session('flight.flightResult');
            $inboundResultIndex = session('flight.inbound_flightResult');
            
            if (!$traceId) {
                Log::warning('No traceId found in session for SSR API calls');
                return;
            }
            Log::info('Starting SSR API calls', ['traceId' => $traceId, 'outboundResultIndex' => $outboundResultIndex, 'inboundResultIndex' => $inboundResultIndex]);
            // Call SSR API for outbound flight
            if ($outboundResultIndex) {
                Log::info('Calling SSR API for outbound flight', [
                    'traceId' => $traceId,
                    'resultIndex' => $outboundResultIndex
                ]);

                $ssrResponse = $tboService->ssr($traceId, $outboundResultIndex);
                Log::info('SSR API response for outbound flight', $ssrResponse);
                
                // Parse the SSR response using the new method
                //$parsedSSR = $tboService->parseSSRResponse($ssrResponse);
                
                if ($ssrResponse && $ssrResponse['Response']['ResponseStatus'] == 1 ) {
                    $flight = session('flight', []);
                    $flight['SSRDetails'] = $ssrResponse['Response'];
                    session(['flight' => $flight]);
                    // Log::info('Successfully stored parsed outbound SSR details in flight session', [
                    //     'is_lcc' => 0,//$ssrResponse['Response']['is_lcc'],
                    //     'meals_count' => count($ssrResponse['Response']['meals']),
                    //     'baggage_count' => count($ssrResponse['Response']['Baggage'])
                    // ]);
                } else {
                    Log::warning('Failed to parse outbound SSR response');
                    // Set empty SSR data as fallback
                    $flight = session('flight', []);
                    $flight['SSRDetails'] = ['is_lcc' => false, 'MealDynamic' => [], 'Baggage' => [], 'seats' => []];
                    session(['flight' => $flight]);
                }
            }

            // Call SSR API for inbound flight if exists
            if ($inboundResultIndex) {
                Log::info('Calling SSR API for inbound flight', [
                    'traceId' => $traceId,
                    'resultIndex' => $inboundResultIndex
                ]);

                $inboundSSRResponse = $tboService->ssr($traceId, $inboundResultIndex);
                Log::info('SSR API response for inbound flight', $inboundSSRResponse);
                
                // Parse the SSR response using the new method
                //$parsedInboundSSR = $tboService->parseSSRResponse($inboundSSRResponse);

                if ($inboundSSRResponse && $inboundSSRResponse['Response']['ResponseStatus'] == 1 ) {
                    $flight = session('flight', []);
                    $flight['InboundSSRDetails'] = $inboundSSRResponse['Response'];
                    session(['flight' => $flight]);
                    Log::info('Successfully stored parsed inbound SSR details in flight session', [
                        'is_lcc' => $inboundSSRResponse['Response']['is_lcc'],
                        'meals_count' => count($inboundSSRResponse['Response']['meals']),
                        'baggage_count' => count($inboundSSRResponse['Response']['Baggage'])
                    ]);
                } else {
                    Log::warning('Failed to parse inbound SSR response');
                    // Set empty SSR data as fallback
                    $flight = session('flight', []);
                    $flight['InboundSSRDetails'] = ['is_lcc' => false, 'MealDynamic' => [], 'Baggage' => [], 'seats' => []];
                    session(['flight' => $flight]);
                }
            }

        } catch (\Exception $e) {
            Log::error('Error calling SSR API from passenger page', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Set empty SSR data as fallback to prevent page crashes
            $flight = session('flight', []);
            $flight['SSRDetails'] = ['is_lcc' => false, 'MealDynamic' => [], 'Baggage' => [], 'Seats' => []];
            $flight['InboundSSRDetails'] = ['is_lcc' => false, 'MealDynamic' => [], 'Baggage' => [], 'Seats' => []];
            session(['flight' => $flight]);
        }
    }

    /**
     * Get SSR data from session for use in views
     */
    public function getSSRData()
    {
        $flight = session('flight', []);
        
        $ssrData = [
            'outbound_ssr' => $flight['SSRDetails'] ?? [],
            'inbound_ssr' => $flight['InboundSSRDetails'] ?? []
        ];

        // If this is an AJAX request, return JSON
        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'data' => $ssrData
            ]);
        }

        return $ssrData;
    }
    
    public function moyasarSuccess(Request $request)
    {
        $payment = MoyasarPaymentService::getInvoice($request->id);

        // Check if there was an error getting the payment
        if (array_key_exists('error', $payment)) {
            Log::error('Moyasar payment retrieval failed in success', $payment);
            return view('website.error');
        }

        if (isset($payment['status']) && $payment['status'] == 'paid') {
            $reservation = Reservation::where('payment_id', $payment['invoice_id'])
                ->where('status', 0)
                ->where('payment_method', 0)
                ->first();
            if (!$reservation) {
               return redirect(url('/'));
            }
            if ($reservation) {
                try {
                    // Complete booking and get the result
                    $bookingResult = ReservationService::completeBooking($reservation->id);
                    
                    // Check if booking/ticketing was successful
                    if (!$bookingResult['success']) {
                    Log::error('Booking/Ticketing failed for reservation ID: ' . $reservation->id . ' after successful payment', [
                        'errors' => $bookingResult['errors'],
                        'payment_id' => $payment['invoice_id'],
                        'reservation_id' => $reservation->id
                    ]);
                    
                    // Update reservation status to indicate booking failure but payment success
                    $reservation->update([
                        'status' => 2, // Using status 2 to indicate payment success but booking failure
                        'payment_method' => 1, 
                        'payment_type' => $payment['source']['company'], 
                        'paid_at' => now(),
                        'booking_error' => implode('; ', $bookingResult['errors'])
                    ]);
                    
                    // Still save transaction card info since payment was successful
                    $transaction_card = new TransactionCard();
                    $transaction_card->company = $payment['source']['company'];
                    $transaction_card->number = $payment['source']['number'];
                    $transaction_card->name = $payment['source']['name'];
                    $transaction_card->reference_number = $payment['source']['reference_number'];
                    $transaction_card->reservation_id = $reservation->id;
                    $transaction_card->save();
                    
                    // Redirect to error page with booking failure message
                    return redirect('/booking-error')->with('error_message', 'Payment was successful but booking failed. Please contact support with your reservation ID: ' . $reservation->id);
                }
                
                // If booking was successful, proceed with normal flow
                $reservation->update(['status' => 1, 'payment_method' => 1, 'payment_type' => $payment['source']['company'], 'paid_at' => now()]);
                $transaction_card = new TransactionCard();
                $transaction_card->company = $payment['source']['company'];
                $transaction_card->number = $payment['source']['number'];
                $transaction_card->name = $payment['source']['name'];
                $transaction_card->reference_number = $payment['source']['reference_number'];
                $transaction_card->reservation_id = $reservation->id;
                $transaction_card->save();
                Mail::to($reservation->user->email)->send(new BookingEmail($reservation));
                } catch (\Exception $e) {
                    Log::error('Exception occurred during booking completion for reservation ID: ' . $reservation->id, [
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString(),
                        'payment_id' => $payment['invoice_id']
                    ]);
                    
                    // Update reservation to indicate exception during booking
                    $reservation->update([
                        'status' => 2, // Payment success but booking exception
                        'payment_method' => 1,
                        'payment_type' => $payment['source']['company'],
                        'paid_at' => now(),
                        'booking_error' => 'Exception during booking: ' . $e->getMessage()
                    ]);
                    
                    // Still save transaction card info since payment was successful
                    $transaction_card = new TransactionCard();
                    $transaction_card->company = $payment['source']['company'];
                    $transaction_card->number = $payment['source']['number'];
                    $transaction_card->name = $payment['source']['name'];
                    $transaction_card->reference_number = $payment['source']['reference_number'];
                    $transaction_card->reservation_id = $reservation->id;
                    $transaction_card->save();
                    
                    // Redirect to error page
                    return redirect('/booking-error')->with('error_message', 'Payment was successful but an error occurred during booking. Please contact support with your reservation ID: ' . $reservation->id);
                }
            }

            return view('website.success', compact('reservation'));
        }

        return view('website.error');
    }
    
    public function bookingError(Request $request)
    {
        // This route is specifically for booking/ticketing errors
        return view('website.error');
    }
}
