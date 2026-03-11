# SafarTech OTA Platform — CTO-Level Technical Audit

**Date:** 2026-03-11
**Auditor:** Claude (Anthropic)
**Branch:** claude/audit-safartech-deployment-3fTaY
**Verdict:** ❌ NOT READY FOR PRODUCTION

---

## Output 1: Product-Aware Architecture Overview

### What SafarTech Is

SafarTech is a Laravel 11 OTA platform for the Saudi market that provides AI-driven trip planning plus real booking of flights and hotels through the TBO supplier API, with Moyasar as the payment gateway. It is a session-heavy, synchronous booking system with async job-based post-booking confirmation.

### Architecture Shape

```
Customer Web (routes/web.php)
  ↓ session-driven state machine
  [AI Planner] → [Flight Search + TBO] → [Hotel Search + TBO]
       ↓                ↓                        ↓
  [Passenger Info + SSR] → [Summary] → [Payment Init]
                                              ↓
                                    [Moyasar Invoice Created]
                                              ↓
                          [Success POST] ←──────── [Webhook POST]
                                    ↓
                         [ReservationService::completeBooking()]
                                    ↓
                    [TBOHotelBookingService::bookingRoom()]
                    [TBOFlightBookingService::bookAndTicket()]
                                    ↓
                         [FetchFlightBookingDetailsJob]  (delayed 15 min)
                         [FetchBookingDetailsJob]        (delayed 2 min)

Admin Panel (routes/panel.php — /admin-panel prefix)
  → Reservation management, financial reports
  → Cancel hotel/flight, balance management
  → Payment settings (gateway credentials)
  → User management, RBAC, CMS
```

### Key Architecture Facts

- **No API routes** (`api.php` does not exist). Everything runs on `web` middleware with session.
- **JWT auth is configured** but used only for the `api` guard, which serves no active routes.
- **PHP 8.4 + Laravel 11** on database queue/session/cache (no Redis).
- **Session is the booking state store**: TraceId, selected flight, hotel, passengers, SSR — all in session.
- **Booking completion runs synchronously on payment success**, calling TBO suppliers inside the web request that handles the Moyasar callback.
- **Two async jobs** handle supplier confirmation polling (flight 15-min delay, hotel 2-min delay).
- **Admin panel** is protected by `HasMiddleware` on the base `Panel\Controller` class (`auth:admin` + `permission`).

---

## Output 2: End-to-End Flow Map

### A. AI Trip Planner Flow
1. User visits `/ai-trip` → enters preferences (origin, destination, dates, budget, interests)
2. `AiTripController::store()` → stores preferences in session → calls OpenAI API to generate destinations
3. `AiTripPlannerController::show()` → generates full itinerary via GPT-4 with hotel/flight recommendations
4. User selects trip → `session(['preferences' => ...])` persists trip config
5. User continues to flight or hotel search

### B. Flight Booking Flow
1. `FlightController::search()` → calls `TBOFlightBookingService` → authenticates via TBO → searches flights → caches results
2. `FlightController::selectFlight()` → stores `outbound_flight` + `inbound_flight` + `traceId` in session
3. `PassengerInformationController` → user fills passenger details → SSR API called (meals/baggage) → stored in session
4. `PaymentController::summary()` → re-calls FareQuote + FareRules + SSR APIs using session TraceId → displays price breakdown

### C. Hotel Booking Flow
1. `HotelController::search()` → calls TBO hotel API → stores results
2. `HotelController::selectRoom()` → user selects room → `hotel.BookingCodeHotel` stored in session
3. `TBOPreBookController` → pre-booking validation → session updated
4. Hotel data persists alongside flight data in session

### D. Payment Flow
1. `PaymentController::payment()` (POST) → calculates final price (base + SSR costs) → calls `ReservationService::add()`
2. `ReservationService::add()` → **`updateOrCreate`** reservation → saves hotel/flight/passenger data from session → returns reservation
3. Controller creates Moyasar invoice → stores `payment_id` on reservation → redirects user to Moyasar
4. User completes payment at Moyasar
5. **Two parallel paths**:
   - `POST /moyasar-success` (user browser return) → verifies nonce + calls `ReservationService::completeBooking()`
   - `POST /webhooks/moyasar` (server-to-server) → verifies payment status + calls `ReservationService::completeBooking()`

