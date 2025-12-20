<?php

namespace App\Services;

use App\Models\Country;
use App\Models\ReservationFlight;
use Beste\Json;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TBOFlightBookingService
{
    private $username;

    private $password;

    public function __construct()
    {
        $this->username = config('services.tbo_flight.username');
        $this->password = config('services.tbo_flight.password');
    }

    public function authenticate()
    {
        return Cache::remember('tbo-flight-auth', now()->addHours(2), function () {
               
            $response = Http::timeout(120)->post(config('services.tbo_flight.auth').'/Authenticate/ValidateAgency/', [
                'UserName' => $this->username,
                'Password' => $this->password,
                'BookingMode' => 'API',
                'IPAddress' => request()->ip(),
            ]);

            $responseData = $response->json();
            
            if (!$response->successful() || !isset($responseData['TokenId'])) {
                Log::error('TBO Authentication failed', [
                    'status' => $response->status(),
                    'response' => $responseData
                ]);
                throw new \Exception('TBO Authentication failed: ' . ($responseData['Error']['ErrorMessage'] ?? 'Unknown error'));
            }
             session(['traceId' => $responseData['TrackingId']]);
            Log::info('TBO Authentication successful', ['TokenId' => substr($responseData['TokenId'], 0, 20) . '...']);
            return $responseData['TokenId'];
        });
    }


     public function authenticateV2()
    {
        return Cache::remember('tbo-flight-auth', now()->addHours(2), function () {
            
            $response = Http::timeout(120)->post(config('services.tbo_flight.auth').'/Authenticate/ValidateAgency/', [
                'UserName' => $this->username,
                'Password' => $this->password,
                'BookingMode' => 'API',
                'IPAddress' => request()->ip(),
            ]);

            $responseData = $response->json();
            
            if (!$response->successful() || !isset($responseData['TokenId'])) {
                Log::error('TBO Authentication failed', [
                    'status' => $response->status(),
                    'response' => $responseData
                ]);
                throw new \Exception('TBO Authentication failed: ' . ($responseData['Error']['ErrorMessage'] ?? 'Unknown error'));
            }
             session(['traceId' => $responseData['TrackingId']]);
            Log::info('TBO Authentication successful', ['TokenId' => substr($responseData['TokenId'], 0, 20) . '...']);
            return ['TokenId' => $responseData['TokenId'], 'TraceId' => $responseData['TrackingId']] ;
        });
    }


    /**
     * Get a fresh token (force refresh)
     */
    public function getFreshToken()
    {
        Cache::forget('tbo-flight-auth');
        return $this->authenticate();
    }

    /**
     * Make API call with automatic token refresh on authentication failure
     */
    private function makeApiCall($url, $data, $method = 'POST', $maxRetries = 2)
    {
        $attempt = 0;
        
        while ($attempt < $maxRetries) {
            $data['TokenId'] = $this->authenticate();
            
            $response = Http::timeout(1900)->$method($url, $data);
            $responseData = $response->json();
            
            // Check if token is invalid and retry with fresh token
            if (isset($responseData['Response']['Error']['ErrorCode']) && 
                $responseData['Response']['Error']['ErrorCode'] == 6 && 
                strpos($responseData['Response']['Error']['ErrorMessage'], 'Invalid Token') !== false) {
                
                Log::warning('TBO API returned Invalid Token error, refreshing token and retrying...', [
                    'attempt' => $attempt + 1,
                    'url' => $url
                ]);
                
                // Get fresh token and retry
                $this->getFreshToken();
                $attempt++;
                continue;
            }
            
            // Return response if no token error
            return $responseData;
        }
        
        Log::error('TBO API call failed after maximum retries', [
            'url' => $url,
            'max_retries' => $maxRetries,
            'last_response' => $responseData ?? null
        ]);
        
        return $responseData ?? ['Response' => ['Error' => ['ErrorMessage' => 'API call failed after retries']]];
    }

    /**
     * Get a fresh TraceId by performing a quick search using stored preferences
     */
    public function getFreshTraceId()
    {
        try {
            // Get search parameters from session to recreate the search
            $preferences = session('preferences', []);
            
            if (empty($preferences)) {
                Log::error('No preferences found in session to refresh TraceId');
                return null;
            }

            // Build search segments from preferences
            $segments = [];
            if (in_array($preferences['journey_type'] ?? 1, [2, 5])) {
                $segments[] = [
                    'Origin' => $preferences['origin'] ?? 'RUH',
                    'Destination' => $preferences['destination'] ?? 'CAI',
                    'FlightCabinClass' => 1,
                    'PreferredDepartureTime' => ($preferences['start_date'] ?? date('Y-m-d')) . 'T' . ($preferences['flight_time'] ?? '00:00:00'),
                    'PreferredArrivalTime' => ($preferences['start_date'] ?? date('Y-m-d')) . 'T00:00:00',
                ];

                $segments[] = [
                    'Origin' => $preferences['destination'] ?? 'CAI',
                    'Destination' => $preferences['origin'] ?? 'RUH',
                    'FlightCabinClass' => 1,
                    'PreferredDepartureTime' => ($preferences['end_date'] ?? date('Y-m-d')) . 'T' . ($preferences['flight_time_return'] ?? '00:00:00'),
                    'PreferredArrivalTime' => ($preferences['end_date'] ?? date('Y-m-d')) . 'T00:00:00',
                ];
            } else {
                $segments[] = [
                    'Origin' => $preferences['origin'] ?? 'RUH',
                    'Destination' => $preferences['destination'] ?? 'CAI',
                    'FlightCabinClass' => 1,
                    'PreferredDepartureTime' => ($preferences['start_date'] ?? date('Y-m-d')) . 'T' . ($preferences['flight_time'] ?? '00:00:00'),
                    'PreferredArrivalTime' => ($preferences['start_date'] ?? date('Y-m-d')) . 'T00:00:00',
                ];
            }

            // Perform fresh search to get new TraceId
            Log::info('Performing fresh search to get new TraceId due to session timeout');
            
            $response = Http::timeout(1900)->post(config('services.tbo_flight.search_url'), [
                'AdultCount' => (string) ($preferences['adults'] ?? 1),
                'ChildCount' => (string) ($preferences['children'] ?? 0),
                'InfantCount' => (string) ($preferences['infants'] ?? 0),
                'IsDomestic' => (string) ($preferences['is_domestic'] ?? false),
                'BookingMode' => "5",
                'DirectFlight' => "false",
                'OneStopFlight' => "false",
                'JourneyType' => (string) ($preferences['journey_type'] ?? 1),
                'EndUserIp' => request()->ip(),
                'TokenId' => $this->authenticate(),
                //'PreferredAirlines' => ["EK", "QR", "EY", "SQ", "CX", "TG", "MH", "GA", "TK", "LH", "BA", "AF", "KL", "LX", "VS", "AC", "UA", "DL", "AA", "NZ"],                //'Sources' => ["AI", "SV", "EK", "EY", "UL"],
                'Segments' => $segments,
                
               
                //'ResultFareType' => 0,
              //  'PreferredAirlines' => ["SG"] 
           ]);

            $responseData = $response->json();
            
            if (isset($responseData['Response']['TraceId'])) {
                $newTraceId = $responseData['Response']['TraceId'];
                Log::info('Successfully obtained fresh TraceId: ' . $newTraceId);
                
                // Update session with new TraceId
                session(['traceId' => $newTraceId]);
                
                return $newTraceId;
            } else {
                Log::error('Failed to get fresh TraceId from search response', $responseData);
                return null;
            }

        } catch (\Exception $e) {
            Log::error('Exception while getting fresh TraceId: ' . $e->getMessage());
            return null;
        }
    }

    public function search($request)
    {
        $Segments = [];

        if (in_array($request->journey_type, [2, 5])) {
            $Segments[] = [
                'Origin' => $request->origin,
                'Destination' => $request->destination,
                'FlightCabinClass' =>  1,
                'PreferredDepartureTime' => $request->start_date . 'T' . $request->flight_time,
                'PreferredArrivalTime' => $request->start_date . 'T00:00:00',
            ];

            $Segments[] = [
                'Origin' => $request->destination,
                'Destination' => $request->origin,
                'FlightCabinClass' =>  1,
                'PreferredDepartureTime' => $request->end_date . 'T' . $request->flight_time_return,
                'PreferredArrivalTime' => $request->end_date . 'T00:00:00',
            ];
        } else {
            $Segments[] = [
                'Origin' => $request->origin,
                'Destination' => $request->destination,
                'FlightCabinClass' =>  1,
                'PreferredDepartureTime' => $request->start_date . 'T' . $request->flight_time,
                'PreferredArrivalTime' => $request->start_date . 'T00:00:00',
            ];
        }

        $requestBody = [
            'AdultCount' => (string) $request->adults ?: "1",
            'ChildCount' => (string) $request->children ?: "0",
            'InfantCount' => (string) $request->infants ?: "0",
            'IsDomestic' => (string) $request->is_domestic ?: false,
            'BookingMode' => "5",
            'DirectFlight' => "false",
            'OneStopFlight' => "false",
            'JourneyType' => (string) $request->journey_type ?: 1, // Specify journey type (1 - OneWay, 2 - Return, 3 - Multi Stop, 4 - AdvanceSearch, 5 - Special Return)
            'EndUserIp' => request()->ip(),
            'TokenId' => $this->authenticate(),
            //'PreferredAirlines' => ["EK", "QR", "EY", "SQ", "CX", "TG", "MH", "GA", "TK", "LH", "BA", "AF", "KL", "LX", "VS", "AC", "UA", "DL", "AA", "NZ"],            //'Sources' => ["AI", "SV", "EK", "EY", "UL"],
            'Segments' => $Segments,
            
           // 'ResultFareType' => 0,
       // 'PreferredAirlines' => ["SG"]           
        //            'SearchCombinationType'=>1
        ];

        $response = Http::timeout(1900)->post(config('services.tbo_flight.search_url'), $requestBody);

        Log::info($response->json());
        Log::info('One: REQUEST Flight: ');
        Log::info(
            $requestBody
            );
        Log::info('One: RESPONSE Flight: ', $response->json());

        session(['flight.flight_expired_time' => Carbon::now()->addMinutes(10)->format('Y-m-d H:i:s')]);
        session(['passengers' => null]);
        session(['traceId' => null]);
        session(['outbound_flight' => null]);
        session(['inbound_flight' => null]);

        return $response->json();
    }

    public function getAgencyBalance()
    {
        $response = Http::get(config('services.tbo_flight.auth').'/Authenticate/ValidateAgency/', [
            'UserName' => $this->username,
            'Password' => $this->password,
            'BookingMode' => 'API',
            'IPAddress' => request()->ip(),
        ]);
        
        return $response->json();
    }

    public function fareRules($traceId, $resultIndex, $tokenId = null)
    {   
        $requestBody= [
            'TokenId' => $tokenId ?? $this->authenticate(),
            'TraceId' => $traceId,
            'ResultIndex' => $resultIndex,
            'EndUserIp' => request()->ip(),
        ];
        $response = Http::timeout(1900)->post(config('services.tbo_flight.RC_TBOINDIA_URL').'/FareRule/',$requestBody );
        
        Log::info('Two: REQUEST FareRules: ');

        Log::info($requestBody);

        Log::info('Two: RESPONSE FareRules: ', $response->json());
        return $response->json();
    }

    public function fareQuote($traceId, $resultIndex, $tokenId = null)
    {
        $tokenIdd = $tokenId ?? $this->authenticate();

        $requestBody = [
            'TokenId' => $tokenIdd,
            'TraceId' => $traceId,
            'ResultIndex' => $resultIndex,
            'EndUserIp' => request()->ip(),
        ];
        $response = Http::timeout(1900)->post(config('services.tbo_flight.RC_TBOINDIA_URL').'/FareQuote/', $requestBody);
        Log::info('Three: REQUEST FareQuote: ');
        Log::info($requestBody);
        Log::info('Three: RESPONSE FareQuote: ', $response->json());

        return $response->json();
    }

       
 
    public function ssr($traceId, $resultIndex, $tokenId = null)
    {
        $response = Http::timeout(1900)->post(config('services.tbo_flight.RC_TBOINDIA_URL').'/SSR/', [
            'TokenId' => $tokenId ?? $this->authenticate(),
            'TraceId' => $traceId,
            'ResultIndex' => $resultIndex,
            'EndUserIp' => request()->ip(),
        ]);

        $responseData = $response->json();
        
        // Log the raw response for debugging
        Log::info('SSR API Raw Response', [
            'traceId' => $traceId,
            'resultIndex' => $resultIndex,
            'response' => $responseData
        ]);

        return $responseData;
    }

    public function booking($traceId, $flight, $fareRuleData = null, $fareQuoteData = null)
    {
        $flight = (array) $flight;
       
        
        // Fetch SSR data from TBO API for accurate pricing and descriptions
        $ssrApiResponse = $this->getSSRData($traceId, $flight['ResultIndex']);
        $ssrApiData = isset($ssrApiResponse['Response']) ? $ssrApiResponse['Response'] : null;
      
        
            $fareRuleResponse = $fareRuleData;
            
            // $this->fareRules($traceId, $flight['ResultIndex']);
            // if (!$fareRuleResponse || !isset($fareRuleResponse['Response']['ResponseStatus']) || $fareRuleResponse['Response']['ResponseStatus'] != 1) {
            //     Log::error('FareRule API failed for flight: ' . $flight['ResultIndex'], $fareRuleResponse ?: []);
            //     // Continue with booking even if FareRule fails, but log the error
            // } else {
            //     $fareRuleData = $fareRuleResponse['Response']['FareRules'] ?? $fareRuleResponse['Response']['FareRule'] ?? $fareRuleResponse;
            //     Log::info('FareRule API successful for flight: ' . $flight['ResultIndex']);
            // }
    

        
        $fareQuoteResponse = $fareQuoteData;
        // $this->fareQuote($traceId, $flight['ResultIndex']);
        // if (!$fareQuoteResponse || !isset($fareQuoteResponse['Response']['ResponseStatus']) || $fareQuoteResponse['Response']['ResponseStatus'] != 1) {
        //     Log::error('Fresh FareQuote API failed for flight: ' . $flight['ResultIndex'], $fareQuoteResponse ?: []);
        //     // Continue with booking with existing data, but log the error
        // } else {
        //     $fareQuoteData = $fareQuoteResponse['Response']['Results'] ?? $fareQuoteResponse['Response'];
        //     Log::info('Fresh FareQuote API successful for flight: ' . $flight['ResultIndex']);
        // }

        // Get SSR data from TBO API
        $ssrData = null;
        try {
            $ssrResponse = $this->getSSRData($traceId, $flight['ResultIndex']);
            if ($ssrResponse && isset($ssrResponse['Response']['ResponseStatus']) && $ssrResponse['Response']['ResponseStatus'] == 1) {
                $ssrData = $ssrResponse['Response'];
                Log::info('SSR API successful for flight: ' . $flight['ResultIndex']);
            } else {
                Log::warning('SSR API failed for flight: ' . $flight['ResultIndex'], $ssrResponse ?: []);
            }
        } catch (\Exception $e) {
            Log::error('SSR API exception for flight: ' . $flight['ResultIndex'], ['error' => $e->getMessage()]);
        }

        $Segments_BE = $flight['Segments'][0];
        $FareRules = $fareRuleData ?? $flight['FareRules'];

        // Use FareQuote data if available, otherwise fall back to original flight data
        if ($fareQuoteData) {
            Log::info('Using FareQuote data for updated pricing in booking');
            $fareQuoteResults = $fareQuoteData['Results'] ?? $fareQuoteData;
            
            // Core fare and pricing data
            $Fare_BE = $fareQuoteResults['Fare'] ?? $flight['Fare'];
            $FareRules = $fareQuoteResults['FareRules'] ?? $flight['FareRules'];
            $LastTicketDate = $fareQuoteResults['LastTicketDate'] ?? $flight['LastTicketDate'];
            $ValidatingAirline = $fareQuoteResults['ValidatingAirline'] ?? $flight['ValidatingAirline'];
            $FareClassification = $fareQuoteResults['FareClassification'] ?? $flight['FareClassification'] ?? 'NORMAL';
            
            // Use updated segments if available in FareQuote
            $Segments_BE = $fareQuoteResults['Segments'][0] ?? $flight['Segments'][0];
            
            // Additional FareQuote specific fields
            $IsLCC = $fareQuoteResults['IsLCC'] ?? $flight['IsLCC'] ?? false;
            $IsRefundable = $fareQuoteResults['IsRefundable'] ?? $flight['IsRefundable'] ?? false;
            $IsPanRequiredAtBook = $fareQuoteResults['IsPanRequiredAtBook'] ?? false;
            $IsPanRequiredAtTicket = $fareQuoteResults['IsPanRequiredAtTicket'] ?? false;
            $IsPassportRequiredAtBook = $fareQuoteResults['IsPassportRequiredAtBook'] ?? false;
            $IsPassportRequiredAtTicket = $fareQuoteResults['IsPassportRequiredAtTicket'] ?? true;
            $AirlineRemark = $fareQuoteResults['AirlineRemark'] ?? '';
            $IsBookableIfSeatNotAvailable = $fareQuoteResults['IsBookableIfSeatNotAvailable'] ?? false;
            $IsHoldAllowedWithSSR = $fareQuoteResults['IsHoldAllowedWithSSR'] ?? false;
            $ResultFareType = $fareQuoteResults['ResultFareType'] ?? 'RegularFare';
            $IsPriceChanged = $fareQuoteData['IsPriceChanged'] ?? false;
            
            Log::info('FareQuote pricing details', [
                'IsPriceChanged' => $IsPriceChanged,
                'OriginalFare' => $flight['Fare']['PublishedFare'] ?? 'N/A',
                'UpdatedFare' => $Fare_BE['PublishedFare'] ?? 'N/A',
                'IsLCC' => $IsLCC,
                'IsRefundable' => $IsRefundable
            ]);
            
        } else {
            Log::info('Using original flight data for booking (FareQuote not available)');
            $Fare_BE = $flight['Fare'];
            $LastTicketDate = $flight['LastTicketDate'];
            $ValidatingAirline = $flight['ValidatingAirline'];
            $FareClassification = $flight['FareClassification'] ?? 'NORMAL';
            $IsLCC = $flight['IsLCC'] ?? false;
            $IsRefundable = $flight['IsRefundable'] ?? false;
            $IsPanRequiredAtBook = false;
            $IsPanRequiredAtTicket = false;
            $IsPassportRequiredAtBook = false;
            $IsPassportRequiredAtTicket = true;
            $AirlineRemark = '';
            $IsBookableIfSeatNotAvailable = false;
            $IsHoldAllowedWithSSR = false;
            $ResultFareType = 'RegularFare';
            $IsPriceChanged = false;
        }
        
        // Safely access FareRules as array - BOOKING METHOD
        $Destination = null;
        $Origin = null;
        
        if (is_array($FareRules) && !empty($FareRules)) {
            $lastFareRule = end($FareRules);
            $firstFareRule = reset($FareRules);
            
            $Destination = is_object($lastFareRule) ? $lastFareRule->Destination : ($lastFareRule['Destination'] ?? null);
            $Origin = is_object($firstFareRule) ? $firstFareRule->Origin : ($firstFareRule['Origin'] ?? null);
        } else {
            // Fallback to segment data if FareRules is not available
            if (isset($Segments_BE[0])) {
                $firstSegment = is_array($Segments_BE[0]) ? $Segments_BE[0] : (array)$Segments_BE[0];
                $lastSegment = is_array($Segments_BE) ? end($Segments_BE) : (array)end($Segments_BE);
                
                $Origin = $firstSegment['Origin']['Airport']['CityCode'] ?? 'DEL';
                $Destination = $lastSegment['Destination']['Airport']['CityCode'] ?? 'JED';
            } else {
                // Ultimate fallback
                $Origin = 'DEL';
                $Destination = 'JED';
            }
        }
        
        Log::info('Extracted Origin and Destination for booking', [
            'Origin' => $Origin,
            'Destination' => $Destination,
            'FareRules_Type' => gettype($FareRules),
            'FareRules_Count' => is_array($FareRules) ? count($FareRules) : 'not_array'
        ]);
        
        $todayDate = now()->format('Y-m-d');

        // Get passenger data from session or use defaults
        $passengers = [];
        $sessionPassengers = session('passengers', []);
        
        Log::info('Processing passenger data for booking', [
            'passengers_count' => count($sessionPassengers),
            'passengers_data' => $sessionPassengers
        ]);
        
        
        if (!empty($sessionPassengers)) {
            foreach ($sessionPassengers as $key => $passenger) {
                // Get SSR selections from passenger data directly
               
                $passengerMeal = $passenger['selected_meal'] ?? 'none';
                $passengerBaggage = $passenger['selected_baggage'] ?? 'none';
                
                Log::info("Processing SSR for passenger {$key}", [
                    'passenger_name' => ($passenger['first_name'] ?? 'Unknown') . ' ' . ($passenger['last_name'] ?? 'Unknown'),
                    'meal_selection' => $passengerMeal,
                    'baggage_selection' => $passengerBaggage,
                    'meal_price' => $passenger['meal_price'] ?? 0,
                    'baggage_price' => $passenger['baggage_price'] ?? 0
                ]);
                
                // Create SSR objects using TBO API data
                $mealSSR = $this->createMealSSR($passengerMeal, $ssrData, $Origin, $Destination);
                $baggageSSR = $this->createBaggageSSR($passengerBaggage, $ssrData, $Origin, $Destination);
                
                Log::info("Created SSR objects for passenger {$key}", [
                    'meal_ssr' => $mealSSR,
                    'baggage_ssr' => $baggageSSR
                ]);
                
                $passengers[] = (object) [
                    // Required fields only
                    'PaxId' => 0,
                    'Title' => $passenger['title'] ?? 'Mr',
                    'FirstName' => $passenger['first_name'] ?? 'FirstName',
                    'LastName' => $passenger['last_name'] ?? 'LastName',
                    'Mobile1' => $passenger['phone'] ?? '9664355353',
                    'Mobile1CountryCode' => '966',
                    'IsLeadPax' => $key === 0 ? true : false,
                    'DateOfBirth' => isset($passenger['birth_date']) ? $passenger['birth_date'] . 'T00:00:00' : '1985-05-08T00:00:00',
                    'Type' => $passenger['pax_type'] ?? 1,
                    'Gender' => $passenger['gender'] ?? 1,
                    'Email' => $passenger['email'] ?? 'test@email.com',
                    'Nationality' => (object) [
                        'CountryCode' => $passenger['nationality'] ?? 'SA',
                        'CountryName' => isset($passenger['nationality']) ? Country::where('code', $passenger['nationality'])->first()?->name_en ?? 'Saudi Arabia' : 'Saudi Arabia',
                    ],
                    
                    // Optional fields with sensible defaults
                    'PassportNo' => $passenger['passport_number'] ?? null,
                    'PassportExpiry' => isset($passenger['passport_expiry_date']) ? $passenger['passport_expiry_date'] . 'T00:00:00' : '0001-01-01T00:00:00',
                    'AddressLine1' => $passenger['address'] ?? 'Saudi Arabia',
                    'Country' => (object) [
                        'CountryCode' => 'SA',
                        'CountryName' => 'Saudi Arabia',
                    ],
                    
                    // Auto-generated/default fields
                    'PaxKey' => strtoupper(substr($passenger['last_name'] ?? 'USER', 0, 5) . substr($passenger['first_name'] ?? 'TEST', 0, 6) . ($passenger['title'] ?? 'MR')),
                    'Fare_BE' => $Fare_BE,
                    
                    // TBO Required defaults
                    'PassportIssueCountryCode' => null,
                    'PassportIssueDate' => '0001-01-01T00:00:00',
                    'Mobile2' => '',
                    'Mobile2CountryCode' => '',
                    'PassengerIdType' => 0,
                    'PassengerIdNo' => null,
                    'PassengerIdExpiry' => '0001-01-01T00:00:00',
                    'PassengerIdIssueDate' => '0001-01-01T00:00:00',
                    'PassengerIdIssueCountryCode' => null,
                    'City' => (object) [
                        'CountryCode' => null,
                        'CityCode' => 'RUH',
                        'CityName' => 'Riyadh',
                    ],
                    'AddressLine2' => '',
                    'Meal' => $passengerMeal !== 'none' ? $passengerMeal : null,
                    'Seat' => null,
                    'FFAirline' => null,
                    'FFNumber' => null,
                    'TboAirPaxId' => 0,
                    'PaxBaggage' => $baggageSSR,
                    'PaxMeal' => $mealSSR,
                    'IDCardNo' => null,
                    'ZipCode' => null,
                    'PaxSeat' => null,
                    'Ticket' => null,
                    'PaxStatus' => 0,
                    'TurkeyNumber' => null,
                    'SavePaxDetails' => true,
                    'IsTboAirBaggageAdded' => false,
                    'IdDetailCode' => null,
                    'DocumentDetails' => null,
                    'IdDetails' => [
                        (object) [
                            'PaxId' => 0,
                            'IdType' => 1,
                            'IdNumber' => $passenger['passport_number'] ?? null,
                            'IssuedCountryCode' => null,
                            'IssueDate' => null,
                            'ExpiryDate' => isset($passenger['passport_expiry_date']) ? $passenger['passport_expiry_date'] . 'T00:00:00' : '0001-01-01T00:00:00',
                        ],
                    ],
                ];
            }
        } else {
            // Minimal fallback data for testing
            $passengers[] = (object) [
                'PaxId' => 0,
                'Title' => 'Mr',
                'FirstName' => 'Test',
                'LastName' => 'User',
                'Mobile1' => '9664355353',
                'Mobile1CountryCode' => '966',
                'Mobile2' => '',
                'Mobile2CountryCode' => '',
                'IsLeadPax' => true,
                'DateOfBirth' => '1985-05-08T00:00:00',
                'Type' => 1,
                'Gender' => 1,
                'Email' => 'test@example.com',
                'Nationality' => (object) [
                    'CountryCode' => 'SA',
                    'CountryName' => 'Saudi Arabia',
                ],
                'Country' => (object) [
                    'CountryCode' => 'SA',
                    'CountryName' => 'Saudi Arabia',
                ],
                'City' => (object) [
                    'CountryCode' => null,
                    'CityCode' => 'RUH',
                    'CityName' => 'Riyadh',
                ],
                'AddressLine1' => 'Riyadh, Saudi Arabia',
                'AddressLine2' => 'Default Address Line 2', // Mandatory field for TBO API
                'PassportNo' => null,
                'PassportExpiry' => '0001-01-01T00:00:00',
                'Fare_BE' => $Fare_BE,
                'PaxKey' => strtoupper(substr($passenger['last_name'] ?? 'USER', 0, 5) . substr($passenger['first_name'] ?? 'TEST', 0, 6) . ($passenger['title'] ?? 'MR')),
                
                // Required TBO defaults
                'PassportIssueCountryCode' => null,
                'PassportIssueDate' => '0001-01-01T00:00:00',
                'PassengerIdType' => 0,
                'PassengerIdNo' => null,
                'PassengerIdExpiry' => '0001-01-01T00:00:00',
                'PassengerIdIssueDate' => '0001-01-01T00:00:00',
                'PassengerIdIssueCountryCode' => null,
                'Meal' => null,
                'Seat' => null,
                'FFAirline' => null,
                'FFNumber' => null,
                'TboAirPaxId' => 0,
                'PaxBaggage' => [],
                'PaxMeal' => [],
                'IDCardNo' => null,
                'ZipCode' => null,
                'PaxSeat' => null,
                'Ticket' => null,
                'PaxStatus' => 0,
                'TurkeyNumber' => null,
                'SavePaxDetails' => true,
                'IsTboAirBaggageAdded' => false,
                'IdDetailCode' => null,
                'DocumentDetails' => null,
                'IdDetails' => [
                    (object) [
                        'PaxId' => 0,
                        'IdType' => 1,
                        'IdNumber' => null,
                        'IssuedCountryCode' => null,
                        'IssueDate' => null,
                        'ExpiryDate' => '0001-01-01T00:00:00',
                    ],
                ],
            ];
        }

        // Get user data for UserData field
        $leadPassenger = $passengers[0];
        $userData = ($leadPassenger->Email ?? 'sdfs@gmail.com') . ',' . 
                   ($leadPassenger->Mobile1CountryCode ?? '966') . '-' . ($leadPassenger->Mobile1 ?? '4355353') . ',' .
                   ($leadPassenger->Title ?? 'Mr') . ' ' . ($leadPassenger->FirstName ?? 'FirstName') . ' ' . ($leadPassenger->LastName ?? 'LastName');
        Log::info('UserData for booking: ' . json_encode($Segments_BE[0]['Origin']['DepTime']));
        // Add mandatory TBO API fields to segments
        foreach ($Segments_BE as $index => $segment) {
            if (is_array($segment)) {
                // Add missing mandatory fields
                $Segments_BE[$index]['NoOfSeatAvailable'] = $segment['NoOfSeatAvailable'] ?? 0;
                $Segments_BE[$index]['BookingClass'] = $segment['BookingClass'] ?? $segment['CabinClass'] ?? 'Y';
                $Segments_BE[$index]['OperatingCarrier'] = $segment['OperatingCarrier'] ?? $segment['Airline']['AirlineCode'] ?? '';
                $Segments_BE[$index]['Stops'] = $segment['Stops'] ?? 0;
                
                // Rename IsETicketEligible to ETicketEligible if exists
                if (isset($segment['IsETicketEligible'])) {
                    $Segments_BE[$index]['ETicketEligible'] = $segment['IsETicketEligible'];
                    unset($Segments_BE[$index]['IsETicketEligible']);
                } else {
                    $Segments_BE[$index]['ETicketEligible'] = $segment['ETicketEligible'] ?? true;
                }
            }
        }

        // Generate a unique FlightId based on flight data
        $flightId = hash('crc32', $flight['ResultIndex'] . $traceId . time());
        $flightIdNumeric = hexdec(substr($flightId, 0, 8)); // Convert hex to numeric for TBO API
        
        Log::info('Generated FlightId for booking', [
            'FlightId' => $flightIdNumeric,
            'ResultIndex' => $flight['ResultIndex'],
            'TraceId' => $traceId
        ]);
        
        $array = [
            'ResultId' => $flight['ResultIndex'],
            'Itinerary' => (object) [
                'AgencySalesRepresentative' => 0,
                'IsHoldEligibleForLcc' => false,
                'IsManual' => false,
                'IssuancePCC' => null,
                'FlightId' => $flightIdNumeric,
                'Segments_BE' => $Segments_BE,
                'Passenger' => $passengers,
                'FareRules' => $FareRules,
                'PNR' => '',
                'InactivePNR' => null,
                'SplitPNR' => null,
                'SupplierCode' => null,
                'FlightBookingSource' => 72,
                'Destination' => $Destination,
                'FareType' => $ResultFareType ?? 'PUBL',
                'LastTicketDate' => $LastTicketDate,
                'LastVoidDate' => '0001-01-01T00:00:00',
                'Origin' => $Origin,
                'CreatedOn' => $todayDate,
                'TboAirBookingSourceId' => 0,
                'PaymentMode' => 0,
                //"SourceSessionId"=> "03e32262-9dd6-4248-a304-ade5f182edb3",
                'FailedBookingId' => 0,
                'ValidatingAirline' => null,
                'ValidatingAirlineCode' => $ValidatingAirline,
                'Ticketed' => false,
                'IsDomestic' => false,
                'AirlineCode' => null,
                'TravelDate' => $Segments_BE[0]['Origin']['DepTime'],
                'NonRefundable' => !$IsRefundable,
                'BookingId' => 0,
                'BookingMode' => 5,
                'AgentRefNo' => null,
                'IsLcc' => $IsLCC,
                'ClientIP' => null,
                'AirlineRemark' => $AirlineRemark,
                'SearchType' => 1,
                'OnBehalfOf' => 0,
                'EarnedLoyaltyPoints' => 0,
                'TrackingId' => $traceId,
                'SupplierGroupId' => 5,
                'TripId' => null,
                'TripName' => ($leadPassenger->FirstName ?? 'FirstName') . ' ' . ($leadPassenger->LastName ?? 'LastName') . ' Trip',
                'PNRStatus' => 0,
                'StaffRemarks' => null,
                'PricingKeyDetail' => null,
                'TokenId' => $this->authenticate(),
                'SSRData' => null,
                'isNewBooking' => false,
                'ParentBookingId' => 0,
                'isFromBulkImport' => false,
                'isApplicableforNewWidgetWallet' => false,
                'callBackUrl' => 'tboairdemo.techmaster.in',
                'isCancellationcoverAdded' => false,
                'FoidDetails' => (object) [],
                'BookingOperations' => null,
                'UpsellOptionsList' => null,
                'PaymentKey' => null,
                'HotelConfirmationNumber' => null,
                'HelpCenter' => null,
                'MiniFareRules' => [[]],
                'FareClassification' => $FareClassification,
                'CustomizedFareType' => null,
                //"IsVATApplicable"=> true,
                'ResultType' => 2,
                
                // Include FareQuote data properly in the itinerary structure
                'Fare' => $fareQuoteData['Fare'] ?? $Fare_BE,
                'FareBreakup' => $fareQuoteData['FareBreakup'] ?? null,
                'ResultIndex' => $fareQuoteData['ResultIndex'] ?? $flight['ResultIndex'],
                ...$fareQuoteData ,
                // Include FareRules data in the itinerary
                'FareRuleDetail' => $fareRuleData,
            ],
            'PNR' => '',
            'BookingId' => '',
            'CorporateCode' => null,
            'ConfirmPriceChangeTicket' => false,
            'IPAddress' => '115.114.12.58',
            'IsGenerateTicketRequestFromQueues' => false,
            'SegmentAnalyticsToken' => 'fdhzelbwysit22iibnxsghsj',
            'TokenId' => $this->authenticate(),
            'TrackingId' => $traceId,
            'EndUserBrowserAgent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Safari/537.36',
            'PointOfSale' => 'IN',
            'RequestOrigin' => 'India-https://www.tboair.com',
            'UserData' => $userData,
            'WebServerIP' => null,
        ];

 
        $response = Http::timeout(1900)->post(config('services.tbo_flight.after_booking').'/Booking/Book/', $array);
        $responseData = $response->json();
        Log::info('Four: booking-flight-Request ', $array);
        Log::info('Four: booking-flight-response ', $responseData);

        // Check for session timeout and retry with fresh TraceId
        if (isset($responseData['Response']['Error']['ErrorMessage']) && 
            (str_contains($responseData['Response']['Error']['ErrorMessage'], 'Session TimeOut') ||
             str_contains($responseData['Response']['Error']['ErrorMessage'], 'InvalidRequest') ||
             str_contains($responseData['Response']['Error']['ErrorMessage'], 'Please do FareQuote before'))) {
            
            Log::warning('Session timeout detected in booking, refreshing TraceId and retrying');
            
            $newTraceId = $this->getFreshTraceId();
            if ($newTraceId) {
                // Update the array with new TraceId
                $array['TraceId'] = $newTraceId;
                
                // Retry booking with new TraceId
                Log::info('Retrying booking with fresh TraceId: ' . $newTraceId);
                $response = Http::timeout(1900)->post(config('services.tbo_flight.after_booking').'/Booking/Book/', $array);
                $responseData = $response->json();
                Log::info('booking-flight-retry-response ', $responseData);
            }
        }

        return $responseData;
    }

    public function ticket($traceId, $flight, $pnr = null, $fareRuleData = null, $fareQuoteData = null)
    {
        $flight = (array) $flight;

        
            $fareRuleResponse = $fareRuleData;
            
            // $this->fareRules($traceId, $flight['ResultIndex']);
            // if (!$fareRuleResponse || !isset($fareRuleResponse['Response']['ResponseStatus']) || $fareRuleResponse['Response']['ResponseStatus'] != 1) {
            //     Log::error('FareRule API failed for flight: ' . $flight['ResultIndex'], $fareRuleResponse ?: []);
            //     // Continue with ticketing even if FareRule fails, but log the error
            // } else {
            //     $fareRuleData = $fareRuleResponse['Response']['FareRules'] ?? $fareRuleResponse['Response']['FareRule'] ?? $fareRuleResponse;
            //     Log::info('FareRule API successful for flight: ' . $flight['ResultIndex']);
            // }
       // }
 
        $fareQuoteResponse = $fareQuoteData;
        // $this->fareQuote($traceId, $flight['ResultIndex']);
        // if (!$fareQuoteResponse || !isset($fareQuoteResponse['Response']['ResponseStatus']) || $fareQuoteResponse['Response']['ResponseStatus'] != 1) {
        //     Log::error('Fresh FareQuote API failed for flight: ' . $flight['ResultIndex'], $fareQuoteResponse ?: []);
        //     // Continue with ticketing with existing data, but log the error
        // } else {
        //     $fareQuoteData = $fareQuoteResponse['Response']['Results'] ?? $fareQuoteResponse['Response'];
        //     Log::info('Fresh FareQuote API successful for flight: ' . $flight['ResultIndex']);
        // }

        // Get SSR data from TBO API
        $ssrData = null;
        try {
            $ssrResponse = $this->getSSRData($traceId, $flight['ResultIndex']);
            if ($ssrResponse && isset($ssrResponse['Response']['ResponseStatus']) && $ssrResponse['Response']['ResponseStatus'] == 1) {
                $ssrData = $ssrResponse['Response'];
                Log::info('SSR API successful for flight: ' . $flight['ResultIndex']);
            } else {
                Log::warning('SSR API failed for flight: ' . $flight['ResultIndex'], $ssrResponse ?: []);
            }
        } catch (\Exception $e) {
            Log::error('SSR API exception for flight: ' . $flight['ResultIndex'], ['error' => $e->getMessage()]);
        }

        $passengers = [];
        $Segments_BE = $flight['Segments'][0];
        $FareRules = $flight['FareRules'];
        
        // Use FareQuote data if available, otherwise fall back to original flight data
        if ($fareQuoteData) {
            Log::info('Using FareQuote data for updated pricing in ticketing');
            $fareQuoteResults = $fareQuoteData['Results'] ?? $fareQuoteData;
            
            // Core fare and pricing data
            $Fare_BE = $fareQuoteResults['Fare'] ?? $flight['Fare'];
            //$FareRules = $fareQuoteResults['FareRules'] ?? $flight['FareRules'];
            $LastTicketDate = $fareQuoteResults['LastTicketDate'] ?? $flight['LastTicketDate'];
            $ValidatingAirline = $fareQuoteResults['ValidatingAirline'] ?? $flight['ValidatingAirline'];
            $FareClassification = $fareQuoteResults['FareClassification'] ?? $flight['FareClassification'] ?? 'NORMAL';
            $AirlineCode = $fareQuoteResults['AirlineCode'] ?? $flight['AirlineCode'] ?? null;
            
            $LastTicketDate = $fareQuoteResults['LastTicketDate'] ?? $flight['LastTicketDate'] ?? null;
            $MiniFareRules = $fareQuoteResults['MiniFareRules'] ?? $flight['MiniFareRules'] ?? [[]];
            $FareClassification = $fareQuoteResults['FareClassification'] ?? $flight['FareClassification'] ?? 'NORMAL';
            // Use updated segments if available in FareQuote
            $Segments_BE = $fareQuoteResults['Segments'][0] ?? $flight['Segments'][0];
            
            // Additional FareQuote specific fields
            $IsLCC = $fareQuoteResults['IsLCC'] ?? $flight['IsLCC'] ?? true;
            $IsRefundable = $fareQuoteResults['IsRefundable'] ?? $flight['IsRefundable'] ?? false;
            $IsPanRequiredAtBook = $fareQuoteResults['IsPanRequiredAtBook'] ?? false;
            $IsPanRequiredAtTicket = $fareQuoteResults['IsPanRequiredAtTicket'] ?? false;
            $IsPassportRequiredAtBook = $fareQuoteResults['IsPassportRequiredAtBook'] ?? false;
            $IsPassportRequiredAtTicket = $fareQuoteResults['IsPassportRequiredAtTicket'] ?? true;
            $AirlineRemark = $fareQuoteResults['AirlineRemark'] ?? 'You need to provide additional Address details at Passenger Page. ';
            $IsBookableIfSeatNotAvailable = $fareQuoteResults['IsBookableIfSeatNotAvailable'] ?? false;
            $IsHoldAllowedWithSSR = $fareQuoteResults['IsHoldAllowedWithSSR'] ?? false;
            $ResultFareType = $fareQuoteResults['ResultFareType'] ?? 'RegularFare';
            $IsPriceChanged = $fareQuoteData['IsPriceChanged'] ?? false;
            
            Log::info('FareQuote pricing details for ticketing', [
                'IsPriceChanged' => $IsPriceChanged,
                'OriginalFare' => $flight['Fare']['PublishedFare'] ?? 'N/A',
                'UpdatedFare' => $Fare_BE['PublishedFare'] ?? 'N/A',
                'IsLCC' => $IsLCC,
                'IsRefundable' => $IsRefundable
            ]);
            
        } else {
            Log::info('Using original flight data for ticketing (FareQuote not available)');
            $Fare_BE = $flight['Fare'];
            $LastTicketDate = $flight['LastTicketDate'];
            $ValidatingAirline = $flight['ValidatingAirline'];
            $FareClassification = $flight['FareClassification'] ?? 'NORMAL';
            $IsLCC = $flight['IsLCC'] ?? true;
            $IsRefundable = $flight['IsRefundable'] ?? false;
            $IsPanRequiredAtBook = false;
            $IsPanRequiredAtTicket = false;
            $IsPassportRequiredAtBook = false;
            $IsPassportRequiredAtTicket = true;
            $AirlineRemark = 'You need to provide additional Address details at Passenger Page. ';
            $IsBookableIfSeatNotAvailable = false;
            $IsHoldAllowedWithSSR = false;
            $ResultFareType = 'RegularFare';
            $IsPriceChanged = false;
        }
        
        // Safely access FareRules as array - TICKET METHOD
        $Destination = null;
        $Origin = null;
        
        if (is_array($FareRules) && !empty($FareRules)) {
            $lastFareRule = end($FareRules);
            $firstFareRule = reset($FareRules);
            
            $Destination = is_object($lastFareRule) ? $lastFareRule->Destination : ($lastFareRule['Destination'] ?? null);
            $Origin = is_object($firstFareRule) ? $firstFareRule->Origin : ($firstFareRule['Origin'] ?? null);
        } else {
            // Fallback to segment data if FareRules is not available
            if (isset($Segments_BE[0])) {
                $firstSegment = is_array($Segments_BE[0]) ? $Segments_BE[0] : (array)$Segments_BE[0];
                $lastSegment = is_array($Segments_BE) ? end($Segments_BE) : (array)end($Segments_BE);
                
                $Origin = $firstSegment['Origin']['Airport']['CityCode'] ?? 'DEL';
                $Destination = $lastSegment['Destination']['Airport']['CityCode'] ?? 'JED';
            } else {
                // Ultimate fallback
                $Origin = 'DEL';
                $Destination = 'JED';
            }
        }
        
        Log::info('Extracted Origin and Destination for ticketing', [
            'Origin' => $Origin,
            'Destination' => $Destination,
            'FareRules_Type' => gettype($FareRules),
            'FareRules_Count' => is_array($FareRules) ? count($FareRules) : 'not_array'
        ]);
        
        $todayDate = now()->format('Y-m-d');

        $sessionPassengers = session('passengers', []);
       
        foreach ($sessionPassengers as $key => $passenger) {
            // Get SSR selections from passenger data directly
            $passengerMeal = $passenger['selected_meal'] ?? 'none';
            $passengerBaggage = $passenger['selected_baggage'] ?? 'none';
            
            Log::info("Processing SSR for passenger {$key} in ticketing", [
                'passenger_name' => ($passenger['first_name'] ?? 'Unknown') . ' ' . ($passenger['last_name'] ?? 'Unknown'),
                'meal_selection' => $passengerMeal,
                'baggage_selection' => $passengerBaggage,
                'meal_price' => $passenger['meal_price'] ?? 0,
                'baggage_price' => $passenger['baggage_price'] ?? 0
            ]);
            
            // Create SSR objects using TBO API data
            $mealSSR = $this->createMealSSR($passengerMeal, $ssrData, $Origin, $Destination);
            $baggageSSR = $this->createBaggageSSR($passengerBaggage, $ssrData, $Origin, $Destination);
            
            $passengers[] = (object) [
                'PassportIssueCountryCode' => null,
                'PassportIssueDate' => '0001-01-01T00:00:00',
                'PaxId' => 0,
                'Title' => $passenger['title'],
                'FirstName' => $passenger['first_name'],
                'LastName' => $passenger['last_name'],
                'Mobile1' => $passenger['phone'],
                'Mobile1CountryCode' => '',
                'Mobile2' => '',
                'Mobile2CountryCode' => '',
                'IsLeadPax' => true,
                'DateOfBirth' => $passenger['birth_date'] . 'T00:00:00',
                'Type' => $passenger['pax_type'],
                'PassengerIdType' => 0,
                'PassengerIdNo' => null,
                'PassengerIdExpiry' => '0001-01-01T00:00:00',
                'PassengerIdIssueDate' => '0001-01-01T00:00:00',
                'PassengerIdIssueCountryCode' => null,
                'PassportNo' => null,
                'Nationality' => [
                    'CountryCode' => $passenger['nationality'],
                    'CountryName' => Country::where('code', $passenger['nationality'])->first()->name_en,
                ],
                'Country' => [
                    'CountryCode' => 'AE',
                    'CountryName' => 'United Arab Emirates',
                ],
                'City' => [
                    'CountryCode' => null,
                    'CityCode' => 'QAJ',
                    'CityName' => 'Ajman City',
                ],
                'AddressLine1' => $passenger['address'],
                'AddressLine2' => 'Default Address Line 2', // Mandatory field for TBO API
                'Gender' => $passenger['gender'],
                'Email' => $passenger['email'],
                'Meal' => $passengerMeal !== 'none' ? $passengerMeal : null,
                'Seat' => null,
                'Fare_BE' => $Fare_BE,
                'FFAirline' => null,
                'FFNumber' => null,
                'PaxKey' => strtoupper(substr($passenger['last_name'] ?? 'USER', 0, 5) . substr($passenger['first_name'] ?? 'TEST', 0, 6) . ($passenger['title'] ?? 'MR')),
                'TboAirPaxId' => 0,
                'PassportExpiry' => isset($passenger['passport_expiry_date']) ? $passenger['passport_expiry_date'] . 'T00:00:00' : '0001-01-01T00:00:00',
                'PaxBaggage' => $baggageSSR,
                'PaxMeal' => $mealSSR,
                'IDCardNo' => null,
                'ZipCode' => null,
                'PaxSeat' => null,
                'Ticket' => null,
                'PaxStatus' => 0,
                'TurkeyNumber' => null,
                'SavePaxDetails' => true,
                'IsTboAirBaggageAdded' => false,
                'IdDetailCode' => null,
                'DocumentDetails' => null,
                'IdDetails' => [
                    [
                        'PaxId' => 0,
                        'IdType' => 1,
                        'IdNumber' => isset($passenger['passport_number']) && $passenger['passport_number'] ? $passenger['passport_number'] : null,
                        'IssuedCountryCode' => null,
                        'IssueDate' => null,
                        'ExpiryDate' => isset($passenger['passport_expiry_date']) ? $passenger['passport_expiry_date'] . 'T00:00:00' : '0001-01-01T00:00:00',
                    ],
                ],
            ];
        }

        // Add mandatory TBO API fields to segments for LCC ticketing
        // foreach ($Segments_BE as $index => $segment) {
        //     if (is_array($segment)) {
        //         // Add missing mandatory fields
        //         $Segments_BE[$index]['NoOfSeatAvailable'] = $segment['NoOfSeatAvailable'] ?? 0;
        //         $Segments_BE[$index]['BookingClass'] = $segment['BookingClass'] ?? $segment['CabinClass'] ?? 'Y';
        //         $Segments_BE[$index]['OperatingCarrier'] = $segment['OperatingCarrier'] ?? $segment['Airline']['AirlineCode'] ?? '';
        //         $Segments_BE[$index]['Stops'] = $segment['Stops'] ?? 0;
                
        //         // Rename IsETicketEligible to ETicketEligible if exists
        //         if (isset($segment['IsETicketEligible'])) {
        //             $Segments_BE[$index]['ETicketEligible'] = $segment['IsETicketEligible'];
        //             unset($Segments_BE[$index]['IsETicketEligible']);
        //         } else {
        //             $Segments_BE[$index]['ETicketEligible'] = $segment['ETicketEligible'] ?? true;
        //         }
        //     }
        // }

        // Generate a unique FlightId for LCC ticketing
        $flightId = hash('crc32', $flight['ResultIndex'] . $traceId . time());
        $flightIdNumeric = hexdec(substr($flightId, 0, 8)); // Convert hex to numeric for TBO API

        Log::info('Generated FlightId for LCC ticketing', [
            'FlightId' => $flightIdNumeric,
            'ResultIndex' => $flight['ResultIndex'],
            'TraceId' => $traceId
        ]);
        $todayDate = now()->format('Y-m-d');
         $leadPassenger = $passengers[0];
        $userData = ($leadPassenger->Email ?? 'sdfs@gmail.com') . ',' . 
                   ($leadPassenger->Mobile1CountryCode ?? '966') . '-' . ($leadPassenger->Mobile1 ?? '4355353') . ',' .
                   ($leadPassenger->Title ?? 'Mr') . ' ' . ($leadPassenger->FirstName ?? 'FirstName') . ' ' . ($leadPassenger->LastName ?? 'LastName');

        $array = [
            'ResultId' => $flight['ResultIndex'],
            'Itinerary' => [
                'AgencySalesRepresentative' => 0,
                'IsHoldEligibleForLcc' => false,
                'IsManual' => false,
                'IssuancePCC' => null,
                'FlightId' =>$IsLCC ? 0: $flightIdNumeric,
                'Segments_BE' => $Segments_BE,
                'Passenger' => $passengers,
                'FareRules' => $FareRules,
                'PNR' => $pnr,
                'InactivePNR' => null,
                'SplitPNR' => null,
                'SupplierCode' => null,
                'FlightBookingSource' => 72,
                'Destination' => $Destination,
                'FareType' => $ResultFareType ?? 'PUBL',
                'LastTicketDate' => $LastTicketDate,
                'LastVoidDate' => '0001-01-01T00:00:00',
                'Origin' => $Origin,
                'CreatedOn' => $todayDate,
                'TboAirBookingSourceId' => 0,
                'PaymentMode' => 0,
                //'SourceSessionId' => '7fdc1982-5da6-4ed4-8d63-3587cb17369a',
                'FailedBookingId' => 0,
                
                'Ticketed' => false,
                'IsDomestic' => false,
                'AirlineCode' => null,
                'TravelDate' => $Segments_BE[0]['Origin']['DepTime'] ?? $flight['Segments'][0]['Origin']['DepTime'],
                'NonRefundable' => !$IsRefundable,
                
                'BookingId' => 0,
                'BookingMode' => 5,
                'AgentRefNo' => null,
                'IsLcc' => $IsLCC,
                'ClientIP' => null,
                'AirlineRemark' => $AirlineRemark,
                'SearchType' => 1,
                'OnBehalfOf' => 0,
                'EarnedLoyaltyPoints' => 0,
                'TrackingId' => $traceId,
                'SupplierGroupId' => 5,
                'OrderKey' => null,
                'TripId' => null,
                'TripName' => ($leadPassenger->FirstName ?? 'FirstName') . ' ' . ($leadPassenger->LastName ?? 'LastName') . ' Trip',
                'PNRStatus' => 0,
                'StaffRemarks' => null,
                'PricingKeyDetail' => null,
                'TokenId' => $this->authenticate(),
                'SSRData' => null,
                'isNewBooking' => false,
                'isNewBookingFlow' => false,
                'ParentBookingId' => 0,
                'isFromBulkImport' => false,
                'isApplicableforNewWidgetWallet' => false,
                'callBackUrl' => 'tboairdemo.techmaster.in',
                'isCancellationcoverAdded' => false,
                'FoidDetails' => [],
                'BookingOperations' => null,
                'UpsellOptionsList' => null,
                'PaymentKey' => null,
                'HotelConfirmationNumber' => null,
                'HelpCenter' => null,
                
                'FareClassification' => $FareClassification,
                'CustomizedFareType' => null,
                //"IsVATApplicable"=> false,
                'ResultType' => 2,
                
                // Include FareQuote data properly in the ticket itinerary structure
                'Fare' => $fareQuoteData['Fare'] ?? $Fare_BE,
                'FareBreakup' => $fareQuoteData['FareBreakup'] ?? null,
                'ResultIndex' => $fareQuoteData['ResultIndex'] ?? $flight['ResultIndex'],
                ...$fareQuoteData ,
                
                // Include FareRules data in the ticket itinerary
                'FareRuleDetail' => $fareRuleData,
                'ValidatingAirline' => null,
                'ValidatingAirlineCode' => $ValidatingAirline,
                'AirlineCode' => $AirlineCode,
                'LastTicketDate' => $LastTicketDate,
                'MiniFareRules' => $MiniFareRules,
                'FareClassification' => $FareClassification,
                'Fare_BE' => $Fare_BE,
            ],
            'PNR' => "",
            'BookingId' => '',
            'CorporateCode' => null,
            'ConfirmPriceChangeTicket' => false,
            'IPAddress' =>  '115.114.12.58',
            'IsGenerateTicketRequestFromQueues' => false,
            'SegmentAnalyticsToken' =>'fdhzelbwysit22iibnxsghsj',
            'TokenId' => $this->authenticate(),
            'TrackingId' => $traceId,
            'EndUserBrowserAgent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/126.0.0.0 Safari/537.36',
            'PointOfSale' => 'IN',
            'RequestOrigin' => 'India-https://www.tboair.com',
            'UserData' =>  $userData,
            'WebServerIP' => null,
        ];
        Log::info('Five: ticket-flight-Request ');

        Log::info(json_encode($array, JSON_PRETTY_PRINT));
        $response = Http::timeout(1900)->post(config('services.tbo_flight.after_booking').'/Booking/Ticket/', $array);
        $responseData = $response->json();
        
        Log::info('Five: ticket-flight-response ');

        Log::info($responseData);

        // Check for session timeout and retry with fresh TraceId
        if (isset($responseData['Response']['Error']['ErrorMessage']) && 
            (str_contains($responseData['Response']['Error']['ErrorMessage'], 'Session TimeOut') ||
             str_contains($responseData['Response']['Error']['ErrorMessage'], 'InvalidRequest') ||
             str_contains($responseData['Response']['Error']['ErrorMessage'], 'Please do FareQuote before'))) {
            
            Log::warning('Session timeout detected in ticket, refreshing TraceId and retrying');
            
            $newTraceId = $this->getFreshTraceId();
            if ($newTraceId) {
                // Update the array with new TraceId
                $array['TrackingId'] = $newTraceId;
                
                // Retry ticket with new TraceId
                Log::info('Retrying ticket with fresh TraceId: ' . $newTraceId);
                $response = Http::timeout(1900)->post(config('services.tbo_flight.after_booking').'/Booking/Ticket/', $array);
                $responseData = $response->json();
                Log::info('ticket-flight-retry-response ', $responseData);
            }
        }

        return $responseData;
    }


     private function createBaggageSSR($baggageCode, $ssrData, $origin, $destination)
    {
        if (empty($baggageCode) || $baggageCode === 'none') {
            Log::info('No baggage SSR requested', ['baggage_code' => $baggageCode]);
            return null;
        }

        Log::info('Creating baggage SSR', [
            'baggage_code' => $baggageCode,
            'origin' => $origin,
            'destination' => $destination,
            'has_ssr_data' => !empty($ssrData),
            'ssr_structure' => [
                'has_Baggage' => isset($ssrData['Baggage']),
                'Baggage_count' => isset($ssrData['Baggage']) ? count($ssrData['Baggage']) : 0
            ]
        ]);

        // Try to find baggage in SSR API data first
        $baggageFromAPI = $this->findBaggageInSSR($baggageCode, $ssrData);
        
        if ($baggageFromAPI) {
            Log::info('Found baggage in SSR API data', $baggageFromAPI);
            return [
                'Code' => $baggageFromAPI['Code'],
                'Description' => $baggageFromAPI['Description'] ?? "Extra {$baggageFromAPI['Weight']}kg Baggage",
                'AirlineCode' => $baggageFromAPI['AirlineCode'] ?? 'SG',
                'Amount' => floatval($baggageFromAPI['Price'] ?? 0),
                'Currency' => $baggageFromAPI['Currency'] ?? 'SAR',
                'Origin' => $baggageFromAPI['Origin'] ?? $origin,
                'Destination' => $baggageFromAPI['Destination'] ?? $destination
            ];
        }

        // Extract weight from frontend code for fallback description
        $weight = '';
        if (preg_match('/(\d+)/', $baggageCode, $matches)) {
            $weight = $matches[1];
        }

        // Always create fallback baggage - ensure we never return null for valid baggage codes
        $fallbackBaggage = [
            'Code' => $baggageCode,
            'Description' => $weight ? "Extra {$weight}kg Baggage" : "Extra {$baggageCode} Baggage",
            'AirlineCode' => 'AI', // Use correct airline code
            'Amount' => $weight ? floatval($weight * 3.5) : 65.0, // Estimate based on weight or default
            'Currency' => 'SAR',
            'Origin' => $origin,
            'Destination' => $destination
        ];
        
        Log::info('Using fallback baggage data (not found in SSR API)', $fallbackBaggage);
        return $fallbackBaggage;
    }

     private function findBaggageInSSR($baggageCode, $ssrData)
    {
        if (empty($baggageCode) || $baggageCode === 'none') {
            return null;
        }

        // Map frontend codes to potential API codes dynamically
        $possibleCodes = $this->mapBaggageCodeToAPI($baggageCode, $ssrData);
        
        // Debug: Log the actual SSR data structure
        Log::info('SSR Baggage data structure debug', [
            'frontend_code' => $baggageCode,
            'possible_codes' => $possibleCodes,
            'ssr_baggage_structure' => isset($ssrData['Baggage']) ? array_keys($ssrData['Baggage']) : 'no_baggage_key',
            'ssr_baggage_sample' => isset($ssrData['Baggage'][0]) ? $ssrData['Baggage'][0] : 'no_first_element'
        ]);
        
        // For LCC airlines - check Baggage array (only LCC has baggage pricing)
        if (isset($ssrData['Baggage']) && is_array($ssrData['Baggage'])) {
            // Handle nested array structure - check first flight segment
            foreach ($ssrData['Baggage'] as $segmentIndex => $flightSegmentBaggage) {
                Log::info("Processing baggage segment {$segmentIndex}", [
                    'is_array' => is_array($flightSegmentBaggage),
                    'segment_data' => is_array($flightSegmentBaggage) ? 'array_with_' . count($flightSegmentBaggage) . '_items' : $flightSegmentBaggage
                ]);
                
                if (is_array($flightSegmentBaggage)) {
                    foreach ($flightSegmentBaggage as $baggageIndex => $baggage) {
                        Log::info("Checking baggage item {$baggageIndex}", [
                            'baggage_code' => $baggage['Code'] ?? 'no_code',
                            'baggage_weight' => $baggage['Weight'] ?? 'no_weight',
                            'baggage_price' => $baggage['Price'] ?? 'no_price',
                            'full_baggage_data' => $baggage
                        ]);
                        
                        if (isset($baggage['Code'])) {
                            // Check if the API code matches any of our possible codes
                            if (in_array($baggage['Code'], $possibleCodes) || $baggage['Code'] === $baggageCode) {
                                Log::info('Found matching baggage in SSR data', [
                                    'frontend_code' => $baggageCode,
                                    'matched_api_code' => $baggage['Code'],
                                    'baggage_data' => $baggage
                                ]);
                                return $baggage;
                            }
                        }
                    }
                } else {
                    // Handle case where flightSegmentBaggage is not an array (direct baggage object)
                    if (isset($flightSegmentBaggage['Code'])) {
                        Log::info("Checking direct baggage object", [
                            'baggage_code' => $flightSegmentBaggage['Code'],
                            'baggage_data' => $flightSegmentBaggage
                        ]);
                        
                        if (in_array($flightSegmentBaggage['Code'], $possibleCodes) || $flightSegmentBaggage['Code'] === $baggageCode) {
                            Log::info('Found matching baggage in direct SSR data', [
                                'frontend_code' => $baggageCode,
                                'matched_api_code' => $flightSegmentBaggage['Code'],
                                'baggage_data' => $flightSegmentBaggage
                            ]);
                            return $flightSegmentBaggage;
                        }
                    }
                }
            }
        }

        Log::info('No matching baggage found in SSR data using strict matching, trying fuzzy matching', [
            'frontend_code' => $baggageCode,
            'possible_codes' => $possibleCodes,
            'total_ssr_baggage_segments' => isset($ssrData['Baggage']) ? count($ssrData['Baggage']) : 0
        ]);
        
        // Fallback: Try fuzzy matching if strict matching failed
        if (isset($ssrData['Baggage']) && is_array($ssrData['Baggage'])) {
            $frontendWeight = null;
            if (preg_match('/(\d+)/', $baggageCode, $matches)) {
                $frontendWeight = intval($matches[1]);
            }
            
            if ($frontendWeight) {
                Log::info('Attempting fuzzy weight-based matching', ['target_weight' => $frontendWeight]);
                
                foreach ($ssrData['Baggage'] as $segmentIndex => $flightSegmentBaggage) {
                    if (is_array($flightSegmentBaggage)) {
                        foreach ($flightSegmentBaggage as $baggageIndex => $baggage) {
                            if (isset($baggage['Weight'])) {
                                $apiWeight = intval($baggage['Weight']);
                                
                                // Try exact weight match
                                if ($apiWeight === $frontendWeight) {
                                    Log::info('Found fuzzy weight match', [
                                        'frontend_code' => $baggageCode,
                                        'api_code' => $baggage['Code'] ?? 'no_code',
                                        'matched_weight' => $apiWeight,
                                        'baggage_data' => $baggage
                                    ]);
                                    return $baggage;
                                }
                                
                                // Try closest weight match (±2kg tolerance)
                                if (abs($apiWeight - $frontendWeight) <= 2) {
                                    Log::info('Found close weight match', [
                                        'frontend_code' => $baggageCode,
                                        'api_code' => $baggage['Code'] ?? 'no_code',
                                        'target_weight' => $frontendWeight,
                                        'api_weight' => $apiWeight,
                                        'weight_diff' => abs($apiWeight - $frontendWeight),
                                        'baggage_data' => $baggage
                                    ]);
                                    return $baggage;
                                }
                            }
                        }
                    }
                }
            }
        }
        
        Log::info('No matching baggage found even with fuzzy matching - dumping full SSR structure for debugging', [
            'frontend_code' => $baggageCode,
            'possible_codes' => $possibleCodes,
            'tried_fuzzy_matching' => isset($frontendWeight),
            'full_ssr_baggage_dump' => $ssrData['Baggage'] ?? 'no_baggage_in_ssr'
        ]);
        
        return null;
    }
    
    /**
     * Map frontend baggage codes to potential API codes dynamically from SSR data
     */
    private function mapBaggageCodeToAPI($frontendCode, $ssrData = null)
    {
        // If no SSR data provided, try to get it from session
        if (!$ssrData) {
            $flight = session('flight', []);
            $ssrData = $flight['SSRDetails'] ?? [];
        }
        
        $possibleCodes = [$frontendCode]; // Always include the original code
        
        // Extract weight from frontend code for better matching
        $frontendWeight = null;
        if (preg_match('/(\d+)/', $frontendCode, $matches)) {
            $frontendWeight = intval($matches[1]);
        }
        
        Log::info('Baggage mapping analysis', [
            'frontend_code' => $frontendCode,
            'extracted_weight' => $frontendWeight,
            'has_ssr_data' => !empty($ssrData),
            'ssr_baggage_available' => isset($ssrData['Baggage'])
        ]);
        
        // If we have SSR data, build dynamic mapping
        if (!empty($ssrData) && isset($ssrData['Baggage']) && is_array($ssrData['Baggage'])) {
            // Handle nested array structure - check first flight segment
            foreach ($ssrData['Baggage'] as $segmentIndex => $flightSegmentBaggage) {
                Log::info("Mapping codes from baggage segment {$segmentIndex}", [
                    'is_array' => is_array($flightSegmentBaggage),
                    'element_count' => is_array($flightSegmentBaggage) ? count($flightSegmentBaggage) : 'not_array'
                ]);
                
                if (is_array($flightSegmentBaggage)) {
                    foreach ($flightSegmentBaggage as $baggage) {
                        if (isset($baggage['Code'])) {
                            $apiCode = $baggage['Code'];
                            $weight = isset($baggage['Weight']) ? intval($baggage['Weight']) : null;
                            
                            Log::info('Analyzing API baggage code', [
                                'api_code' => $apiCode,
                                'weight' => $weight,
                                'frontend_weight' => $frontendWeight
                            ]);
                            
                            // Add exact match
                            if ($apiCode === $frontendCode) {
                                $possibleCodes[] = $apiCode;
                                Log::info('Added exact code match', ['code' => $apiCode]);
                            }
                            
                            // Weight-based matching
                            if ($weight && $frontendWeight && $weight == $frontendWeight) {
                                $possibleCodes[] = $apiCode;
                                Log::info('Added weight-based match', ['api_code' => $apiCode, 'weight' => $weight]);
                            }
                            
                            // Pattern-based matching
                            if ($frontendWeight) {
                                // Check if API code contains the weight
                                if (str_contains($apiCode, (string)$frontendWeight)) {
                                    $possibleCodes[] = $apiCode;
                                    Log::info('Added pattern-based match', ['api_code' => $apiCode, 'pattern' => $frontendWeight]);
                                }
                                
                                // Check common patterns: BG20 -> EB20, BG20 -> BG20#1, etc.
                                $weightStr = str_pad($frontendWeight, 2, '0', STR_PAD_LEFT);
                                $commonPatterns = [
                                    "EB{$frontendWeight}",
                                    "EB{$weightStr}",
                                    "BG{$frontendWeight}",
                                    "BG{$weightStr}",
                                    "{$frontendWeight}KG",
                                    "{$weightStr}KG"
                                ];
                                
                                foreach ($commonPatterns as $pattern) {
                                    if (str_contains($apiCode, $pattern) || str_starts_with($apiCode, $pattern)) {
                                        $possibleCodes[] = $apiCode;
                                        Log::info('Added common pattern match', ['api_code' => $apiCode, 'pattern' => $pattern]);
                                    }
                                }
                            }
                        }
                    }
                } else {
                    // Handle direct baggage object case
                    if (isset($flightSegmentBaggage['Code'])) {
                        $possibleCodes[] = $flightSegmentBaggage['Code'];
                        Log::info('Added direct baggage code', ['code' => $flightSegmentBaggage['Code']]);
                    }
                }
            }
        }
        
        // Remove duplicates and return
        $possibleCodes = array_unique($possibleCodes);
        
        Log::info('Final dynamic baggage code mapping', [
            'frontend_code' => $frontendCode,
            'possible_api_codes' => $possibleCodes,
            'mapping_count' => count($possibleCodes),
            'has_ssr_data' => !empty($ssrData)
        ]);
        
        return $possibleCodes;
    }

    public function bookAndTicket($traceId, $flight )
    {
        $tokenId = $this->authenticate();
        $flight = (array) $flight;

        // Fetch SSRData
        $ssrData = null;
        try {
            $ssrResponse = $this->getSSRData($traceId, $flight['ResultIndex'],$tokenId);
            if ($ssrResponse && isset($ssrResponse['Response']['ResponseStatus']) && $ssrResponse['Response']['ResponseStatus'] == 1) {
                $ssrData = $ssrResponse['Response'];
                
            }  else {
                Log::warning('SSRData API failed for flight ID: ' .  $ssrResponse ?: []);
            }
        } catch (\Exception $e) {
            Log::error('SSR API exception for flight: ' . $flight['ResultIndex'], ['error' => $e->getMessage()]);
        }


        // Fetch FareRule data 
        $fareRuleData = null;
        $FareRules = null;
        try {
            $fareRuleResponse = (new TBOFlightBookingService)->fareRules($traceId, $flight['ResultIndex'],$tokenId);
            Log::info('fareRuleResponse: '. json_encode($fareRuleResponse));
            if ($fareRuleResponse  && $fareRuleResponse['Response']  && isset($fareRuleResponse['Response']['ResponseStatus']) && $fareRuleResponse['Response']['ResponseStatus'] == 1) {
                $fareRuleData = $fareRuleResponse['Response']['FareRules'];
                $FareRules = $fareRuleData;
              
            } else {
                Log::warning('FareRule API failed for flight ID: ' , $fareRuleResponse ?: []);
            }
        } catch (\Exception $e) {
            Log::error('FareRule API exception for flight ID: ' . ', Error: ' . $e->getMessage());
        }

        // Fetch FareQuote data
        $fareQuoteData = null;
        $Fare_BE = null;
        $Segments_BE = null;
        $AirlineCode = null;
        $ValidatingAirline = null;
        $LastTicketDate = null;
        $MiniFareRules = null;
        $FareClassification = null;
        /* 
        var jsonData = pm.response.json();
            pm.environment.set("Fare_BE", JSON.stringify(jsonData.Response.Results.Fare));

            pm.environment.set("Segments_BE", JSON.stringify(jsonData.Response.Results.Segments[0]));

            pm.environment.set("AirlineCode", JSON.stringify(jsonData.Response.Results.AirlineCode));

            pm.environment.set("ValidatingAirline", jsonData.Response.Results.ValidatingAirline);

            pm.environment.set("LastTicketDate", JSON.stringify(jsonData.Response.Results.LastTicketDate));

            pm.environment.set("MiniFareRules", JSON.stringify(jsonData.Response.Results.MiniFareRules));

            pm.environment.set("FareClassification", JSON.stringify(jsonData.Response.Results.FareClassification));

        */
        try {
            Log::info('Testing existing trace_id validity with FareQuote for flight ID: ' );
            $fareQuoteResponse = (new TBOFlightBookingService)->fareQuote($traceId, $flight['ResultIndex'],$tokenId);
            Log::info('fareQuoteResponse: '. json_encode($fareQuoteResponse));
            if ($fareQuoteResponse && $fareQuoteResponse['Response'] &&  isset($fareQuoteResponse['Response']['ResponseStatus']) && $fareQuoteResponse['Response']['ResponseStatus'] == 1) {
                $fareQuoteData = $fareQuoteResponse['Response']['Results'];
                $Fare_BE = $fareQuoteData['Fare'];
                //$Segments_BE = $fareQuoteData['Segments'][0];
                $Segments_BE = [];
                foreach ($fareQuoteData['Segments'] as $segmentArray) {
                    $Segments_BE = array_merge($Segments_BE, $segmentArray);
                }
                $AirlineCode = $fareQuoteData['AirlineCode'];
                $ValidatingAirline = $fareQuoteData['ValidatingAirline'];
                $LastTicketDate = $fareQuoteData['LastTicketDate'];
                $MiniFareRules = $fareQuoteData['MiniFareRules'];
                $FareClassification = $fareQuoteData['FareClassification'];
                //Log::info('Existing trace_id is valid - FareQuote successful for flight ID: ' . $flight->id);
        } else {
            Log::warning('FareQuote API failed for flight ID: ' .  $fareQuoteResponse ?: []);
            return null;
        }
        } catch (\Exception $e) {
            Log::error('FareQuote API exception for flight ID: ' .   ', Error: ' . $e->getMessage());
        }

        $Destination = null;
        $Origin = null;
        
        if (is_array($FareRules) && !empty($FareRules)) {
            $lastFareRule = end($FareRules);
            $firstFareRule = reset($FareRules);
            
            $Destination = is_object($firstFareRule) ? $firstFareRule->Destination : ($firstFareRule['Destination'] ?? null);
            $Origin = is_object($firstFareRule) ? $firstFareRule->Origin : ($firstFareRule['Origin'] ?? null);
        } else {
            // Fallback to segment data if FareRules is not available
            if (isset($Segments_BE[0])) {
                $firstSegment = is_array($Segments_BE[0]) ? $Segments_BE[0] : (array)$Segments_BE[0];
                $lastSegment = is_array($Segments_BE) ? end($Segments_BE) : (array)end($Segments_BE);
                
                $Origin = $firstSegment['Origin']['Airport']['CityCode'] ?? 'DEL';
                $Destination = $firstSegment['Destination']['Airport']['CityCode'] ?? 'JED';
            } else {
                // Ultimate fallback
                $Origin = 'DEL';
                $Destination = 'JED';
            }
        }

        $todayDate = now()->format('Y-m-d');
        $passengers = [];
        $sessionPassengers = session('passengers', []);
       
       
        foreach ($sessionPassengers as $key => $passenger) {
            // Get SSR selections from passenger data directly
            $passengerMeal = $passenger['selected_meal'] ?? 'none';
            $passengerBaggage = $passenger['selected_baggage'] ?? 'none';
            
            Log::info("Processing SSR for passenger {$key} in ticketing", [
                'passenger_name' => ($passenger['first_name'] ?? 'Unknown') . ' ' . ($passenger['last_name'] ?? 'Unknown'),
                'meal_selection' => $passengerMeal,
                'baggage_selection' => $passengerBaggage,
                'meal_price' => $passenger['meal_price'] ?? 0,
                'baggage_price' => $passenger['baggage_price'] ?? 0
            ]);
            
            // Create SSR objects using TBO API data
            $mealSSR = $this->createMealSSR($passengerMeal, $ssrData, $Origin, $Destination);
            $baggageSSR = $this->createBaggageSSR($passengerBaggage, $ssrData, $Origin, $Destination);
            
            Log::info("SSR creation results for passenger {$key} in bookAndTicket", [
                'passenger_name' => ($passenger['first_name'] ?? 'Unknown') . ' ' . ($passenger['last_name'] ?? 'Unknown'),
                'baggage_input' => $passengerBaggage,
                'baggage_ssr_result' => $baggageSSR,
                'meal_ssr_result' => $mealSSR,
                'origin' => $Origin,
                'destination' => $Destination,
                'has_ssr_data' => !empty($ssrData)
            ]);
            
            $passengers[] = (object) [
                'PassportIssueCountryCode' => null,
                'PassportIssueDate' => '0001-01-01T00:00:00',
                'PaxId' => 0,
                'Title' => $passenger['title'],
                'FirstName' => $passenger['first_name'],
                'LastName' => $passenger['last_name'],
                'Mobile1' => $passenger['phone'],
                'Mobile1CountryCode' => '966',
                'Mobile2' => '-',
                'Mobile2CountryCode' => '',
                'IsLeadPax' => true,
                'DateOfBirth' => $passenger['birth_date'] . 'T00:00:00' ?? "1985-05-08T00:00:00",
                'Type' => $passenger['pax_type'],
                'PassengerIdType' => 0,
                'PassengerIdNo' => null,
                'PassengerIdExpiry' => '0001-01-01T00:00:00',
                'PassengerIdIssueDate' => '0001-01-01T00:00:00',
                'PassengerIdIssueCountryCode' => null,
                'PassportNo' => isset($passenger['passport_number']) && $passenger['passport_number'] ? $passenger['passport_number'] : null,
                'Nationality' => [
                    'CountryCode' => $passenger['nationality'],
                    'CountryName' => Country::where('code', $passenger['nationality'])->first()->name_en,
                ],
                'Country' => [
                    'CountryCode' =>  $passenger['nationality'],
                    'CountryName' => Country::where('code', $passenger['nationality'])->first()->name_en,
                ],
                'City' => [
                    'CountryCode' => null,
                    'CityCode' => 'QAJ',
                    'CityName' => 'Ajman City',
                ],
                'AddressLine1' => $passenger['address'],
                'AddressLine2' => 'Default Address Line 2', // Mandatory field for TBO API
                'Gender' => $passenger['gender'],
                'Email' => $passenger['email'],
                'Meal' => $passengerMeal !== 'none' ? $passengerMeal : null,
                'Seat' => null,
                'Fare_BE' => $Fare_BE,
                'FFAirline' => null,
                'FFNumber' => null,
                'PaxKey' => strtoupper(substr($passenger['last_name'] ?? 'USER', 0, 5) . substr($passenger['first_name'] ?? 'TEST', 0, 6) . ($passenger['title'] ?? 'MR')),
                'TboAirPaxId' => 0,
                'PassportExpiry' => isset($passenger['passport_expiry_date']) ? $passenger['passport_expiry_date'] . 'T00:00:00' : '0001-01-01T00:00:00',
                'PaxBaggage' => $baggageSSR,
                'PaxMeal' => $mealSSR,
                'IDCardNo' => null,
                'ZipCode' => null,
                'PaxSeat' => null,
                'Ticket' => null,
                'PaxStatus' => 0,
                'TurkeyNumber' => null,
                'SavePaxDetails' => true,
                'IsTboAirBaggageAdded' => false,
                'IdDetailCode' => null,
                'DocumentDetails' => null,
                'IdDetails' => [
                    [
                        'PaxId' => 0,
                        'IdType' => 1,
                        'IdNumber' => isset($passenger['passport_number']) && $passenger['passport_number'] ? $passenger['passport_number'] : null,
                        'IssuedCountryCode' => null,
                        'IssueDate' => null,
                        'ExpiryDate' => isset($passenger['passport_expiry_date']) ? $passenger['passport_expiry_date'] . 'T00:00:00' : '0001-01-01T00:00:00',
                    ],
                ],
            ];
        };
        $todayDate = now()->format('Y-m-d');
        $leadPassenger = $passengers[0];
        $userData = ($leadPassenger->Email ?? 'sdfs@gmail.com') . ',' . 
                   ($leadPassenger->Mobile1CountryCode ?? '966') . '-' . ($leadPassenger->Mobile1 ?? '4355353') . ',' .
                   ($leadPassenger->Title ?? 'Mr') . ' ' . ($leadPassenger->FirstName ?? 'FirstName') . ' ' . ($leadPassenger->LastName ?? 'LastName');

        Log::info('SEGMENTS:');
        Log::info(json_encode($Segments_BE, JSON_PRETTY_PRINT));


        $bookingRequest =(object) [
            "ResultId" => $flight['ResultIndex'],
            "Itinerary" => (object) [
                "AgencySalesRepresentative" => 0,
                "IsHoldEligibleForLcc" => false,
                "IsManual" => false,
                "IssuancePCC" => null,
                "FlightId" => 0,
                "Segments_BE" => $Segments_BE,
                "Passenger" => $passengers,
                "FareRules" => $FareRules,
                "PNR" => "",
                "InactivePNR" => null,
                "SplitPNR" => null,
                "SupplierCode" => null,
                "FlightBookingSource" => 72,
                "Destination" => $Destination,
                "FareType" => "PUBL",
                "LastTicketDate" => $LastTicketDate,
                "LastVoidDate" => "0001-01-01T00:00:00",
                "Origin" => $Origin,
                "CreatedOn" => $todayDate,
                "TboAirBookingSourceId" => 0,
                "PaymentMode" => 0,
                //"SourceSessionId" => "03e32262-9dd6-4248-a304-ade5f182edb3",
                "FailedBookingId" => 0,
                "ValidatingAirline" => null,
                "ValidatingAirlineCode" => $ValidatingAirline,
                "Ticketed" => false,
                "IsDomestic" => false,
                "AirlineCode" => $AirlineCode,
                "TravelDate" => "2024-05-24T21:20:00",
                "NonRefundable" => true,
                "BookingId" => 0,
                "BookingMode" => 1,
                "AgentRefNo" => null,
                "IsLcc" => $fareQuoteData['IsLCC'],
                "ClientIP" => request()->ip(),
                "AirlineRemark" => $fareQuoteData['AirlineRemark'] ?? null,
                "SearchType" => 1,
                "OnBehalfOf" => 0,
                "EarnedLoyaltyPoints" => 0,
                "TrackingId" => $traceId,
                "SupplierGroupId" => 5,
                "TripId" => null,
                "TripName" => "Anand Pathak Trip",
                "PNRStatus" => 0,
                "StaffRemarks" => null,
                "PricingKeyDetail" => null,
                "TokenId" => $tokenId,
                "SSRData" => null,
                "isNewBooking" => false,
                "ParentBookingId" => 0,
                "isFromBulkImport" => false,
                "isApplicableforNewWidgetWallet" => false,
                "callBackUrl" => "tboairdemo.techmaster.in",
                "isCancellationcoverAdded" => false,
                "FoidDetails" => [],
                "BookingOperations" => null,
                "UpsellOptionsList" => null,
                "PaymentKey" => null,
                "HotelConfirmationNumber" => null,
                "HelpCenter" => null,
                "MiniFareRules" => $MiniFareRules,
                "FareClassification" => $FareClassification,
                "CustomizedFareType" => null,
                //"IsVATApplicable" => true,
                "ResultType" => 2
            ],
            "PNR" => "",
            "BookingId" => "",
            "CorporateCode" => null,
            "ConfirmPriceChangeTicket" => false,
            "IPAddress" => request()->ip(),
            "IsGenerateTicketRequestFromQueues" => false,
            "SegmentAnalyticsToken" => "fdhzelbwysit22iibnxsghsj",
            "TokenId" => $tokenId,
            "TrackingId" => $traceId,
            "EndUserBrowserAgent" => "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Safari/537.36",
            "PointOfSale" => "IN",
            "RequestOrigin" => "India-https://www.tboair.com",
            "UserData" =>  $userData,
            "WebServerIP" => null
        ];
     


        if($fareQuoteData['IsLCC']){
           
        


            Log::info('Five: ticket-flight-Request ');

            Log::info(json_encode($bookingRequest, JSON_PRETTY_PRINT));
            $responseTicket = Http::timeout(1900)->post(config('services.tbo_flight.after_booking').'/Booking/Ticket/', $bookingRequest);
            $responseTicketData = $responseTicket->json();
            
            Log::info('Five: ticket-flight-response ');

            //Log::info($responseTicketData);
            Log::info(json_encode($responseTicketData, JSON_PRETTY_PRINT));

            

            return ['responseTicketData'=>$responseTicketData];

        }else{
            

                Log::info('Four: book-flight-Request ');

                Log::info(json_encode($bookingRequest, JSON_PRETTY_PRINT));
                $bookingResponse = Http::timeout(1900)->post(config('services.tbo_flight.after_booking').'/Booking/Book/', $bookingRequest);
                $responseData = $bookingResponse->json();
                
                Log::info('Four: book-flight-response ');

                Log::info(json_encode($responseData, JSON_PRETTY_PRINT));


                if (!((isset($responseData['IsSuccess']) && $responseData['IsSuccess']) || 
                (isset($responseData['Status']) && $responseData['Status'] == 1))) {
                    return $responseData;
                }
                


             
                //$responseData['IsPriceChanged'] = true;
                // First, check if the keys exist before accessing them
                if ((isset($responseData['IsPriceChanged']) && $responseData['IsPriceChanged'] === true) ||
                    (isset($responseData['IsTimeChanged']) && $responseData['IsTimeChanged'] === true)) {
                    
                    $errorType = '';
                    $message = '';
                    
                    // Determine error type
                    if (isset($responseData['IsPriceChanged']) && $responseData['IsPriceChanged'] === true && 
                        isset($responseData['IsTimeChanged']) && $responseData['IsTimeChanged'] === true) {
                        $errorType = 'price_and_time_changed';
                        $message = 'Flight fare and schedule have changed. Please search again.';
                    } elseif (isset($responseData['IsPriceChanged']) && $responseData['IsPriceChanged'] === true) {
                        $errorType = 'price_changed';
                        $message = 'Fare has changed. Please search again with current prices.';
                    } else {
                        $errorType = 'time_changed';
                        $message = 'Flight schedule has changed. Please search again.';
                    }
                    
                    Log::warning("$errorType detected - Attempting to cancel booking");
                    
                    // Only attempt cancellation if we have required parameters
                    if (isset($responseData['PNR']) && $responseData['PNR'] && isset($responseData['BookingId']) && $responseData['BookingId']) {
                        try {
                            $cancelRequest = [
                                'EndUserIp' => request()->ip(),
                                'TokenId' => $tokenId,
                                'PNR' => $responseData['PNR'],
                                'LastName' => "John",
                                'BookingId' => $responseData['BookingId']
                            ];
                            
                            Log::info('Attempting cancellation with request:', $cancelRequest);
                            
                            // Try different cancel endpoints
                            $cancelResponse = Http::timeout(30)->post(config('services.tbo_flight.after_booking').'/Booking/ReleasePNR/', $cancelRequest);
                            // OR try this alternative endpoint:
                            // $cancelResponse = Http::timeout(30)->post(config('services.tbo_flight.url').'/Booking/CancelBooking/', $cancelRequest);
                            
                            $cancelData = $cancelResponse->json();
                            Log::info('Cancellation response:', $cancelData);
                            
                        } catch (\Exception $e) {
                            Log::error('Cancellation failed:', ['error' => $e->getMessage()]);
                        }
                    } else {
                        Log::warning('Cannot cancel - Missing PNR or BookingId', [
                            'PNR' => $responseData['PNR'] ?? null,
                            'BookingId' => $responseData['BookingId'] ?? null
                        ]);
                    }
                    
                    return [
                        'status' => 'error',
                        'error_type' => $errorType,
                        'message' => $message,
                        'new_price' => $responseData['NewPrice'] ?? $responseData['Fare'] ?? null,
                       // 'original_price' => $bookingRequest['TotalPrice'] ?? null,
                        'cancelled' => true,
                        'response_data' => $responseData
                    ];
                }

                // If no price/time change detected, continue with normal flow
                if (!((isset($responseData['IsSuccess']) && $responseData['IsSuccess']) || 
                    (isset($responseData['Status']) && $responseData['Status'] == 1))) {
                    return $responseData;
                }

                $PNR = $responseData['PNR'] ?? null;

                if (!$PNR) {
                    Log::error('No PNR generated despite successful response');
                    return [
                        'status' => 'error', 
                        'message' => 'Booking failed - No PNR generated',
                        'response_data' => $responseData
                    ];
                }

 


                $PNR = $responseData['PNR'] ?? null;

                $bookingRequest->PNR = $PNR;

                if (isset($bookingRequest->Itinerary)) {
                    $bookingRequest->Itinerary->PNR = $PNR;
                }
        


                Log::info('Five: ticket-flight-Request ');

                Log::info(json_encode($bookingRequest, JSON_PRETTY_PRINT));
                $responseTicket = Http::timeout(1900)->post(config('services.tbo_flight.after_booking').'/Booking/Ticket/', $bookingRequest);
                $responseTicketData = $responseTicket->json();
                
                Log::info('Five: ticket-flight-response ');

                //Log::info($responseTicketData);
                Log::info(json_encode($responseTicketData, JSON_PRETTY_PRINT));

                

                return ['responseTicketData'=>$responseTicketData, 'responseBookingData'=>$responseData];

        }
        
       


    }

    /**
     * Direct ticketing for LCC flights (bypasses booking step)
     * For LCC airlines, this method generates PNR and invoice directly
     */
    public function ticketLCC($traceId, $flight, $fareRuleData = null, $fareQuoteData = null)
    {
        Log::info('Starting LCC direct ticketing for flight: ' . $flight['ResultIndex']);
        
        // Fetch SSR data from TBO API for accurate pricing and descriptions
        $ssrApiData = null;
        try {
            $ssrResponse = $this->getSSRData($traceId, $flight['ResultIndex']);
            if ($ssrResponse && isset($ssrResponse['Response']['ResponseStatus']) && $ssrResponse['Response']['ResponseStatus'] == 1) {
                $ssrApiData = $ssrResponse['Response'];
                Log::info('SSR API successful for LCC flight: ' . $flight['ResultIndex']);
            } else {
                Log::warning('SSR API failed for LCC flight: ' . $flight['ResultIndex'], $ssrResponse ?: []);
            }
        } catch (\Exception $e) {
            Log::error('SSR API exception for LCC flight: ' . $flight['ResultIndex'], ['error' => $e->getMessage()]);
        }
        
        // Call FareRule API before LCC ticketing if not already provided
        if (!$fareRuleData) {
            Log::info('Calling FareRule API before LCC ticketing for flight: ' . $flight['ResultIndex']);
            $fareRuleResponse = $this->fareRules($traceId, $flight['ResultIndex']);
            if (!$fareRuleResponse || !isset($fareRuleResponse['Response']['ResponseStatus']) || $fareRuleResponse['Response']['ResponseStatus'] != 1) {
                Log::error('FareRule API failed for LCC flight: ' . $flight['ResultIndex'], $fareRuleResponse ?: []);
                // Continue with ticketing even if FareRule fails, but log the error
            } else {
                $fareRuleData = $fareRuleResponse['Response']['FareRules'] ?? $fareRuleResponse['Response']['FareRule'] ?? $fareRuleResponse;
                Log::info('FareRule API successful for LCC flight: ' . $flight['ResultIndex']);
            }
        }

        // Always call FareQuote API immediately before LCC ticketing for freshest data (TBO requirement)
        Log::info('Calling fresh FareQuote API immediately before LCC ticketing for flight: ' . $flight['ResultIndex']);
        $fareQuoteResponse = $this->fareQuote($traceId, $flight['ResultIndex']);
        if (!$fareQuoteResponse || !isset($fareQuoteResponse['Response']['ResponseStatus']) || $fareQuoteResponse['Response']['ResponseStatus'] != 1) {
            Log::error('Fresh FareQuote API failed for LCC flight: ' . $flight['ResultIndex'], $fareQuoteResponse);
            // Continue with ticketing with existing data, but log the error
        } else {
            $fareQuoteData = $fareQuoteResponse['Response']['Results'] ?? $fareQuoteResponse['Response'];
            Log::info('Fresh FareQuote API successful for LCC flight: ' . $flight['ResultIndex']);
        }

        // For LCC, we call ticket directly without booking
        return $this->ticket($traceId, $flight, null, $fareRuleData, $fareQuoteData);
    }

    public function cancel($request)
    {
        $response = Http::timeout(1900)->post(config('services.tbo_flight.after_booking').'/Booking/ReleasePNR/', [
            'TokenId' => $this->authenticate(),
            'LastName' => $request['last_name'],
            'PNR' => $request['pnr'],
        ]);

        $reservation = ReservationFlight::where('pnr', $request['pnr'])->first();
        $reservation->status = 3;
        $reservation->refunded = 1;
        $reservation->save();

        return $response->json();
    }

    public function refund($request)
    {
        $response = Http::timeout(1900)->post(config('services.tbo_flight.after_booking').'/Booking/RefundApi', [
            'TokenId'     => $this->authenticate(),
            'EndUserIp'   => request()->ip(),
            'BookingId'   => $request->booking_id,
            'PaxId'       => $request->pax_ids,
            'SegmentId'   => $request->segment_ids,
            'RequestType' => 'Refund',
            'Remarks'     => $request->remarks ?? 'Refund Request',
        ]);

        return $response->json();
    }

    /**
     * Get SSR data and cache it in session for reuse
     */
    public function getSSRData($traceId, $resultIndex)
    {
        // Check if SSR data is already cached in session
        $cacheKey = "ssr_api_data_{$traceId}_{$resultIndex}";
        $cachedSSR = session($cacheKey);
        
        if ($cachedSSR) {
            Log::info('Using cached SSR data', ['cache_key' => $cacheKey]);
            // Return in same format as fresh API call - wrap cached Response in full structure
            return ['Response' => $cachedSSR];
        }

        // Fetch SSR data from TBO API
        Log::info('Fetching SSR data from TBO API', [
            'trace_id' => $traceId,
            'result_index' => $resultIndex
        ]);
        
        $ssrResponse = $this->ssr($traceId, $resultIndex);
        
        Log::info('SSR API Response', $ssrResponse);

        if (isset($ssrResponse['Response'])) {
            $ssrData = $ssrResponse['Response'];
            
            // Cache the SSR data in session for reuse during booking process
            session([$cacheKey => $ssrData]);
            
            Log::info('SSR data cached successfully', ['cache_key' => $cacheKey]);
            return $ssrResponse; // Return full response structure for consistency
        }

        Log::warning('No SSR data found in API response', $ssrResponse);
        return [];
    }

    /**
     * Find meal data from SSR API response
     */
    private function findMealInSSR($mealCode, $ssrData)
    {
        if (empty($mealCode) || $mealCode === 'none') {
            return null;
        }

        // Debug: Log the actual SSR meal data structure
        Log::info('SSR Meal data structure debug', [
            'meal_code' => $mealCode,
            'ssr_meal_structure' => [
                'has_MealDynamic' => isset($ssrData['MealDynamic']),
                'MealDynamic_count' => isset($ssrData['MealDynamic']) ? count($ssrData['MealDynamic']) : 0,
                'has_Meal' => isset($ssrData['Meal']),
                'Meal_count' => isset($ssrData['Meal']) ? count($ssrData['Meal']) : 0
            ],
            'ssr_meal_sample' => isset($ssrData['MealDynamic'][0]) ? $ssrData['MealDynamic'][0] : (isset($ssrData['Meal'][0]) ? $ssrData['Meal'][0] : 'no_meal_data')
        ]);

        // For LCC airlines - check MealDynamic array
        if (isset($ssrData['MealDynamic']) && is_array($ssrData['MealDynamic'])) {
            // Handle nested array structure - check first flight segment
            foreach ($ssrData['MealDynamic'] as $segmentIndex => $flightSegmentMeals) {
                Log::info("Processing meal segment {$segmentIndex}", [
                    'is_array' => is_array($flightSegmentMeals),
                    'segment_data' => is_array($flightSegmentMeals) ? 'array_with_' . count($flightSegmentMeals) . '_items' : $flightSegmentMeals
                ]);
                
                if (is_array($flightSegmentMeals)) {
                    foreach ($flightSegmentMeals as $mealIndex => $meal) {
                        Log::info("Checking meal item {$mealIndex}", [
                            'meal_code' => $meal['Code'] ?? 'no_code',
                            'meal_description' => $meal['AirlineDescription'] ?? $meal['Description'] ?? 'no_description',
                            'meal_price' => $meal['Price'] ?? 'no_price',
                            'full_meal_data' => $meal
                        ]);
                        
                        if (isset($meal['Code']) && $meal['Code'] === $mealCode) {
                            Log::info('Found matching meal in MealDynamic SSR data', [
                                'meal_code' => $mealCode,
                                'matched_api_code' => $meal['Code'],
                                'meal_data' => $meal
                            ]);
                            return $meal;
                        }
                    }
                } else {
                    // Handle case where flightSegmentMeals is not an array (direct meal object)
                    if (isset($flightSegmentMeals['Code'])) {
                        Log::info("Checking direct meal object in MealDynamic", [
                            'meal_code' => $flightSegmentMeals['Code'],
                            'meal_data' => $flightSegmentMeals
                        ]);
                        
                        if ($flightSegmentMeals['Code'] === $mealCode) {
                            Log::info('Found matching meal in direct MealDynamic SSR data', [
                                'meal_code' => $mealCode,
                                'matched_api_code' => $flightSegmentMeals['Code'],
                                'meal_data' => $flightSegmentMeals
                            ]);
                            return $flightSegmentMeals;
                        }
                    }
                }
            }
        }

        // For Non-LCC airlines - check Meal array
        if (isset($ssrData['Meal']) && is_array($ssrData['Meal'])) {
            Log::info('Checking Meal array for Non-LCC airlines', [
                'meal_count' => count($ssrData['Meal'])
            ]);
            
            // Handle both direct array and nested array structure
            foreach ($ssrData['Meal'] as $mealIndex => $mealData) {
                Log::info("Processing meal data {$mealIndex}", [
                    'is_array' => is_array($mealData),
                    'meal_data_sample' => is_array($mealData) ? 'nested_array' : $mealData
                ]);
                
                if (is_array($mealData)) {
                    // Nested structure
                    foreach ($mealData as $subMealIndex => $meal) {
                        Log::info("Checking nested meal item {$subMealIndex}", [
                            'meal_code' => $meal['Code'] ?? 'no_code',
                            'meal_data' => $meal
                        ]);
                        
                        if (isset($meal['Code']) && $meal['Code'] === $mealCode) {
                            Log::info('Found matching meal in nested Meal SSR data', [
                                'meal_code' => $mealCode,
                                'matched_api_code' => $meal['Code'],
                                'meal_data' => $meal
                            ]);
                            return $meal;
                        }
                    }
                } else {
                    // Direct structure
                    if (isset($mealData['Code'])) {
                        Log::info("Checking direct meal data", [
                            'meal_code' => $mealData['Code'],
                            'meal_data' => $mealData
                        ]);
                        
                        if ($mealData['Code'] === $mealCode) {
                            Log::info('Found matching meal in direct Meal SSR data', [
                                'meal_code' => $mealCode,
                                'matched_api_code' => $mealData['Code'],
                                'meal_data' => $mealData
                            ]);
                            return $mealData;
                        }
                    }
                }
            }
        }

        Log::info('No matching meal found in SSR data using strict matching, trying fallback patterns', [
            'meal_code' => $mealCode,
            'checked_MealDynamic' => isset($ssrData['MealDynamic']),
            'checked_Meal' => isset($ssrData['Meal'])
        ]);
        
        // Fallback: Try pattern matching for common meal code variations
        $mealPatterns = $this->getMealCodePatterns($mealCode);
        
        // Check MealDynamic with patterns
        if (isset($ssrData['MealDynamic']) && is_array($ssrData['MealDynamic'])) {
            foreach ($ssrData['MealDynamic'] as $flightSegmentMeals) {
                if (is_array($flightSegmentMeals)) {
                    foreach ($flightSegmentMeals as $meal) {
                        if (isset($meal['Code']) && in_array($meal['Code'], $mealPatterns)) {
                            Log::info('Found meal using pattern matching in MealDynamic', [
                                'original_code' => $mealCode,
                                'matched_pattern' => $meal['Code'],
                                'meal_data' => $meal
                            ]);
                            return $meal;
                        }
                    }
                }
            }
        }
        
        // Check Meal with patterns
        if (isset($ssrData['Meal']) && is_array($ssrData['Meal'])) {
            foreach ($ssrData['Meal'] as $mealData) {
                if (is_array($mealData)) {
                    foreach ($mealData as $meal) {
                        if (isset($meal['Code']) && in_array($meal['Code'], $mealPatterns)) {
                            Log::info('Found meal using pattern matching in nested Meal', [
                                'original_code' => $mealCode,
                                'matched_pattern' => $meal['Code'],
                                'meal_data' => $meal
                            ]);
                            return $meal;
                        }
                    }
                } else {
                    if (isset($mealData['Code']) && in_array($mealData['Code'], $mealPatterns)) {
                        Log::info('Found meal using pattern matching in direct Meal', [
                            'original_code' => $mealCode,
                            'matched_pattern' => $mealData['Code'],
                            'meal_data' => $mealData
                        ]);
                        return $mealData;
                    }
                }
            }
        }

        Log::info('No matching meal found even with pattern matching - dumping full SSR structure for debugging', [
            'meal_code' => $mealCode,
            'meal_patterns_tried' => $mealPatterns,
            'full_ssr_meal_dump' => [
                'MealDynamic' => $ssrData['MealDynamic'] ?? 'no_meal_dynamic',
                'Meal' => $ssrData['Meal'] ?? 'no_meal'
            ]
        ]);

        return null;
    }

    /**
     * Get possible meal code patterns for fallback matching
     */
    private function getMealCodePatterns($mealCode)
    {
        $patterns = [$mealCode]; // Always include original
        
        // Add common variations
        $upperCode = strtoupper($mealCode);
        $lowerCode = strtolower($mealCode);
        
        $patterns[] = $upperCode;
        $patterns[] = $lowerCode;
        
        // Add variations with/without trailing characters
        $patterns[] = $upperCode . '#1';
        $patterns[] = $upperCode . '_1';
        $patterns[] = $upperCode . '-1';
        
        // Remove duplicates
        return array_unique($patterns);
    }

 
  

    /**
     * Create meal SSR object with proper TBO structure using API data
     */
    private function createMealSSR($mealCode, $ssrData, $origin, $destination)
    {
        if (empty($mealCode) || $mealCode === 'none') {
            Log::info('No meal SSR requested', ['meal_code' => $mealCode]);
            return null;
        }

        Log::info('Creating meal SSR', [
            'meal_code' => $mealCode,
            'origin' => $origin,
            'destination' => $destination,
            'has_ssr_data' => !empty($ssrData),
            'ssr_structure' => [
                'has_MealDynamic' => isset($ssrData['MealDynamic']),
                'MealDynamic_count' => isset($ssrData['MealDynamic']) ? count($ssrData['MealDynamic']) : 0,
                'has_Meal' => isset($ssrData['Meal']),
                'Meal_count' => isset($ssrData['Meal']) ? count($ssrData['Meal']) : 0
            ]
        ]);

        // Try to find meal in SSR API data first
        $mealFromAPI = $this->findMealInSSR($mealCode, $ssrData);
        
        if ($mealFromAPI) {
            Log::info('Found meal in SSR API data', $mealFromAPI);
            return [
                'Code' => $mealFromAPI['Code'],
                'Description' => $mealFromAPI['AirlineDescription'] ?? $this->getMealDescription($mealCode),
                'AirlineCode' => $mealFromAPI['AirlineCode'] ?? 'SG',
                'Amount' => floatval($mealFromAPI['Price'] ?? 0),
                'Currency' => $mealFromAPI['Currency'] ?? 'SAR',
                'Origin' => $mealFromAPI['Origin'] ?? $origin,
                'Destination' => $mealFromAPI['Destination'] ?? $destination
            ];
        }

        // Fallback to default data if not found in SSR API
        $fallbackMeal = [
            'Code' => $mealCode,
            'Description' => $this->getMealDescription($mealCode),
            'AirlineCode' => 'SG',
            'Amount' => 0.0,
            'Currency' => 'SAR',
            'Origin' => $origin,
            'Destination' => $destination
        ];
        
        Log::info('Using fallback meal data (not found in SSR API)', $fallbackMeal);
        return $fallbackMeal;
    }

  

    /**
     * Validate and log SSR data structure for debugging
     */
    private function validateSSRData($userSSRData, $methodName = '')
    {
        Log::info("SSR Data Validation for {$methodName}", [
            'structure' => [
                'has_meal_array' => isset($userSSRData['meal']) && is_array($userSSRData['meal']),
                'has_baggage_array' => isset($userSSRData['baggage']) && is_array($userSSRData['baggage']),
                'meal_count' => isset($userSSRData['meal']) ? count($userSSRData['meal']) : 0,
                'baggage_count' => isset($userSSRData['baggage']) ? count($userSSRData['baggage']) : 0,
            ],
            'full_data' => $userSSRData
        ]);
        
        return isset($userSSRData['meal']) && isset($userSSRData['baggage']);
    }

    /**
     * Get meal description for meal codes
     */
    private function getMealDescription($mealCode)
    {
        $descriptions = [
            'VGML' => 'Vegetarian Meal',
            'MOML' => 'Muslim Meal (Halal)',
            'KSML' => 'Kosher Meal',
            'CHML' => 'Child Meal',
            'HNML' => 'Hindu Meal',
            'JNML' => 'Jain Meal',
            'AVML' => 'Asian Vegetarian Meal',
            'BBML' => 'Baby Meal',
            'DBML' => 'Diabetic Meal',
            'FPML' => 'Fruit Platter',
            'GFML' => 'Gluten Free Meal',
            'LCML' => 'Low Calorie Meal',
            'LPML' => 'Low Protein Meal',
            'LSML' => 'Low Salt Meal',
            'NLML' => 'Non Lactose Meal',
            'ORML' => 'Oriental Meal',
            'PFML' => 'Peanut Free Meal',
            'RVML' => 'Raw Vegetarian Meal',
            'SFML' => 'Seafood Meal',
            'SPML' => 'Special Meal',
            'VOML' => 'Vegetarian Oriental Meal'
        ];

        return $descriptions[$mealCode] ?? 'Special Meal';
    }

    /**
     * Check if the flight is LCC based on the flight data
     */
    private function isLccFlight($flightData = null)
    {
        // First check if we have flight data with isLcc field
        if ($flightData && isset($flightData['isLcc'])) {
            return (bool) $flightData['isLcc'];
        }

        // Check session data for flight information
        $flight = session('flight', []);
        if (isset($flight['isLcc'])) {
            return (bool) $flight['isLcc'];
        }

        // Fallback: Check if airline is commonly known LCC
        $lccAirlines = [
            '6E', // IndiGo
            'SG', // Spice Jet
            'AK', // Air Asia
            'G8', // Go Air
            'I5', // Air Asia India
            'FZ', // Fly Dubai
            'G9', // Air Arabia
            'UK', // Vistara (technically not LCC but sometimes acts like one)
        ];

        // Try to get airline code from flight data or session
        $airlineCode = null;
        if ($flightData && isset($flightData['ValidatingAirlineCode'])) {
            $airlineCode = $flightData['ValidatingAirlineCode'];
        } elseif (isset($flight['ValidatingAirlineCode'])) {
            $airlineCode = $flight['ValidatingAirlineCode'];
        }

        if ($airlineCode && in_array($airlineCode, $lccAirlines)) {
            return true;
        }

        // Default to false (Non-LCC) if we can't determine
        Log::info('Could not determine if flight is LCC, defaulting to Non-LCC');
        return false;
    }

    /**
     * Parse SSR response based on airline type (LCC vs Non-LCC)
     */
    public function parseSSRResponse($ssrResponse, $isLcc = null)
    {
        if (!isset($ssrResponse['Response'])) {
            return null;
        }

        $response = $ssrResponse['Response'];

        // Check for errors first
        if (isset($response['ResponseStatus']) && $response['ResponseStatus'] == 4) {
            Log::warning('SSR API session expired', ['response' => $response]);
            return null;
        }

        if (!isset($response['ResponseStatus']) || $response['ResponseStatus'] != 1) {
            Log::warning('SSR API failed', ['response' => $response]);
            return null;
        }
        
        // Auto-detect if LCC if not provided
        if ($isLcc === null) {
            $isLcc = $this->isLccFlight();
        }

        Log::info('Parsing SSR response', [
            'is_lcc' => $isLcc,
            'has_baggage' => isset($response['Baggage']),
            'has_meal_dynamic' => isset($response['MealDynamic']),
            'has_meal' => isset($response['Meal']),
            'has_seat_preference' => isset($response['SeatPreference'])
        ]);

        $parsedResponse = [
            'is_lcc' => $isLcc,
            'MealDynamic' => [],
            'baggage' => [],
            'seats' => []
        ];

        if ($isLcc) {
            // LCC Airlines - structured data with pricing
            if (isset($response['MealDynamic']) && is_array($response['MealDynamic'])) {
                foreach ($response['MealDynamic'] as $meal) {
                    $parsedResponse['meals'][] = [
                        'code' => $meal['Code'] ?? '',
                        'description' => $meal['AirlineDescription'] ?? $meal['Description'] ?? $this->getMealDescription($meal['Code'] ?? ''),
                        'price' => floatval($meal['Price'] ?? 0),
                        'currency' => $meal['Currency'] ?? 'SAR',
                        'origin' => $meal['Origin'] ?? '',
                        'destination' => $meal['Destination'] ?? '',
                        'airline_code' => $meal['AirlineCode'] ?? '',
                        'flight_number' => $meal['FlightNumber'] ?? ''
                    ];
                }
            }

            if (isset($response['Baggage']) && is_array($response['Baggage'])) {
                foreach ($response['Baggage'] as $baggage) {
                    $parsedResponse['baggage'][] = [
                        'code' => $baggage['Code'] ?? '',
                        'weight' => $baggage['Weight'] ?? '',
                        'description' => $baggage['Text'] ?? $baggage['Description'] ?? "Extra {$baggage['Weight']} Baggage",
                        'price' => floatval($baggage['Price'] ?? 0),
                        'currency' => $baggage['Currency'] ?? 'SAR',
                        'origin' => $baggage['Origin'] ?? '',
                        'destination' => $baggage['Destination'] ?? '',
                        'airline_code' => $baggage['AirlineCode'] ?? '',
                        'flight_number' => $baggage['FlightNumber'] ?? ''
                    ];
                }
            }
        } else {
            // Non-LCC Airlines - indicative options only
            if (isset($response['Meal']) && is_array($response['Meal'])) {
                foreach ($response['Meal'] as $meal) {
                    $parsedResponse['meals'][] = [
                        'code' => $meal['Code'] ?? '',
                        'description' => $meal['Description'] ?? $this->getMealDescription($meal['Code'] ?? ''),
                        'price' => 0, // Non-LCC meals are usually included or indicative
                        'currency' => 'SAR',
                        'origin' => '',
                        'destination' => '',
                        'airline_code' => '',
                        'flight_number' => ''
                    ];
                }
            }

            // Non-LCC typically don't have paid baggage options in SSR
            // They may have seat preferences
            if (isset($response['SeatPreference']) && is_array($response['SeatPreference'])) {
                foreach ($response['SeatPreference'] as $seat) {
                    $parsedResponse['seats'][] = [
                        'code' => $seat['Code'] ?? '',
                        'description' => $seat['Description'] ?? '',
                        'price' => 0, // Non-LCC seat preferences are usually indicative
                        'currency' => 'SAR'
                    ];
                }
            }
        }

        Log::info('SSR response parsed', [
            'meals_count' => count($parsedResponse['meals']),
            'baggage_count' => count($parsedResponse['baggage']),
            'seats_count' => count($parsedResponse['seats'])
        ]);

        return $parsedResponse;
    }

    /**
     * Get booking details for a flight reservation
     * Used to check status updates for InProgress bookings (Status = 5)
     */
    public function getBookingDetails($pnr, $bookingId = null, $tokenId = null)
    {
        try {
            Log::info('Fetching flight booking details', [
                'pnr' => $pnr,
                'booking_id' => $bookingId,
                'has_token' => !empty($tokenId)
            ]);

            $requestData = [
                'TokenId' => $tokenId ?? $this->authenticate(),
                'PNR' => $pnr,
                'EndUserIp' => request()->ip()
            ];

            // Add BookingId if provided (more specific lookup)
            if (!empty($bookingId)) {
                $requestData['BookingId'] = $bookingId;
            }

            // Call TBO GetBookingDetail API
            $response = Http::timeout(120)->post(config('services.tbo_flight.after_booking').'/Booking/GetBookingDetails/', $requestData);
            $responseData = $response->json();

            Log::info('Flight booking details response', [
                'pnr' => $pnr,
                'booking_id' => $bookingId,
                'response_status' => $response->status(),
                'response_data' => $responseData
            ]);

            // Check for API errors
            if (isset($responseData['Response']['Error'])) {
                Log::error('TBO GetBookingDetail API error', [
                    'pnr' => $pnr,
                    'error' => $responseData['Response']['Error']
                ]);
                return [
                    'status' => 'error',
                    'error' => $responseData['Response']['Error'],
                    'response' => $responseData
                ];
            }

            // Check for successful response
            if (isset($responseData['Response']['ResponseStatus']) && $responseData['Response']['ResponseStatus'] == 1) {
                Log::info('Successfully retrieved flight booking details', [
                    'pnr' => $pnr,
                    'booking_status' => $responseData['BookingStatus'] ?? 'unknown',
                    'ticket_status' => $responseData['TicketStatus'] ?? 'unknown'
                ]);

                return [
                    'status' => 'success',
                    'booking_details' => $responseData,
                    'booking_status' => $responseData['BookingStatus'] ?? null,
                    'ticket_status' => $responseData['TicketStatus'] ?? null,
                    'pnr' => $responseData['PNR'] ?? $pnr,
                    'booking_id' => $responseData['BookingId'] ?? $bookingId
                ];
            }

            // Handle other response statuses
            Log::warning('TBO GetBookingDetail API returned non-success status', [
                'pnr' => $pnr,
                'response_status' => $responseData['Response']['ResponseStatus'] ?? 'unknown',
                'response' => $responseData
            ]);

            return [
                'status' => 'failed',
                'message' => 'Unable to retrieve booking details',
                'response' => $responseData
            ];

        } catch (\Exception $e) {
            Log::error('Exception while fetching flight booking details', [
                'pnr' => $pnr,
                'booking_id' => $bookingId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'status' => 'error',
                'error' => $e->getMessage(),
                'exception' => true
            ];
        }
    }
}
