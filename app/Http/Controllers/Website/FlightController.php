<?php

namespace App\Http\Controllers\Website;

use App\Models\ReservationFlight;
use App\Models\ReservationPassenger;
use App\Services\SearchService;
use App\Services\TBOFlightBookingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class FlightController extends Controller
{
    public function __construct(Request $request)
    {
        parent::__construct($request);
    }

    public function index(Request $request)
    {
        if (! @session('preferences')['destination']) {
            return redirect('/ai-trip')->with('error', 'Please select your destination first');
        }

        $data = [
            // Use request parameters first, then session as fallback
            'adults' => (int) ($request->adults ?: (@session('preferences')['adults'] ?: 1)),
            'children' => (int) ($request->children ?: (@session('preferences')['children'] ?: 0)),
            'infants' => (int) ($request->infants ?: (@session('preferences')['infants'] ?: 0)),
            'is_domestic' => $request->is_domestic ?: false,
            'one_stop' => $request->one_stop == 'on' ? true : false,
            'direct' => $request->direct == 'on' ? true : false,
            'journey_type' => (int) ($request->journey_type ?: (@session('preferences')['journey_type'] ?: 2)),
            'currency' => @session('currency') == 'SAR' ? 'SAR' : 'USD',
            'origin' => $request->origin ?: @session('preferences')['origin'],
            'destination' => $request->destination ?: @session('preferences')['destination'],
            'flight_class' => (int) ($request->flight_class ?: (@session('preferences')['flight_class'] ?: 1)),
            'start_date' => $request->start_date ?: @session('preferences')['start_date'],
            'end_date' => $request->end_date ?: @session('preferences')['end_date'],
            'flight_time' => $request->flight_time ?: '00:00:00',
            'flight_time_return' => $request->flight_time_return ?: '00:00:00',
            // Additional filter parameters
            'sort' => $request->sort ?: 'price_from_low_to_high',
            'type' => $request->type, // For outbound/inbound selection
        ];

        if (session()->has('flight') && @session('flight')['flight_expired_time'] < now()) {
            session(['flight' => null]);
            session(['passengers' => null]);
            session(['traceId' => null]);
            session(['outbound_flight' => null]);
            session(['inbound_flight' => null]);
            
            // Clear the cache using the key we're about to create
            $cacheKey = 'flights'.md5(json_encode($data));
            Cache::forget($cacheKey);
            session(['current_flight_cache_key' => null]);
        }

        request()->merge($data);
        //Log::info('Flight search data: ', $request->all());
        
        // Store the cache key in session for later use in selectFlight
        $cacheKey = 'flights'.md5(json_encode($data));
        session(['current_flight_cache_key' => $cacheKey]);
        
        try {
            
            $flights = Cache::remember(key: $cacheKey, ttl: 60 * 15, callback: function () {
                session(['flight' => null]);

                return (new TBOFlightBookingService)->search(request());
            });
        } catch (\Exception $e) {
            Log::info($e->getMessage().' on line '.$e->getLine().' in '.$e->getFile());

            return back()->with('error', 'Something went wrong, please try again later.');
        }

        $TraceId = @$flights['Response']['TraceId'];

        session(['traceId' => $TraceId]);

        if (! $flights && ! array_key_exists('Results', @$flights['Response'])) {
            return back()->with('error', 'No flights found');
        }

        $outbound_flights = array_key_exists('Results', @$flights['Response']) ? @$flights['Response']['Results'][0] : [];

        $inbound_flights = array_key_exists('Results', @$flights['Response']) ? @$flights['Response']['Results'][1] : [];

        $sort = $request->sort == 'price_from_high_to_low' ? 'sortByDesc' : 'sortBy';

        // Log filter parameters for debugging
        Log::info('Filter parameters:', [
            'direct' => $request->direct,
            'one_stop' => $request->one_stop,
            'direct_on' => $request->direct == 'on',
            'one_stop_on' => $request->one_stop == 'on'
        ]);

        // Process and filter outbound flights
        $outbound_flights = collect($outbound_flights)->map(function ($flight) use ($TraceId) {
            $flight['Published_Fare'] = @$flight['Fare']['BaseFare'] + @$flight['Fare']['Tax'] + @$flight['Fare']['OtherCharges'] + @$flight['Fare']['ServiceFee'] + @$flight['Fare']['AdditionalTxnFeepub'] ?? 0 + @$flight['Fare']['AirlineTransFee'];

            $flight['Offered_Fare'] = @$flight['Published_Fare'] - (@$flight['Fare']['CommissionEarned'] + @$flight['Fare']['IncentiveEarned'] + @$flight['Fare']['PLBEarned'] + @$flight['Fare']['AdditionalTxnFeepub'] ?? 0);

            $flight['Invoice_Amount'] = @$flight['Offered_Fare'] + (@$flight['Fare']['TdsOnCommission'] + @$flight['Fare']['TdsOnIncentive'] + @$flight['Fare']['TdsOnPLB']);

            $flight['Currency'] = @$flight['Fare']['Currency'];
            
            // Add TraceId to each flight
            $flight['TraceId'] = $TraceId;

            return $flight;
        });

        //Log::info('Outbound flights before filtering: ' . $outbound_flights->count());
        
        // Log segment counts for debugging
        $segmentCounts = $outbound_flights->map(function($flight) {
            return count($flight['Segments']);
        })->toArray();
        //Log::info('Segment counts in flights:', $segmentCounts);

        // Apply additional filters
        $directFilter = $request->direct == 'on';
        $oneStopFilter = $request->one_stop == 'on';
        
        if ($directFilter || $oneStopFilter) {
            $outbound_flights = $outbound_flights->filter(function ($flight) use ($directFilter, $oneStopFilter) {
                $segmentCount = count($flight['Segments']);
                
                // If both filters are active, show flights that match either condition
                if ($directFilter && $oneStopFilter) {
                    return $segmentCount == 1 || $segmentCount == 2;
                }
                
                // If only direct filter is active, show only direct flights
                if ($directFilter) {
                    return $segmentCount == 1;
                }
                
                // If only one-stop filter is active, show only one-stop flights
                if ($oneStopFilter) {
                    return $segmentCount == 2;
                }
                
                return true;
            });
            
            Log::info('Applied flight filters', [
                'direct' => $directFilter,
                'one_stop' => $oneStopFilter,
                'filtered_count' => $outbound_flights->count()
            ]);
        }

        //Log::info('Outbound flights after filtering: ' . $outbound_flights->count());

        // Apply time filters
        if ($request->flight_time && $request->flight_time != '00:00:00') {
            $outbound_flights = $outbound_flights->filter(function ($flight) use ($request) {
                $depTime = @$flight['Segments'][0]['Origin']['DepTime'];
                if (!$depTime) return true;
                
                $flightHour = \Carbon\Carbon::parse($depTime)->hour;
                
                switch ($request->flight_time) {
                    case '08:00:00': // Morning 6-12
                        return $flightHour >= 6 && $flightHour < 12;
                    case '14:00:00': // Afternoon 12-18
                        return $flightHour >= 12 && $flightHour < 18;
                    case '19:00:00': // Evening 18-24
                        return $flightHour >= 18 && $flightHour <= 23;
                    case '01:00:00': // Night 0-6
                        return $flightHour >= 0 && $flightHour < 6;
                    default:
                        return true;
                }
            });
        }

        $outbound_flights = $outbound_flights->$sort('Invoice_Amount')->paginate();

        // Process and filter inbound flights
        $inbound_flights = collect($inbound_flights)->map(function ($flight) use ($TraceId) {
            $flight['Published_Fare'] = @$flight['Fare']['BaseFare'] + @$flight['Fare']['Tax'] + @$flight['Fare']['OtherCharges'] + @$flight['Fare']['ServiceFee'] + @$flight['Fare']['AdditionalTxnFeepub'] ?? 0 + @$flight['Fare']['AirlineTransFee'];

            $flight['Offered_Fare'] = @$flight['Published_Fare'] - (@$flight['Fare']['CommissionEarned'] + @$flight['Fare']['IncentiveEarned'] + @$flight['Fare']['PLBEarned'] + @$flight['Fare']['AdditionalTxnFeepub'] ?? 0);

            $flight['Invoice_Amount'] = @$flight['Offered_Fare'] + (@$flight['Fare']['TdsOnCommission'] + @$flight['Fare']['TdsOnIncentive'] + @$flight['Fare']['TdsOnPLB']);

            $flight['Currency'] = @$flight['Fare']['Currency'];
            
            // Add TraceId to each flight
            $flight['TraceId'] = $TraceId;

            return $flight;
        });

        // Apply additional filters to inbound flights
        if ($directFilter || $oneStopFilter) {
            $inbound_flights = $inbound_flights->filter(function ($flight) use ($directFilter, $oneStopFilter) {
                $segmentCount = count($flight['Segments']);
                
                // If both filters are active, show flights that match either condition
                if ($directFilter && $oneStopFilter) {
                    return $segmentCount == 1 || $segmentCount == 2;
                }
                
                // If only direct filter is active, show only direct flights
                if ($directFilter) {
                    return $segmentCount == 1;
                }
                
                // If only one-stop filter is active, show only one-stop flights
                if ($oneStopFilter) {
                    return $segmentCount == 2;
                }
                
                return true;
            });
            
            Log::info('Applied inbound flight filters', [
                'direct' => $directFilter,
                'one_stop' => $oneStopFilter,
                'filtered_count' => $inbound_flights->count()
            ]);
        }

        // Apply return flight time filters
        if ($request->flight_time_return && $request->flight_time_return != '00:00:00') {
            $inbound_flights = $inbound_flights->filter(function ($flight) use ($request) {
                $depTime = @$flight['Segments'][0]['Origin']['DepTime'];
                if (!$depTime) return true;
                
                $flightHour = \Carbon\Carbon::parse($depTime)->hour;
                
                switch ($request->flight_time_return) {
                    case '08:00:00': // Morning 6-12
                        return $flightHour >= 6 && $flightHour < 12;
                    case '14:00:00': // Afternoon 12-18
                        return $flightHour >= 12 && $flightHour < 18;
                    case '19:00:00': // Evening 18-24
                        return $flightHour >= 18 && $flightHour <= 23;
                    case '01:00:00': // Night 0-6
                        return $flightHour >= 0 && $flightHour < 6;
                    default:
                        return true;
                }
            });
        }

        $inbound_flights = $inbound_flights->$sort('Invoice_Amount')->paginate();

        if (count($inbound_flights) != 0) {
            session(['flight.inbound_flight' => true]);
        } else {
            session(['flight.inbound_flight' => false]);
        }
        return view('website.flights', compact('outbound_flights', 'inbound_flights'));
    }

    public function search(Request $request)
    {
        SearchService::search($request, 3);

        return redirect('/flights');
    }


     public function searchNew(Request $request)
    {
        SearchService::searchNew($request, 3);

        return redirect('/flights');
    }


    // public function search(Request $request)
    // {
    //     SearchService::search($request, 3);

    //     return redirect('/flights');
    // }

    public function selectFlight()
    {
        // Use the same data structure as in index() method for consistent cache key
        $data = [
            'adults' => (int) (@session('preferences')['adults'] ?: 1),
            'children' => (int) (@session('preferences')['children'] ?: 0),
            'infants' => (int) (@session('preferences')['infants'] ?: 0),
            'is_domestic' => @session('preferences')['is_domestic'] ?: false,
            'one_stop' => @session('preferences')['one_stop'] == 'on' ? true : false,
            'direct' => @session('preferences')['direct'] == 'on' ? true : false,
            'journey_type' => (int) (@session('preferences')['journey_type'] ?: 2),
            'currency' => @session('currency') == 'SAR' ? 'SAR' : 'USD',
            'origin' => @session('preferences')['origin'],
            'destination' => @session('preferences')['destination'],
            'flight_class' => (int) (@session('preferences')['flight_class'] ?: 1),
            'start_date' => @session('preferences')['start_date'],
            'end_date' => @session('preferences')['end_date'],
            'flight_time' => @session('preferences')['flight_time'] ?: '00:00:00',
            'flight_time_return' => @session('preferences')['flight_time_return'] ?: '00:00:00',
            'sort' => 'price_from_low_to_high', // Default sort
            'type' => null, // No specific type filter
        ];

        // Try to get flights using the cached key from session first
        $cacheKey = session('current_flight_cache_key');
        $flights = null;
        
        if ($cacheKey) {
            $flights = Cache::get($cacheKey);
            Log::info('Using cached flight key from session: ' . $cacheKey);
        }
        
        // Fallback to constructing cache key if session key doesn't work
        if (!$flights) {
            $flights = Cache::get('flights'.md5(json_encode($data)));
            Log::info('Using constructed cache key as fallback');
        }

        if (! $flights) {
            Log::info('No flight cache found, redirecting to search');
            return redirect('/flights')->with('error', 'Flight data expired, please search again.');
        }

        Log::info('Flight selection - Result: ' . request()->result);
        Log::info('Flight selection - Segment data exists: ' . (request()->segment ? 'Yes' : 'No'));

        // Get TraceId from cached flights response
        $TraceId = @$flights['Response']['TraceId'];

        if (str_starts_with(request()->result, 'OB')) {
            $flights = array_key_exists('Results', $flights['Response']) ? $flights['Response']['Results'][0] : [];
            Log::info('Processing outbound flight, flights count: ' . count($flights));

            $flight = collect($flights)->filter(function ($query) {
                return str_contains($query['ResultIndex'], Str::limit(request()->result, 10, ''));
            })->first();

            Log::info('Found matching outbound flight: ' . ($flight ? 'Yes' : 'No'));
            
            if ($flight) {
                // Add TraceId to the selected outbound flight
                $flight['TraceId'] = $TraceId;
                session(['outbound_flight' => $flight]);
                Log::info('Stored outbound flight in session with TraceId');
            }
        }
//if(config('app.flight_version'=='v2')){
    if (str_starts_with(request()->result, 'IB')) {
        $flights = array_key_exists('Results', $flights['Response']) ? @$flights['Response']['Results'][1] : [];
        Log::info('Processing inbound flight, flights count: ' . count($flights));

        $flight = collect($flights)->filter(function ($query) {
            return str_contains($query['ResultIndex'], Str::limit(request()->result, 10, ''));
        })->first();

        Log::info('Found matching inbound flight: ' . ($flight ? 'Yes' : 'No'));
        
        if ($flight) {
            // Add TraceId to the selected inbound flight
            $flight['TraceId'] = $TraceId;
            session(['inbound_flight' => $flight]);
            Log::info('Stored inbound flight in session with TraceId');
        }
    }
//}

        // Store basic flight selection data without API calls (APIs will be called from passenger page)
        if (str_starts_with(request()->result, 'OB')) {
            Log::info('Processing outbound flight selection');

            if (isset(request()->segment)) {
                session(['flight.flightSegment' => json_decode(request()->segment, true)]);
            }

            if (isset(request()->result)) {
                session(['flight.flightResult' => request()->result]);
            }

            if (isset($flight['Invoice_Amount'])) {
                session(['flight.flight_amount' => (float) $flight['Invoice_Amount']]);
            }

            if (session('flight.inbound_flight')) {
                session(['flight.outbound_flight_selected' => true]);
                Log::info('Outbound flight selected, redirecting to flights for inbound selection');

                return redirect('/flights');
            } else {
                Log::info('No inbound flight needed, proceeding to passenger information');
            }
        } else {
            Log::info('Processing inbound flight selection');

            if (isset(request()->segment)) {
                session(['flight.inbound_flightSegment' => json_decode(request()->segment, true)]);
            }

            if (isset(request()->result)) {
                session(['flight.inbound_flightResult' => request()->result]);
            }

            if (isset($flight['Invoice_Amount'])) {
                session(['flight.inbound_flight_amount' => (float) $flight['Invoice_Amount']]);
            }

            session(['flight.inbound_flight_selected' => true]);
            Log::info('Inbound flight selected, proceeding to passenger information');
        }

        Log::info('Flight selection completed, redirecting to passenger information (APIs will be called there)');
        // Redirect to passenger information where APIs will be called from passenger page
        return redirect('/summary');
    }

    public function cancel_flight($pnr)
    {
        $reservation_flight = ReservationFlight::where('pnr', $pnr)->first();

        if (! $reservation_flight) {
            abort(404);
        }

        if ((int) optional($reservation_flight->reservation)->user_id !== (int) auth('web')->id()) {
            abort(403);
        }

        $last_name = optional(ReservationPassenger::where('reservation_id', $reservation_flight->reservation_id)->first())->last_name;

        if (! $last_name) {
            abort(404);
        }

        $request = [
            'pnr' => $pnr,
            'last_name' => $last_name,
        ];
        (new TBOFlightBookingService)->cancel($request);
        return $reservation_flight->is_refundable == 0 ?  redirect()->back()->with('success', __('dashboard.reservation_canceled')) : redirect()->back()->with('success', __('dashboard.cancel_pending'));
    }
}