### E. Booking Completion Flow
1. `ReservationService::completeBooking()` → acquires soft lock on reservation (15-min window)
2. For hotels: `TBOHotelBookingService::bookingRoom()` → dispatches `FetchBookingDetailsJob`
3. For flights (LCC + Non-LCC): `TBOFlightBookingService::bookAndTicket()` → handles Status=5 (InProgress) by dispatching `FetchFlightBookingDetailsJob` (15-min delay)
4. Reservation status set to `1` (success) or `2` (paid but failed)
5. Confirmation email sent via `BookingEmail` Mailable

### F. Admin Operations Flow
1. Admin logs in → `/admin-panel/login`
2. Views reservations → `ReservationController::index()` (all reservations loaded, no pagination)
3. Can cancel hotel/flight → `cancel_hotel` / `cancel_flight` with TBO API calls
4. Financial reports → filtered by date range, exportable via Excel
5. Refund → via `MoyasarPaymentService::refund()` call
6. Balance management → add/deduct from user wallet

---

## Output 3: Top Production Risks

### RISK 1 — Arbitrary View Disclosure (Path Traversal in Route)
**Severity: CRITICAL**
**Business Impact: Exposes internal blade templates to unauthenticated users**

**File:** `routes/web.php:10-12`
```php
Route::get('page/{page}', function ($page) {
    return view("website.$page");
});
```

Any unauthenticated visitor can call `/page/` with any value. Laravel's view resolution maps dots to directory separators. No input sanitization or whitelist exists.

**Fix direction:** Replace with explicit whitelist:
```php
$allowed = ['about', 'faq'];
if (!in_array($page, $allowed)) abort(404);
```

---

### RISK 2 — `updateOrCreate` Race Condition Destroys Booking Data
**Severity: CRITICAL**
**Business Impact: Customer who navigates back and retries payment overwrites a pending or paid reservation; child records deleted and recreated mid-flight**

**File:** `app/Services/ReservationService.php:115-124`
```php
$reservation = Reservation::updateOrCreate(
    ['user_id' => auth('web')->id(), 'payment_method' => 0],
    $reservationData
);
$reservation->hotel()->delete();
$reservation->flight()->delete();
$reservation->passengers()->delete();
```

The match condition is only `user_id` + `payment_method = 0`. Any pending (unpaid) reservation matches. If the user opens two tabs or navigates back:
- Booking A's data is overwritten with Booking B's data
- Moyasar processes Booking A's payment against now-wrong data
- Child record cascade delete runs without transaction protection

**Fix direction:** Always `create()` a new reservation. Clean up orphaned unpaid reservations via a scheduled job (`reservations:clean-orphaned-unpaid`).

---

### RISK 3 — `env()` Direct Calls in MoyasarPaymentService (Breaks with Config Cache)
**Severity: CRITICAL — Deployment Blocker**
**Business Impact: After `php artisan config:cache` (required in production), ALL payment processing fails silently**

**File:** `app/Services/MoyasarPaymentService.php:13, 17, 43, 46, 63, 67`
```php
Http::withBasicAuth(env('MOYASAR_SECRET_KEY'), '')
    ->post(env('MOYASAR_BASE_URL').'/v1/invoices', ...)
```

Laravel's `config:cache` causes `env()` to return `null`. The service authenticates with null credentials against a null URL.

**Additional callsites:** `GooglePlaceService`, `AiTripPlannerController`

**Fix direction:** Use `config('services.moyasar.secret_key')` throughout. Wire credentials into `config/services.php`.

---

### RISK 4 — Moyasar Webhook Has No HMAC Signature Verification
**Severity: HIGH**
**Business Impact: Unauthenticated endpoint; any source can trigger booking-completion logic**

**File:** `app/Http/Controllers/Webhooks/MoyasarWebhookController.php`

The webhook verifies payment status by calling back to Moyasar (partial mitigation), but does not verify the HMAC-SHA256 `Signature` header that Moyasar sends. The endpoint is open to:
- Enumeration of payment IDs
- Triggering Moyasar API lookups at rate-limit scale
- Race condition exploitation against the booking lock

