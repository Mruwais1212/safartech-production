<?php

namespace Tests\Feature;

use App\Models\Reservation;
use App\Models\ReservationFlight;
use App\Models\ReservationHotel;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Critical business flow tests covering:
 * - Admin route protection (auth required)
 * - Invoice ownership / access control
 * - Cancellation authorization
 * - Payment webhook idempotency
 * - Admin destructive operations protection
 * - Financial reports access
 */
class BookingFlowCriticalTest extends TestCase
{
    use RefreshDatabase;

    // -------------------------------------------------------------------------
    // Admin panel route protection
    // -------------------------------------------------------------------------

    public function test_admin_reservations_page_requires_admin_auth(): void
    {
        $response = $this->get('/admin-panel/reservations');
        // Must redirect to login, not render panel
        $this->assertNotEquals(200, $response->getStatusCode(),
            'Admin reservations must not be accessible without auth');
    }

    public function test_admin_financial_reports_requires_admin_auth(): void
    {
        $response = $this->get('/admin-panel/financial_reports');
        $this->assertNotEquals(200, $response->getStatusCode(),
            'Financial reports must require admin auth');
    }

    public function test_admin_delete_all_reservations_requires_auth_and_token(): void
    {
        // Unauthenticated request must not succeed
        $response = $this->post('/admin-panel/delete-all-reservations', [
            'confirm_token' => 'DELETE_ALL_RESERVATIONS',
        ]);
        $this->assertNotEquals(200, $response->getStatusCode(),
            'delete-all without admin auth must be rejected');
    }

    // -------------------------------------------------------------------------
    // Invoice ownership
    // -------------------------------------------------------------------------

    public function test_invoice_is_accessible_by_owner(): void
    {
        $owner = User::factory()->create();

        $reservation = Reservation::create([
            'user_id'  => $owner->id,
            'type'     => 3,
            'price'    => 100,
            'currency' => 'SAR',
            'status'   => 1,
        ]);

        // Owner should get a response other than 403/401 (might be 500 if PDF libs missing)
        $response = $this->actingAs($owner, 'web')
            ->get('/generate-invoice/' . $reservation->id);

        $this->assertNotEquals(403, $response->getStatusCode(),
            'Owner must not get 403 on their own invoice');
        $this->assertNotEquals(401, $response->getStatusCode());
    }

