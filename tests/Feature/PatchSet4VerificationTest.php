<?php

namespace Tests\Feature;

use App\Jobs\FetchBookingDetailsJob;
use App\Jobs\FetchFlightBookingDetailsJob;
use App\Models\Reservation;
use App\Models\ReservationHotel;
use App\Models\ReservationPassenger;
use App\Models\User;
use App\Services\ReservationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class PatchSet4VerificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_state_changing_routes_are_post_only_for_target_endpoints(): void
    {
        $routes = collect(Route::getRoutes()->getRoutes());

        $targets = [
            'logout',
            'privilege-change-status/{id}',
            'delete-all-reservations',
            'cancel-hotel-reservations/{id}',
            'cancel-flight-reservations/{id}',
            'admin/check-inprogress-flights/{pnr?}',
            'moyasar-success',
        ];

        foreach ($targets as $target) {
            $matched = $routes->first(function ($route) use ($target) {
                return str_contains($route->uri(), $target);
            });

            $this->assertNotNull($matched, "Route not found for target: {$target}");

            if ($target === 'moyasar-success') {
                $allMatches = $routes->filter(fn ($route) => str_contains($route->uri(), $target));
                $methods = $allMatches->flatMap(fn ($route) => $route->methods())->unique()->values()->all();
                $this->assertContains('POST', $methods);

                continue;
            }

            $methods = $matched->methods();
            $this->assertContains('POST', $methods, "Expected POST method for {$target}");
            $this->assertNotContains('GET', $methods, "GET must not exist for state-changing route {$target}");
        }
    }

    public function test_complete_booking_twice_does_not_double_book_hotel_supplier(): void
    {
        $user = User::factory()->create();

        $reservation = Reservation::create([
            'user_id' => $user->id,
            'type' => 2,
            'price' => 150,
            'currency' => 'SAR',
            'status' => 0,
            'search_request' => json_encode([['Adults' => 1, 'Children' => 0, 'ChildrenAges' => []]]),
            'booking_reference_id' => 'BKTEST123',
        ]);

        ReservationHotel::create([
            'reservation_id' => $reservation->id,
            'hotel_id' => 'HOTEL123',
            'check_in' => '2026-04-01',
            'check_out' => '2026-04-03',
            'rooms' => 1,
            'adults' => 1,
            'children' => 0,
            'price' => 150,
            'currency' => 'SAR',
            'hotel_name' => 'Test Hotel',
            'hotel_image' => 'img.jpg',
            'hotel_address' => 'Riyadh',
            'hotel_rate' => '4',
            'booking_code' => 'HCODE123',
            'status' => 0,
        ]);

        ReservationPassenger::create([
            'reservation_id' => $reservation->id,
            'first_name' => 'John',
            'last_name' => 'Doe',
            'pax_type' => 1,
            'birth_date' => '1990-01-01',
            'email' => 'john@example.com',
            'phone' => '500000000',
            'nationality' => 'SA',
            'gender' => 'male',
            'address' => 'Riyadh',
            'passport_number' => 'P123',
            'passport_expiry_date' => '2030-01-01',
            'title' => 'Mr',
        ]);

        Queue::fake();

        $hotelService = $this->mock(\App\Services\TBOHotelBookingService::class);
        $hotelService->shouldReceive('bookingRoom')->once()->andReturn([
            'Status' => ['Code' => 200],
            'ConfirmationNumber' => 'CN-123',
        ]);
        $hotelService->shouldReceive('bookingDetails')->andReturn([
            'Rooms' => [['IsRefundable' => true]],
            'Status' => ['Code' => 200],
        ]);

        $first = ReservationService::completeBooking($reservation->id, 'test-corr-1');
        $second = ReservationService::completeBooking($reservation->id, 'test-corr-2');

        $this->assertTrue($first['success']);
        $this->assertTrue($second['success']);

        $reservation->refresh();
        $this->assertFalse((bool) $reservation->booking_in_progress);

        Queue::assertPushed(FetchBookingDetailsJob::class, 1);
    }

    public function test_job_dedup_unique_keys_are_stable_for_same_targets(): void
    {
        $hotelJobA = new FetchBookingDetailsJob(10, 'BK-10');
        $hotelJobB = new FetchBookingDetailsJob(10, 'BK-10');
        $flightJobA = new FetchFlightBookingDetailsJob(20, 'PNR20', 30, 15);
        $flightJobB = new FetchFlightBookingDetailsJob(20, 'PNR20', 30, 15);

        $this->assertSame($hotelJobA->uniqueId(), $hotelJobB->uniqueId());
        $this->assertSame($flightJobA->uniqueId(), $flightJobB->uniqueId());
        $this->assertGreaterThan(0, $hotelJobA->uniqueFor);
        $this->assertGreaterThan(0, $flightJobA->uniqueFor);
    }

    public function test_complete_booking_exception_releases_processing_lock(): void
    {
        $user = User::factory()->create();

        $reservation = Reservation::create([
            'user_id' => $user->id,
            'type' => 2,
            'price' => 150,
            'currency' => 'SAR',
            'status' => 0,
            'search_request' => json_encode([['Adults' => 1, 'Children' => 0, 'ChildrenAges' => []]]),
            'booking_reference_id' => 'BKTEST500',
        ]);

        ReservationHotel::create([
            'reservation_id' => $reservation->id,
            'hotel_id' => 'HOTEL500',
            'check_in' => '2026-04-01',
            'check_out' => '2026-04-03',
            'rooms' => 1,
            'adults' => 1,
            'children' => 0,
            'price' => 150,
            'currency' => 'SAR',
            'hotel_name' => 'Test Hotel',
            'hotel_image' => 'img.jpg',
            'hotel_address' => 'Riyadh',
            'hotel_rate' => '4',
            'booking_code' => 'HCODE500',
            'status' => 0,
        ]);

        ReservationPassenger::create([
            'reservation_id' => $reservation->id,
            'first_name' => 'Jane',
            'last_name' => 'Doe',
            'pax_type' => 1,
            'birth_date' => '1990-01-01',
            'email' => 'jane@example.com',
            'phone' => '500000001',
            'nationality' => 'SA',
            'gender' => 'female',
            'address' => 'Riyadh',
            'passport_number' => 'P500',
            'passport_expiry_date' => '2030-01-01',
            'title' => 'Ms',
        ]);

        $hotelService = $this->mock(\App\Services\TBOHotelBookingService::class);
        $hotelService->shouldReceive('bookingRoom')->once()->andThrow(new \RuntimeException('supplier timeout'));

        $result = ReservationService::completeBooking($reservation->id, 'test-corr-ex');
        $reservation->refresh();

        $this->assertFalse($result['success']);
        $this->assertFalse((bool) $reservation->booking_in_progress);
    }
}