**Fix direction:**
```php
if (!hash_equals(
    hash_hmac('sha256', $request->getContent(), config('services.moyasar.webhook_secret')),
    $request->header('Signature') ?? ''
)) {
    return response()->json(['message' => 'Unauthorized'], 401);
}
```

---

### RISK 5 — `queue:work` Running Inside the Scheduler
**Severity: HIGH — Deployment Blocker**
**Business Impact: Booking confirmation jobs for InProgress flights may never fire; multiple overlapping worker processes**

**File:** `bootstrap/app.php:78`
```php
$schedule->command('queue:work --stop-when-empty --tries=3 --timeout=720')
         ->everyMinute()->withoutOverlapping();
```

`queue:work` is a long-running daemon, not a one-shot command. This causes:
1. A new worker spawns every minute, stops when queue is empty
2. Jobs dispatched between ticks (up to 60 seconds) have zero workers
3. `FetchFlightBookingDetailsJob` dispatched with 15-min delay — worker that processes it may have exited
4. `--timeout=720` (12 min) can block the sole worker for an entire booking

**Fix direction:** Remove this scheduler entry. Configure Supervisor with 2+ persistent `queue:work --daemon` workers.

---

### RISK 6 — `MoyasarPaymentService::refund()` Uses HTTP GET Instead of POST
**Severity: HIGH**
**Business Impact: ALL refund operations silently fail; customers never receive their money**

**File:** `app/Services/MoyasarPaymentService.php:65`
```php
$response = Http::withBasicAuth(env('MOYASAR_SECRET_KEY'), '')
    ->get(env('MOYASAR_BASE_URL').'/v1/payments/'.$id.'/refund', ['amount' => $amount]);
```

The Moyasar API requires `POST` for refunds. A `GET` request returns 4xx or is silently ignored.

**Fix direction:** Change `->get(...)` to `->post(...)`.

---

### RISK 7 — SSR Cost Not Persisted to Reservation Price
**Severity: HIGH**
**Business Impact: DB reservation price is lower than amount charged to customer; financial records wrong; invoices show incorrect amounts; VAT calculation incorrect**

**Files:** `app/Http/Controllers/Website/PaymentController.php:217-233` vs `app/Services/ReservationService.php:56-63`

`PaymentController::payment()` computes `$finalTotalPrice = $baseTotalPrice + $totalSSRCost` and sends this to Moyasar. `ReservationService::add()` (called just before) recomputes price independently *without* SSR costs.

**Fix direction:** Accept the computed total price as a parameter to `ReservationService::add()`.

---

### RISK 8 — `request()->merge()` Inside Service Layer for Hotel Booking
**Severity: HIGH**
**Business Impact: Service coupled to HTTP layer; hotel booking cannot be triggered from queue, CLI, or tests; global request object mutated permanently**

**File:** `app/Services/ReservationService.php:515-523`
```php
request()->merge([
    'booking_code' => $reservation->hotel->booking_code,
    'customer_details' => $customer_details,
    'client_reference_id' => auth('web')->id(),
    ...
]);
$book = (new TBOHotelBookingService)->bookingRoom(request());
```

`auth('web')->id()` returns null in any queue context. The mutated request persists for the life of the request.

**Fix direction:** Pass a typed array/DTO to `bookingRoom()` instead of the request object.

---

### RISK 9 — `delete_all` Admin Function Truncates All Booking Data
**Severity: HIGH**
**Business Impact: One admin click permanently destroys all reservations, PNRs, passenger records, and financial history**

**File:** `app/Http/Controllers/Panel/Backend/ReservationController.php:24-35`
```php
public function delete_all() {
    DB::table('reservation_flights')->truncate();
    DB::table('reservation_hotels')->truncate();
    DB::table('reservation_passengers')->truncate();
    // ... 8 tables total
    return 'done';
}
```

No confirmation dialog, no soft delete, no audit trail, no backup trigger.

**Fix direction:** Remove from production. Gate behind `app()->environment('local')` or delete entirely.

---

