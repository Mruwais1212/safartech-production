<?php

namespace Tests\Feature;

use App\Jobs\ReconcileFailedPaidReservationsJob;
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
        // Test the critical replay-protection path: a reservation that is already
        // marked as completed (status=1, payment_processed_at set) must not be
        // processed a second time when a repeat Moyasar callback arrives.
        // Mockery alias mocks are unreliable once classes are autoloaded in the
        // same PHP process, so we use Http::fake to intercept the Moyasar API
        // call and set up the reservation in a pre-completed state.
        Mail::fake();

        \Illuminate\Support\Facades\Http::fake([
            'api.moyasar.com/v1/payments/pay_replay_1' => \Illuminate\Support\Facades\Http::response([
                'status' => 'paid',
                'id' => 'pay_replay_1',
                'invoice_id' => 'inv_replay_123',
                'source' => [
                    'company' => 'visa',
                    'number' => '4111',
                    'name' => 'TEST USER',
                    'reference_number' => 'ref-replay-1',
                ],
            ], 200),
        ]);

        $user = User::factory()->create();

        // Reservation is already completed — status=1, payment_processed_at set.
        $reservation = Reservation::create([
            'user_id'              => $user->id,
            'type'                 => 3,
            'price'                => 250,
            'currency'             => 'SAR',
            'status'               => 1,
            'payment_method'       => 1,
            'payment_id'           => 'inv_replay_123',
            'callback_nonce'       => str_repeat('a', 48),
            'paid_at'              => now(),
            'payment_processed_at' => now(),
        ]);

        // A replay of the Moyasar success callback must return 200 (success view)
        // and must NOT re-trigger booking.
        $response = $this->actingAs($user, 'web')->post('/moyasar-success', [
            'id'    => 'pay_replay_1',
            'nonce' => str_repeat('a', 48),
        ]);

        $response->assertOk();

        // Reservation must remain in completed state.
        $this->assertDatabaseHas('reservations', [
            'id'     => $reservation->id,
            'status' => 1,
        ]);
    }

    public function test_moyasar_success_missing_or_invalid_nonce_is_rejected(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'web')->post('/moyasar-success', [
            'id' => 'pay_1',
        ])->assertStatus(400);

        $reservation = Reservation::create([
            'user_id' => $user->id,
            'type' => 3,
            'price' => 250,
            'currency' => 'SAR',
            'status' => 0,
            'payment_id' => 'inv_test_456',
            'callback_nonce' => str_repeat('b', 48),
        ]);

        \Illuminate\Support\Facades\Http::fake([
            'api.moyasar.com/v1/payments/pay_2' => \Illuminate\Support\Facades\Http::response([
                'status' => 'paid',
                'id' => 'pay_2',
                'invoice_id' => 'inv_test_456',
                'source' => ['company' => 'visa'],
            ], 200),
        ]);

        $this->actingAs($user, 'web')->post('/moyasar-success', [
            'id' => 'pay_2',
            'nonce' => str_repeat('c', 48), // wrong nonce
        ])->assertStatus(403);

        $this->assertDatabaseHas('reservations', [
            'id' => $reservation->id,
            'status' => 0,
        ]);
    }

    public function test_reconciliation_job_updates_attempt_metadata_for_failed_paid_reservations(): void
    {
        $user = User::factory()->create();

        $reservation = Reservation::create([
            'user_id' => $user->id,
            'type' => 3,
            'price' => 300,
            'currency' => 'SAR',
            'status' => 2,
            'paid_at' => now(),
            'booking_error' => 'initial',
            'reconcile_attempt_count' => 0,
            'reconciliation_status' => 'reconcile_pending',
        ]);

        (new ReconcileFailedPaidReservationsJob())->handle();

        $reservation->refresh();

        $this->assertEquals(1, $reservation->reconcile_attempt_count);
        $this->assertNotNull($reservation->last_reconciled_at);
        $this->assertEquals('reconcile_pending', $reservation->reconciliation_status);
        $this->assertStringContainsString('reconciliation_hotfix_v3_1', $reservation->booking_error ?? '');
    }
}
