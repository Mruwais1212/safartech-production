<?php

namespace App\Http\Controllers\Website;

use App\Http\Requests\Website\PassengerInformationRequest;
use App\Services\TBOFlightBookingService;
use App\Support\LogRedactor;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class PassengerInformationController extends Controller
{
    public function __construct(Request $request)
    {
        parent::__construct($request);
    }

    public function index()
    {
        return view('website.passenger-information');
    }

    public function store(PassengerInformationRequest $request)
    {
        $passengers = [];

        $data = collect($request->except(['_token']))->map(function ($item, $label) use (&$passengers) {
            return collect($item)->map(function ($value, $key) use (&$passengers, $label) {
                return $passengers[$key][$label] = $value;
            })->toArray();
        });

        // Save passengers to session
        session(['passengers' => $passengers]);
        
        // Call SSR API to get available meal and baggage options
        $this->callSSR();
        
        // Set success message for modal handling
        session()->flash('passenger_success', true);

        // Check if this is an AJAX request
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Passengers saved successfully!',
                'passengers_count' => count($passengers)
            ]);
        }

        return redirect('/summary');
    }




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
                Log::info('SSR API response for outbound flight', LogRedactor::redact(['trace_id' => $traceId, 'response' => $ssrResponse]));
                
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
                Log::info('SSR API response for inbound flight', LogRedactor::redact(['trace_id' => $traceId, 'response' => $inboundSSRResponse]));
                
                // Parse the SSR response using the new method
                //$parsedInboundSSR = $tboService->parseSSRResponse($inboundSSRResponse);

                if ($inboundSSRResponse && $inboundSSRResponse['Response']['ResponseStatus'] == 1 ) {
                    $flight = session('flight', []);
                    $flight['InboundSSRDetails'] = $inboundSSRResponse['Response'];
                    session(['flight' => $flight]);
                    
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
                'trace_id' => $traceId ?? null,
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

    /**
     * Update passenger SSR (Special Service Request) data in session
     */
    public function updateSSRData(Request $request)
    {
        try {
            $passengerIndex = $request->input('passenger_index');
            $ssrType = $request->input('ssr_type'); // 'meal' or 'baggage'
            $ssrValue = $request->input('ssr_value');
            // ssr_price is intentionally NOT accepted from the client.
            // Price is looked up server-side from the session SSR catalog so the
            // client cannot inject an arbitrary amount that would be charged to Moyasar.
            $ssrPrice = $this->lookupSSRPrice($ssrType, $ssrValue);

            // Get current passengers from session
            $passengers = session('passengers', []);

            if (!isset($passengers[$passengerIndex])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Passenger not found'
                ], 404);
            }

            // Update the passenger with SSR data
            if ($ssrType === 'meal') {
                $passengers[$passengerIndex]['selected_meal'] = $ssrValue;
                $passengers[$passengerIndex]['meal_price'] = $ssrPrice;
            } elseif ($ssrType === 'baggage') {
                $passengers[$passengerIndex]['selected_baggage'] = $ssrValue;
                $passengers[$passengerIndex]['baggage_price'] = $ssrPrice;
            }

            // Update session
            session(['passengers' => $passengers]);

            Log::info("Updated passenger SSR data", [
                'passenger_index' => $passengerIndex,
                'ssr_type' => $ssrType,
                'ssr_value' => $ssrValue,
                'ssr_price' => $ssrPrice,
                'updated_passenger' => $passengers[$passengerIndex] ?? null
            ]);

            return response()->json([
                'success' => true,
                'message' => 'SSR data updated successfully',
                'passenger' => $passengers[$passengerIndex] ?? null
            ]);

        } catch (\Exception $e) {
            Log::error("Error updating passenger SSR data", [
                'error' => $e->getMessage(),
                'request_data' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error updating SSR data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Look up an SSR price from the server-side session SSR catalog.
     *
     * The TBO API returns a catalog of available meals and baggage options with
     * their prices in session('flight.SSRDetails') and session('flight.InboundSSRDetails').
     * We match the selected code against that catalog instead of trusting the
     * client-supplied price.
     *
     * Returns 0 if the code cannot be found in the catalog (selection will still
     * be stored, but at zero cost — the booking team can reconcile manually).
     */
    private function lookupSSRPrice(string $ssrType, ?string $code): float
    {
        if (empty($code) || $code === 'none') {
            return 0.0;
        }

        $flight = session('flight', []);
        $ssrSources = [
            $flight['SSRDetails'] ?? [],
            $flight['InboundSSRDetails'] ?? [],
        ];

        foreach ($ssrSources as $ssrData) {
            if (empty($ssrData)) {
                continue;
            }

            if ($ssrType === 'meal') {
                // MealDynamic: nested array [segmentIndex][mealIndex]
                $mealData = $ssrData['MealDynamic'] ?? [];
                foreach ($mealData as $segment) {
                    if (! is_array($segment)) {
                        continue;
                    }
                    foreach ($segment as $meal) {
                        if (($meal['Code'] ?? null) === $code) {
                            return (float) ($meal['Price'] ?? 0);
                        }
                    }
                }
                // Fallback: flat Meal array
                foreach ($ssrData['Meal'] ?? [] as $meal) {
                    if (($meal['Code'] ?? null) === $code) {
                        return (float) ($meal['Price'] ?? 0);
                    }
                }
            } elseif ($ssrType === 'baggage') {
                // Baggage: nested [segmentIndex][baggageIndex]
                $baggageData = $ssrData['Baggage'] ?? [];
                foreach ($baggageData as $segment) {
                    if (! is_array($segment)) {
                        continue;
                    }
                    foreach ($segment as $baggage) {
                        if (($baggage['Code'] ?? null) === $code) {
                            return (float) ($baggage['Price'] ?? 0);
                        }
                    }
                }
            }
        }

        Log::warning('SSR price lookup failed — code not found in session catalog', [
            'ssr_type' => $ssrType,
            'ssr_code' => $code,
        ]);

        return 0.0;
    }
}