### RISK 10 — Price Calculation Makes 4+ DB Queries Per Booking
**Severity: MEDIUM**
**Business Impact: Performance degradation; inconsistent pricing if settings change mid-flow**

**File:** `app/Helpers/PriceCalculationHelper.php:34-36`
```php
$service_fees_percent = Setting::where('code', 'flight_profit')->first()
    ? (float)Setting::where('code', 'flight_profit')->first()->value : 7;
```

Two queries per setting (existence check, then value fetch). Called from `ReservationService::add()` which also queries separately, totaling 6+ queries before any DB write.

**Fix direction:** `Setting::where('code', 'flight_profit')->value('value') ?? 7` + `Cache::remember('setting.flight_profit', 3600, ...)`.

---

### RISK 11 — CSP Is Report-Only (XSS Not Actually Blocked)
**Severity: MEDIUM**
**Business Impact: Any XSS in Blade templates is not blocked; CSP header provides false confidence**

**File:** `app/Http/Middleware/ContentSecurityPolicyReportOnly.php`

`Content-Security-Policy-Report-Only` reports violations but does not block them. The policy also uses `'unsafe-inline'` for scripts, which would negate most XSS protection even if enforced.

**Fix direction:** Transition to `Content-Security-Policy` header. Remove `'unsafe-inline'` using nonces.

---

### RISK 12 — Admin Reservation List Loads All Records Without Pagination
**Severity: MEDIUM**
**Business Impact: Admin page times out or OOMs as booking volume grows**

**File:** `app/Http/Controllers/Panel/Backend/ReservationController.php:18-20`
```php
$reservations = Reservation::with('hotel', 'flight')->filterDate()->get();
```

No `->paginate()`. With eager loading and no default date filter, this loads the entire booking history into memory.

**Fix direction:** Add `->paginate(50)`.

---

### RISK 13 — Booking Lock Window Shorter Than Possible Supplier Response Time
**Severity: MEDIUM**
**Business Impact: Slow TBO calls (>15 min for flight+hotel) allow concurrent webhook retry to run parallel booking, risking duplicate supplier charges**

**File:** `app/Services/ReservationService.php:421-427`

The 15-minute lock window can expire before combined flight+hotel booking completes on slow supplier networks.

**Fix direction:** Extend lock window to 30 minutes. Add monitoring on lock contention.

---

### RISK 14 — LogRedactor Masks Any Key Containing `'number'`
**Severity: LOW**
**Business Impact: Operational data (`flight_number`, `confirmation_number`, `booking_reference_number`) masked in logs; booking failure investigation impeded**

**File:** `app/Support/LogRedactor.php:15`

`str_contains($key, 'number')` matches `flight_number`, `confirmation_number`, etc.

**Fix direction:** Replace `'number'` with `'passport_number'`. Use suffix/exact matching for sensitive keys.

---

## Output 4: Deployment Readiness Score

| Dimension | Score | Key Reason |
|---|---|---|
| **Security** | 5/10 | Page LFI route, no webhook HMAC, Report-Only CSP, unencrypted PII, `env()` in payment service |
| **Reliability** | 5/10 | `updateOrCreate` race condition, wrong queue setup, refund via GET, SSR price mismatch, 15-min lock window |
| **Maintainability** | 5/10 | Hundreds of lines of dead commented code, `request()` mutation in services, static methods with auth coupling |
| **Observability** | 6/10 | Good: correlation IDs, structured logging, LogRedactor. Bad: over-broad redaction, no alerting on booking failures |
| **Deployability** | 4/10 | `env()` breaks with `config:cache`, `queue:work` in scheduler, incomplete `.env.example`, no migration rollback docs |

**Overall Production Readiness: 4/10 — NOT READY**

The system has clear signs of a working application under development with good structural intent (correlation IDs, lock patterns, idempotency checks), but several critical gaps that would cause financial losses on day one.

---

## Output 5: Concrete Go-Live Plan

### Phase 1: Critical Blockers — Must Fix Before Any Live Traffic

