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
        if (! session('preferences.destination')) {
            return redirect('/ai-trip')->with('error', 'Please select your destination first');
        }

        $prefs = session('preferences', []);

        $data = [
            'adults' => (int) ($request->adults ?: ($prefs['adults'] ?? 1)),
            'children' => (int) ($request->children ?: ($prefs['children'] ?? 0)),
            'infants' => (int) ($request->infants ?: ($prefs['infants'] ?? 0)),
            'is_domestic' => $request->is_domestic ?: false,
            'one_stop' => $request->one_stop == 'on',
            'direct' => $request->direct == 'on',
            'journey_type' => (int) ($request->journey_type ?: ($prefs['journey_type'] ?? 2)),
            'currency' => session('currency') == 'SAR' ? 'SAR' : 'USD',
            'origin' => $request->origin ?: ($prefs['origin'] ?? null),
            'destination' => $request->destination ?: ($prefs['destination'] ?? null),
            'flight_class' => (int) ($request->flight_class ?: ($prefs['flight_class'] ?? 1)),
            'start_date' => $request->start_date ?: ($prefs['start_date'] ?? null),
            'end_date' => $request->end_date ?: ($prefs['end_date'] ?? null),
            'flight_time' => $request->flight_time ?: '00:00:00',
            'flight_time_return' => $request->flight_time_return ?: '00:00:00',
            'sort' => $request->sort ?: 'price_from_low_to_high',
            'type' => $request->type,
        ];

        $flightSession = session('flight', []);
        if (session()->has('flight') && ($flightSession['flight_expired_time'] ?? null) < now()) {
            session(['flight' => null, 'passengers' => null, 'traceId' => null,
                'outbound_flight' => null, 'inbound_flight' => null]);
            Cache::forget('flights'.md5(json_encode($data)));
            session(['current_flight_cache_key' => null]);
        }

        request()->merge($data);

        $cacheKey = 'flights'.md5(json_encode($data));
        session(['current_flight_cache_key' => $cacheKey]);

        try {
            $flights = Cache::remember(key: $cacheKey, ttl: 60 * 15, callback: function () {
                session(['flight' => null]);

                return (new TBOFlightBookingService)->search(request());
            });
        } catch (\Exception $e) {
            Log::error($e->getMessage().' on line '.$e->getLine().' in '.$e->getFile());

            return back()->with('error', 'Something went wrong, please try again later.');
        }

        $response = $flights['Response'] ?? [];
        $TraceId = $response['TraceId'] ?? null;

        session(['traceId' => $TraceId]);

        if (! $flights || ! array_key_exists('Results', $response)) {
            return back()->with('error', 'No flights found');
        }

        $outbound_flights = $response['Results'][0] ?? [];
        $inbound_flights = $response['Results'][1] ?? [];

        $sort = $request->sort == 'price_from_high_to_low' ? 'sortByDesc' : 'sortBy';
        $directFilter = $request->direct == 'on';
        $oneStopFilter = $request->one_stop == 'on';

        $outbound_flights = $this->processFares(collect($outbound_flights), $TraceId);
        $outbound_flights = $this->applyStopFilter($outbound_flights, $directFilter, $oneStopFilter);
        $outbound_flights = $this->applyTimeFilter($outbound_flights, $request->flight_time);
        $outbound_flights = $outbound_flights->$sort('Invoice_Amount')->paginate();

        $inbound_flights = $this->processFares(collect($inbound_flights), $TraceId);
        $inbound_flights = $this->applyStopFilter($inbound_flights, $directFilter, $oneStopFilter);
        $inbound_flights = $this->applyTimeFilter($inbound_flights, $request->flight_time_return);
        $inbound_flights = $inbound_flights->$sort('Invoice_Amount')->paginate();

        session(['flight.inbound_flight' => count($inbound_flights) > 0]);

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

    public function selectFlight()
    {
        $prefs = session('preferences', []);

        $data = [
            'adults' => (int) ($prefs['adults'] ?? 1),
            'children' => (int) ($prefs['children'] ?? 0),
            'infants' => (int) ($prefs['infants'] ?? 0),
            'is_domestic' => $prefs['is_domestic'] ?? false,
            'one_stop' => ($prefs['one_stop'] ?? null) == 'on',
            'direct' => ($prefs['direct'] ?? null) == 'on',
            'journey_type' => (int) ($prefs['journey_type'] ?? 2),
            'currency' => session('currency') == 'SAR' ? 'SAR' : 'USD',
            'origin' => $prefs['origin'] ?? null,
            'destination' => $prefs['destination'] ?? null,
            'flight_class' => (int) ($prefs['flight_class'] ?? 1),
            'start_date' => $prefs['start_date'] ?? null,
            'end_date' => $prefs['end_date'] ?? null,
            'flight_time' => $prefs['flight_time'] ?? '00:00:00',
            'flight_time_return' => $prefs['flight_time_return'] ?? '00:00:00',
            'sort' => 'price_from_low_to_high',
            'type' => null,
        ];

        $cacheKey = session('current_flight_cache_key');
        $flights = $cacheKey ? Cache::get($cacheKey) : null;

        if (! $flights) {
            $flights = Cache::get('flights'.md5(json_encode($data)));
        }

        if (! $flights) {
            return redirect('/flights')->with('error', 'Flight data expired, please search again.');
        }

        $response = $flights['Response'] ?? [];
        $TraceId = $response['TraceId'] ?? null;

        if (str_starts_with(request()->result, 'OB')) {
            $flightList = $response['Results'][0] ?? [];
            $flight = collect($flightList)->first(fn ($q) => str_contains($q['ResultIndex'], Str::limit(request()->result, 10, ''))
            );

            if ($flight) {
                $flight['TraceId'] = $TraceId;
                session(['outbound_flight' => $flight]);
            }

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

                return redirect('/flights');
            }
        } else {
            $flightList = $response['Results'][1] ?? [];
            $flight = collect($flightList)->first(fn ($q) => str_contains($q['ResultIndex'], Str::limit(request()->result, 10, ''))
            );

            if ($flight) {
                $flight['TraceId'] = $TraceId;
                session(['inbound_flight' => $flight]);
            }

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
        }

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

        (new TBOFlightBookingService)->cancel(['pnr' => $pnr, 'last_name' => $last_name]);

        return $reservation_flight->is_refundable == 0
            ? redirect()->back()->with('success', __('dashboard.reservation_canceled'))
            : redirect()->back()->with('success', __('dashboard.cancel_pending'));
    }

    // -------------------------------------------------------------------------
    // Private helpers
    // -------------------------------------------------------------------------

    private function processFares(\Illuminate\Support\Collection $flights, ?string $traceId): \Illuminate\Support\Collection
    {
        return $flights->map(function ($flight) use ($traceId) {
            $fare = $flight['Fare'] ?? [];

            $published = ($fare['BaseFare'] ?? 0)
                + ($fare['Tax'] ?? 0)
                + ($fare['OtherCharges'] ?? 0)
                + ($fare['ServiceFee'] ?? 0)
                + ($fare['AdditionalTxnFeepub'] ?? 0)
                + ($fare['AirlineTransFee'] ?? 0);

            $offered = $published
                - (($fare['CommissionEarned'] ?? 0)
                + ($fare['IncentiveEarned'] ?? 0)
                + ($fare['PLBEarned'] ?? 0)
                + ($fare['AdditionalTxnFeepub'] ?? 0));

            $flight['Published_Fare'] = $published;
            $flight['Offered_Fare'] = $offered;
            $flight['Invoice_Amount'] = $offered
                + ($fare['TdsOnCommission'] ?? 0)
                + ($fare['TdsOnIncentive'] ?? 0)
                + ($fare['TdsOnPLB'] ?? 0);
            $flight['Currency'] = $fare['Currency'] ?? null;
            $flight['TraceId'] = $traceId;

            return $flight;
        });
    }

    private function applyStopFilter(\Illuminate\Support\Collection $flights, bool $direct, bool $oneStop): \Illuminate\Support\Collection
    {
        if (! $direct && ! $oneStop) {
            return $flights;
        }

        return $flights->filter(function ($flight) use ($direct, $oneStop) {
            $segments = count($flight['Segments'] ?? []);
            if ($direct && $oneStop) {
                return $segments == 1 || $segments == 2;
            }

            return $direct ? $segments == 1 : $segments == 2;
        });
    }

    private function applyTimeFilter(\Illuminate\Support\Collection $flights, ?string $timeFilter): \Illuminate\Support\Collection
    {
        if (! $timeFilter || $timeFilter === '00:00:00') {
            return $flights;
        }

        return $flights->filter(function ($flight) use ($timeFilter) {
            $depTime = $flight['Segments'][0]['Origin']['DepTime'] ?? null;
            if (! $depTime) {
                return true;
            }
            $hour = \Carbon\Carbon::parse($depTime)->hour;

            return match ($timeFilter) {
                '08:00:00' => $hour >= 6 && $hour < 12,
                '14:00:00' => $hour >= 12 && $hour < 18,
                '19:00:00' => $hour >= 18 && $hour <= 23,
                '01:00:00' => $hour >= 0 && $hour < 6,
                default => true,
            };
        });
    }
}