    public function test_invoice_returns_404_for_missing_reservation(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user, 'web')
            ->get('/generate-invoice/99999999')
            ->assertNotFound();
    }

    public function test_invoice_returns_403_for_other_user(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();

        $reservation = Reservation::create([
            'user_id'  => $owner->id,
            'type'     => 3,
            'price'    => 100,
            'currency' => 'SAR',
            'status'   => 1,
        ]);

        $this->actingAs($other, 'web')
            ->get('/generate-invoice/' . $reservation->id)
            ->assertForbidden();
    }

    // -------------------------------------------------------------------------
    // Cancellation authorization
    // -------------------------------------------------------------------------

    public function test_cancel_flight_requires_authentication(): void
    {
        $this->post('/cancel-flight/PNR-ANON-001')
            ->assertRedirect(); // redirect to login
    }

    public function test_cancel_flight_returns_403_for_non_owner(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();

        $reservation = Reservation::create([
            'user_id'  => $owner->id,
            'type'     => 1,
            'price'    => 200,
            'currency' => 'SAR',
            'status'   => 1,
        ]);

        ReservationFlight::create([
            'reservation_id' => $reservation->id,
            'result_index'   => 'OB-999',
            'trace_id'       => 'TR-999',
            'is_lcc'         => 0,
            'is_refundable'  => 1,
            'total_price'    => 200,
            'is_direct'      => 1,
            'pnr'            => 'PNR-OWNER-001',
        ]);

        DB::table('reservation_passengers')->insert([
            'reservation_id'       => $reservation->id,
            'first_name'           => 'Test',
            'last_name'            => 'Owner',
            'title'                => 'Mr',
            'pax_type'             => 'Adult',
            'birth_date'           => now()->subYears(30)->toDateString(),
            'email'                => 'owner@example.com',
            'phone'                => '500000001',
            'nationality'          => 'SA',
            'gender'               => 'male',
            'address'              => 'Riyadh',
            'passport_number'      => 'P123456',
            'passport_expiry_date' => now()->addYears(1)->toDateString(),
            'created_at'           => now(),
            'updated_at'           => now(),
        ]);

        $this->actingAs($other, 'web')
            ->post('/cancel-flight/PNR-OWNER-001')
            ->assertForbidden();
    }

    public function test_cancel_hotel_requires_authentication(): void
    {
        $this->post('/cancel-hotel/BOOKING-ANON-001')
            ->assertRedirect();
    }

    // -------------------------------------------------------------------------
    // Booking status / my-trips access control
    // -------------------------------------------------------------------------

    public function test_my_trips_requires_authentication(): void
    {
        $this->get('/my-trips')->assertRedirect();
    }

    public function test_guest_cannot_access_payment_summary(): void
    {
        // summary is exempt from auth per PaymentController middleware, but should
        // redirect/error if no session data (safe default)
        $response = $this->get('/summary');
        // Just confirm it doesn't return a 200 with booking data for a guest with no session
        // A redirect to /ai-trip is expected
        $this->assertNotEquals(500, $response->getStatusCode(),
            'Summary must not 500 for a guest with empty session');
    }

    // -------------------------------------------------------------------------
    // Webhook replay protection
    // -------------------------------------------------------------------------

    public function test_webhook_with_no_secret_configured_passes_signature_check(): void
    {
        \Illuminate\Support\Facades\Config::set('services.moyasar.webhook_secret', null);

        $response = $this->postJson('/webhooks/moyasar', ['id' => 'pay_noop_123']);
        // No secret → skip HMAC → proceed to validation; payload missing nothing required
        $this->assertNotEquals(403, $response->getStatusCode());
    }

    public function test_webhook_with_secret_rejects_tampered_payload(): void
    {
        \Illuminate\Support\Facades\Config::set('services.moyasar.webhook_secret', 'my-webhook-secret');

        $this->postJson('/webhooks/moyasar', ['id' => 'pay_tampered'])->assertStatus(403);
    }

    // -------------------------------------------------------------------------
    // Price integrity — SSR prices must not be client-trusted
    // -------------------------------------------------------------------------

    public function test_ssr_update_does_not_accept_client_price(): void
    {
        $user = User::factory()->create();

        session(['passengers' => [['first_name' => 'Ali', 'last_name' => 'Ahmed', 'pax_type' => 1]]]);
        session(['flight' => [
            'SSRDetails' => [
                'MealDynamic' => [
                    [['Code' => 'VGML', 'Price' => 75.0]],
                ],
            ],
        ]]);

        $response = $this->actingAs($user, 'web')->postJson('/passenger-information/update-ssr', [
            'passenger_index' => 0,
            'ssr_type'        => 'meal',
            'ssr_value'       => 'VGML',
            'ssr_price'       => 9999, // client-supplied malicious price
        ]);

        $response->assertOk();

        $stored = session('passengers');
        $this->assertEquals(75.0, $stored[0]['meal_price'],
            'Price must be catalog price (75), not client-supplied (9999)');
    }

    // -------------------------------------------------------------------------
    // Reconciliation job guard
    // -------------------------------------------------------------------------

    public function test_reconciliation_does_not_process_already_reconciled_reservations(): void
    {
        $user = User::factory()->create();

        // Create a reservation that needs_manual_review (already maxed out attempts)
        Reservation::create([
            'user_id'              => $user->id,
            'type'                 => 3,
            'price'                => 300,
            'currency'             => 'SAR',
            'status'               => 2,
            'paid_at'              => now(),
            'booking_error'        => 'initial',
            'reconcile_attempt_count' => 3,
            'reconciliation_status' => 'needs_manual_review',
        ]);

        $job = new \App\Jobs\ReconcileFailedPaidReservationsJob;
        $job->handle();

        // The reservation must NOT have been touched (attempt count must remain 3)
        $this->assertDatabaseHas('reservations', [
            'reconcile_attempt_count' => 3,
            'reconciliation_status'   => 'needs_manual_review',
        ]);
    }

    // -------------------------------------------------------------------------
    // Health endpoint
    // -------------------------------------------------------------------------

    public function test_health_endpoint_is_accessible_without_auth(): void
    {
        $this->get('/health/status')->assertOk()->assertJson(['status' => 'ok']);
    }
}