| Task | File(s) | Expected Outcome | Risk if Skipped |
|---|---|---|---|
| Fix `env()` → `config()` in MoyasarPaymentService | `app/Services/MoyasarPaymentService.php` | Payments work after `config:cache` | Zero payments processed |
| Fix `refund()` to use POST | `app/Services/MoyasarPaymentService.php:65` | Refunds execute | Every refund silently fails |
| Replace scheduler `queue:work` with Supervisor | `bootstrap/app.php:78` | Jobs process continuously | InProgress flight confirmations lost |
| Remove/disable `delete_all` in production | `app/Http/Controllers/Panel/Backend/ReservationController.php:24` | No catastrophic data loss button | Total DB wipe by admin mistake |
| Add HMAC verification to webhook | `app/Http/Controllers/Webhooks/MoyasarWebhookController.php` | Webhook authenticated | API abuse and enumeration |
| Fix `page/{page}` route whitelist | `routes/web.php:10-12` | No view path traversal | Internal template exposure |
| Fix `env()` in GooglePlaceService, AiTripPlannerController | Multiple files | AI features work after config cache | AI planner broken in production |

### Phase 2: High-Priority Stabilization — First Week After Launch

| Task | File(s) | Expected Outcome | Risk if Skipped |
|---|---|---|---|
| Fix `updateOrCreate` → always `create()` | `app/Services/ReservationService.php:115` | No booking data corruption | Concurrent payments corrupt reservations |
| Fix SSR cost persistence to reservation price | `PaymentController.php`, `ReservationService.php` | DB price matches charged amount | Financial records wrong; invoice incorrect |
| Decouple service from `request()` | `app/Services/ReservationService.php:515-523` | Service testable; queue-safe | Hotel booking fails in non-HTTP context |
| Cache Settings queries | `app/Helpers/PriceCalculationHelper.php` | 4–6 fewer DB queries per booking | Performance; inconsistent pricing |
| Add pagination to admin reservation list | `app/Http/Controllers/Panel/Backend/ReservationController.php` | Admin panel doesn't OOM | Page timeout with real volume |
| Fix LogRedactor sensitive key list | `app/Support/LogRedactor.php` | Booking refs appear in logs | Blind debugging of booking failures |

### Phase 3: Post-Launch Hardening

| Task | File(s) | Expected Outcome | Risk if Skipped |
|---|---|---|---|
| Encrypt PII at rest (passport numbers) | `app/Models/ReservationPassenger.php` | PDPL compliance | Regulatory exposure |
| Enforce CSP (not Report-Only) | `app/Http/Middleware/ContentSecurityPolicyReportOnly.php` | XSS actually blocked | False security confidence |
| Alert on `reconciliation_status = 'reconcile_pending'` | New alerting integration | Ops notified of paid-but-failed within minutes | Silent financial losses |
| Extend booking lock window to 30 min | `app/Services/ReservationService.php:421` | Reduced duplicate booking risk | Rare but possible duplicate charges |
| Remove dead code (commented blocks) | `ReservationService.php`, `PaymentController.php` | Maintainable codebase | Confusion about active logic |

---

## Output 6: Testing Plan

### Unit Tests

| Component | Test Cases |
|---|---|
| `PriceCalculationHelper` | Inside/outside VAT rates, SSR addition, merge logic, zero/negative prices |
| `MoyasarPaymentService` | Mock HTTP responses: paid, failed, network error, null credentials |
| `ReservationService::add()` | All 3 trip types, missing airport data, session variations |
| `ReservationService::completeBooking()` | Lock acquisition, already-processing guard, already-completed guard, hotel failure, flight failure |
| `LogRedactor` | Sensitive key masking, nested arrays, null values, `'number'` substring problem |
| `FetchFlightBookingDetailsJob::mapTboStatusToInternal()` | All TBO status codes including unknown |

### Feature Tests

| Scenario | What to Test |
|---|---|
| Full booking flow (each trip type) | Session state correctly built at each step |
| Payment success page | Correct nonce validation; `completeBooking()` called once |
| Duplicate webhook | Same payment webhooks twice → no double-booking |
| Reservation with SSR | SSR costs included in final charge |
| Admin cancel hotel/flight | TBO API called; reservation status updated |

### Payment Tests (Moyasar Sandbox)

