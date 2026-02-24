<?php

namespace Tests\Feature;

use App\Models\Reservation;
use App\Models\ReservationFlight;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Mockery;
use Tests\TestCase;

class CriticalSecurityPatchesTest extends TestCase
{
    use RefreshDatabase;

    public function test_invoice_returns_403_for_other_user(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();

        $reservation = Reservation::create([
            'user_id' => $owner->id,
            'type' => 3,
            'price' => 100,
            'currency' => 'SAR',
            'status' => 0,
        ]);

        $this->actingAs($otherUser, 'web')
            ->get('/generate-invoice/'.$reservation->id)
            ->assertForbidden();
    }

    public function test_cancel_flight_returns_403_for_other_user(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();

        $reservation = Reservation::create([
            'user_id' => $owner->id,
            'type' => 3,
            'price' => 100,
            'currency' => 'SAR',
            'status' => 1,
        ]);

        ReservationFlight::create([
            'reservation_id' => $reservation->id,
            'result_index' => 'OB-123',
            'trace_id' => 'TRACE-123',
            'is_lcc' => 0,
            'is_refundable' => 1,
            'total_price' => 100,
            'is_direct' => 1,
            'pnr' => 'PNR-OWN-123',
        ]);

        DB::table('reservation_passengers')->insert([
            'reservation_id' => $reservation->id,
            'first_name' => 'Test',
            'last_name' => 'Owner',
            'title' => 'Mr',
            'pax_type' => 'Adult',
            'birth_date' => now()->subYears(30)->toDateString(),
            'email' => 'owner@example.com',
            'phone' => '500000001',
            'nationality' => 'SA',
            'gender' => 'male',
            'address' => 'Riyadh',
            'passport_number' => 'P123456',
            'passport_expiry_date' => now()->addYears(1)->toDateString(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->actingAs($otherUser, 'web')
            ->post('/cancel-flight/PNR-OWN-123')
            ->assertForbidden();
    }

    public function test_guest_users_cannot_access_invoice_and_cancellation_endpoints(): void
    {
        $owner = User::factory()->create();

        $reservation = Reservation::create([
            'user_id' => $owner->id,
            'type' => 3,
            'price' => 100,
            'currency' => 'SAR',
            'status' => 1,
        ]);

        ReservationFlight::create([
            'reservation_id' => $reservation->id,
            'result_index' => 'OB-123',
            'trace_id' => 'TRACE-123',
            'is_lcc' => 0,
            'is_refundable' => 1,
            'total_price' => 100,
            'is_direct' => 1,
            'pnr' => 'PNR-GUEST-123',
        ]);

        $this->get('/generate-invoice/'.$reservation->id)->assertRedirect();
        $this->post('/cancel-flight/PNR-GUEST-123')->assertRedirect();
    }

    public function test_moyasar_success_is_idempotent_and_does_not_double_complete_booking(): void
    {
        Mail::fake();

        $user = User::factory()->create();

        $reservation = Reservation::create([
            'user_id' => $user->id,
            'type' => 3,
            'price' => 250,
            'currency' => 'SAR',
            'status' => 0,
            'payment_method' => 0,
            'payment_id' => 'inv_test_123',
        ]);

        $moyasarMock = Mockery::mock('alias:App\\Services\\MoyasarPaymentService');
        $moyasarMock->shouldReceive('getInvoice')->twice()->andReturn([
            'status' => 'paid',
            'invoice_id' => 'inv_test_123',
            'source' => [
                'company' => 'visa',
                'number' => '4111',
                'name' => 'TEST USER',
                'reference_number' => 'ref-123',
            ],
        ]);

        $reservationServiceMock = Mockery::mock('alias:App\\Services\\ReservationService');
        $reservationServiceMock->shouldReceive('completeBooking')->once()->with($reservation->id)->andReturn([
            'success' => true,
            'errors' => [],
        ]);

        $this->actingAs($user, 'web')->get('/moyasar-success?id=pay_1')->assertOk();
        $this->actingAs($user, 'web')->get('/moyasar-success?id=pay_1')->assertOk();

        $this->assertDatabaseHas('reservations', [
            'id' => $reservation->id,
            'status' => 1,
        ]);

        $this->assertDatabaseCount('transaction_cards', 1);
    }
}