| Scenario |
|---|
| Successful payment → booking confirmed |
| Failed payment → reservation stays pending |
| Webhook fires before success page → completed; success page shows already-done |
| Success page fires before webhook → no double booking |
| Webhook with wrong payment ID → 202 ignored |
| Webhook with missing HMAC → 401 (after fix) |
| Network timeout on invoice creation → error shown, no orphan reservation |

### Supplier Integration Tests (TBO Sandbox)

| Scenario |
|---|
| Flight bookAndTicket (LCC) → confirmed |
| Flight bookAndTicket (Non-LCC) → confirmed |
| TBO returns Status=5 (InProgress) → job dispatched → polling returns confirmed |
| TBO Status=5 → polling fails 3 times → `needs_manual_review=true` |
| Hotel bookingRoom → confirmed → FetchBookingDetailsJob confirms |
| Expired TraceId → correct error handling (not crash) |

### Job/Queue Tests

| Scenario |
|---|
| `FetchFlightBookingDetailsJob` uniqueness: same PNR dispatched twice → one runs |
| Job fires 15 min after dispatch (not immediately) |
| Job fails 3 times → `failed()` → `needs_manual_review=true` |
| `FetchBookingDetailsJob` fires 2 min after dispatch |
| Queue with Supervisor: restart worker mid-job → completion safety |

### Concurrency Tests

| Scenario |
|---|
| Two simultaneous POST to `/moyasar-success` for same reservation → one `completeBooking()` |
| Webhook + success page simultaneously → one booking attempt |
| Two browser tabs paying for different bookings → no data corruption (after fix) |
| `booking_in_progress` lock prevents re-entry within 15 minutes |

### Security Tests

| Scenario |
|---|
| `GET /page/anything` → only whitelisted pages served (after fix) |
| `POST /webhooks/moyasar` without valid HMAC → 401 (after fix) |
| `GET /admin-panel/reservations` without admin session → redirect to login |
| IDOR: User A views reservation of User B → 403 |
| Rate limit on `/payment` POST: 6th request in 1 min → 429 |
| Payment amount tampering: client-side modification → server recalculates from session |

### Staging / UAT Tests

- Full customer journey from homepage → booking confirmation email
- Admin cancels booked flight → status updated
- Financial report export to Excel with real booking data
- AI trip planner → book flight and hotel from generated plan
- Session expires mid-booking (TraceId lost) → error shown, not crash

### Post-Deploy Smoke Tests

- `GET /up` → 200 OK
- `GET /health/booking-system` (with auth) → 200
- Moyasar sandbox payment → reservation created → booking confirmed
- Queue worker: dispatch test job → runs within 60 seconds
- Admin panel login → dashboard loads

---

## Output 7: Deployment Checklist

### Environment / Config Validation
- [ ] All required `.env` variables present and non-empty
- [ ] `APP_ENV=production`, `APP_DEBUG=false`
- [ ] `APP_KEY` is set (32 characters)
- [ ] `MOYASAR_SECRET_KEY`, `MOYASAR_BASE_URL` in `.env` AND referenced via `config()` in code
- [ ] `TBO_FLIGHT_USERNAME`, `TBO_FLIGHT_PASSWORD`, `TBO_HOTEL_USERNAME`, `TBO_HOTEL_PASSWORD` set
- [ ] `OPEN_AI_API_KEY` set
- [ ] `SESSION_DRIVER=database` (or Redis), `SESSION_ENCRYPT=true` recommended
- [ ] `CACHE_STORE=database` or Redis — confirm cache table exists
- [ ] `QUEUE_CONNECTION=database` (or Redis for reliability)
- [ ] `SESSION_SECURE_COOKIE=true` (HTTPS only)
- [ ] `SESSION_SAME_SITE=lax`
- [ ] `MOYASAR_WEBHOOK_SECRET` configured for HMAC verification (after fix)

### Queue / Cron Setup
- [ ] **Remove** `queue:work` from `bootstrap/app.php` scheduler
- [ ] Configure Supervisor with minimum 2 `queue:work` workers
- [ ] Supervisor auto-restart configured on crash
- [ ] `php artisan queue:work --daemon --tries=3 --timeout=300` as persistent process
- [ ] Verify `failed_jobs` table exists (`php artisan queue:failed`)
- [ ] Cron running every minute: `* * * * * php /var/www/artisan schedule:run`
- [ ] Verify `reservations:reconcile-failed-paid` command exists and runs

### Cache / Session / Storage
- [ ] `php artisan config:cache`
- [ ] `php artisan route:cache`
- [ ] `php artisan view:cache`
- [ ] `php artisan storage:link` (public disk symlink)
- [ ] Session table exists and migrated
- [ ] Cache table exists and migrated

### Database Migration
- [ ] Backup database before running migrations
- [ ] Review each migration for destructive operations
- [ ] `php artisan migrate --force`
- [ ] Verify all migrations completed: `php artisan migrate:status`
- [ ] Test rollback on staging (not production): `php artisan migrate:rollback`

### Rollback Plan
- [ ] Tag the current production git commit before deploy
- [ ] Document: `git checkout <previous-tag> && php artisan migrate:rollback --step=N`
- [ ] Verify no migration in this release has missing `down()` method
- [ ] Database backup point verified and restorable
- [ ] Supervisor restart command documented for queue rollback

### Monitoring / Alerts
- [ ] Error alerting configured (Sentry, Flare, or similar)
- [ ] Alert on `reconciliation_status = 'reconcile_pending'` (paid but failed)
- [ ] Alert on `failed_jobs` table growth
- [ ] Alert on Moyasar API failures
- [ ] Uptime monitoring on `/up` and `/health/booking-system`
- [ ] Log aggregation configured

### Post-Deploy Verification
- [ ] `GET /up` → 200
- [ ] Admin panel login works
- [ ] One test booking in Moyasar sandbox completes end to end
- [ ] Queue processes a test job within 60 seconds
- [ ] Verify no `env()` returning null in logs (config cache sanity check)
- [ ] Verify at least one `FetchFlightBookingDetailsJob` dispatched and executed

---

## Final Verdict

### ❌ NOT READY

The system has good structural intent — correlation IDs, booking locks, idempotency checks, nonce-based replay prevention — but has **at least 5 issues that would cause direct financial losses on day one**:

1. `env()` in payment service breaks all payments after `config:cache`
2. Refund uses GET — all refunds silently fail
3. SSR costs not saved to reservation — financial records are wrong
4. `queue:work` in scheduler — booking confirmation jobs unreliable
5. `updateOrCreate` pattern can overwrite in-flight booking data

---

## The 10 Most Important Actions for the Business Owner

In priority order — do in sequence:

1. **Fix `env()` → `config()` in `MoyasarPaymentService.php` immediately.** Production deploy killer. Do this before anything else.

2. **Fix `MoyasarPaymentService::refund()` to use POST.** Every refund issued to customers is currently silently failing.

3. **Replace the scheduler `queue:work` with Supervisor.** Get a systems engineer to configure 2–3 persistent queue workers. Without this, flight confirmations for InProgress bookings are unreliable.

4. **Remove or permanently disable `delete_all` in `ReservationController`.** This is a data destruction button with no confirmation in your production admin panel.

5. **Add HMAC verification to the Moyasar webhook handler.** Security baseline before going live with real customer payments.

6. **Fix the `page/{page}` route** to use a whitelist. Remove it if unused.

7. **Fix the `updateOrCreate` pattern in `ReservationService::add()`.** The current implementation corrupts booking data if a user navigates back and retries. Test carefully before fixing.

8. **Fix the SSR cost persistence gap.** The amount charged to customers must match what is stored in the reservation. Affects financial reporting, VAT calculation, and invoice accuracy.

9. **Set up real-time alerting on `reconciliation_status = 'reconcile_pending'`** (paid but booking failed). You need ops visibility within minutes — not the next day.

10. **Do a full staging run-through of the complete customer journey** (AI planner → flight select → hotel select → passenger info → payment → confirmation email) before any live traffic. The session-driven state machine has many handoff points that only reveal failure modes during end-to-end execution.

---

*Audit completed: 2026-03-11 by Claude (Anthropic) — claude/audit-safartech-deployment-3fTaY*
