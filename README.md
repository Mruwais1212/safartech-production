# SafarTech

AI-powered travel booking platform built on Laravel 11. Supports trip planning, flight and hotel search, reservations, payments (Moyasar), and a bilingual (Arabic/English) admin panel.

---

## Table of Contents

1. [Architecture Overview](#architecture-overview)
2. [Local Development Setup](#local-development-setup)
3. [Environment Variables Reference](#environment-variables-reference)
4. [Database Setup](#database-setup)
5. [Queue Worker Strategy](#queue-worker-strategy)
6. [Scheduler](#scheduler)
7. [Docker / Container Setup](#docker--container-setup)
8. [Production Deployment](#production-deployment)
9. [Rollback Procedure](#rollback-procedure)
10. [Monitoring & Health Checks](#monitoring--health-checks)

---

## Architecture Overview

| Layer | Technology |
|---|---|
| Framework | Laravel 11, PHP 8.2 |
| Frontend | Blade SSR + Vite |
| Database | MySQL 8 |
| Queue | Laravel database queue (default) |
| Cache / Session | Database (Redis optional) |
| HTTP runtime | Nginx + PHP-FPM |
| Process supervisor | Supervisord (inside PHP container) |
| Payments | Moyasar |
| AI planning | OpenAI GPT-4-turbo |
| Hotels | TBO Hotels, Agoda |
| Flights | TBO Flights, Travelopro |
| Push notifications | Firebase FCM |
| Localization | `mcamara/laravel-localization` (ar / en) |

### Route groups

- `/en/` and `/ar/` — public website
- `/en/admin-panel/` and `/ar/admin-panel/` — admin panel (separate guard)

---

## Local Development Setup

### Prerequisites

- PHP 8.2+ with extensions: `pdo_mysql`, `mbstring`, `xml`, `zip`, `gd`, `opcache`, `bcmath`, `pcntl`, `redis`
- Composer 2
- Node.js 18+ and npm
- MySQL 8
- (Optional) Redis

### Steps

```bash
# 1. Clone the repository
git clone <repo-url>
cd safartech-production

# 2. Install PHP dependencies
composer install

# 3. Install Node dependencies and build assets
npm install
npm run dev

# 4. Set up environment
cp .env.example .env
php artisan key:generate

# 5. Edit .env — fill in DB credentials and API keys (see reference below)
vi .env

# 6. Run migrations and seeders
php artisan migrate
php artisan db:seed   # optional — loads reference data

# 7. Generate JWT secret
php artisan jwt:secret

# 8. Create storage symlink
php artisan storage:link

# 9. Start the development server
php artisan serve
```

Visit `http://localhost:8000/en/` (website) or `http://localhost:8000/en/admin-panel/` (admin).

---

## Environment Variables Reference

Copy `.env.example` to `.env` and fill in each section. The table below highlights the keys most likely to cause startup failures if missing.

| Key | Required | Notes |
|---|---|---|
| `APP_KEY` | Yes | Generate with `php artisan key:generate` |
| `DB_*` | Yes | MySQL connection |
| `OPEN_AI_API_KEY` | Yes | **Not** `OPENAI_API_KEY` — the app reads this exact name |
| `OPEN_AI_URL` | Yes | Defaults to OpenAI chat completions endpoint |
| `GOOGLE_MAPS_API_KEY` | Yes | Attraction and restaurant photos in AI planner |
| `TBO_HOTEL_*` | Yes | TBO hotel search and booking |
| `TBO_FLIGHT_*` | Yes | TBO flight search and booking |
| `MOYASAR_SECRET_KEY` | Yes | Payment gateway — webhooks will fail without this |
| `JWT_SECRET` | Yes | Generate with `php artisan jwt:secret` |
| `FCM_SERVER_KEY` | Yes | Push notifications |
| `QUEUE_CONNECTION` | Yes | `database` (default) or `redis` |

See `.env.example` for the complete annotated list of every variable.

---

## Database Setup

```bash
# Create the database
mysql -u root -e "CREATE DATABASE safartech CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# Run all migrations
php artisan migrate

# (First deploy only) Seed reference data
php artisan db:seed

# Check migration status
php artisan migrate:status
```

### Migration safety

- Always run `php artisan migrate --pretend` before applying migrations to a production database.
- Migrations that add indexes check for existing indexes and are safe for both MySQL and PostgreSQL.

---

## Queue Worker Strategy

The project uses the **scheduler-dispatched short-worker** model:

- `bootstrap/app.php` schedules `queue:work --stop-when-empty --tries=3 --timeout=720` to run **every minute** with `withoutOverlapping()`.
- The scheduler starts a worker, it drains the queue, then exits. A new worker starts the next minute.
- This avoids the memory-leak risk of long-running workers and works without Horizon.

**Do not** add a second `queue:work` schedule in `routes/console.php` — the canonical definition lives in `bootstrap/app.php` only.

To switch to a long-running worker (e.g. when job volume justifies it):
1. Remove the `queue:work` line from `bootstrap/app.php` `withSchedule`.
2. Add `[program:queue-worker]` to `docker/supervisord.conf` with `numprocs=2`.
3. Update `QUEUE_CONNECTION` to `redis` for better performance.

---

## Scheduler

The scheduler fires once per minute. Registered commands:

| Command | Frequency | Purpose |
|---|---|---|
| `app:cache-t-b-o-hotel-in-database-command` | Daily 01:00 | Refreshes TBO hotel catalogue cache |
| `queue:work --stop-when-empty --tries=3 --timeout=720` | Every minute | Processes queued jobs |
| `reservations:reconcile-failed-paid` | Every 10 min (configurable) | Retries failed-paid bookings |
| `booking:monitor --alert-threshold=10` | Every 15 min | Health check for in-progress bookings |
| `queue:prune-failed-jobs --hours=168` | Daily | Cleans up week-old failed job records |

In Docker the scheduler runs inside the PHP container via supervisord. Outside Docker, add a cron entry:

```cron
* * * * * www-data cd /var/www/html && php artisan schedule:run >> /dev/null 2>&1
```

---

## Docker / Container Setup

The `docker-compose.yml` runs two services:

| Service | Image | Role |
|---|---|---|
| `app` | `nginx:latest` | HTTP reverse proxy, serves static assets |
| `php` | Built from `Dockerfile` | PHP-FPM + supervisord (scheduler inside) |

Nginx proxies PHP requests to `php:9000` via FastCGI. The nginx config lives in `docker/nginx.conf`.

### Build and start

```bash
# Copy and fill environment file
cp .env.example .env
# Edit .env with production values

# Build and start containers
docker compose up -d --build

# Follow logs
docker compose logs -f php
```

### First-run notes

- The entrypoint (`docker-entrypoint.sh`) waits for the database, runs `migrate --force`, caches config/routes/views, then starts supervisord.
- If `.env` is absent, it copies `.env.example` and generates an app key automatically — **replace the key and all credentials immediately** before any traffic.

---

## Production Deployment

### Prerequisites

- Provisioned MySQL instance (external to containers is recommended).
- All `.env` values filled in and verified.
- Docker and Docker Compose installed on the host.

### Deploy steps

```bash
# 1. Pull latest code
git fetch origin main
git checkout main
git pull origin main

# 2. (Optional) Review pending migrations
php artisan migrate --pretend   # or run inside container

# 3. Rebuild and restart containers (zero-downtime with --no-deps + rolling)
docker compose pull
docker compose up -d --build --remove-orphans

# 4. Verify health
curl -sf http://localhost/up && echo "OK"
docker compose exec php php artisan about
```

### Optimisation commands (run inside php container post-deploy)

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
```

The entrypoint runs `config:cache`, `route:cache`, and `view:cache` automatically on container start.

---

## Rollback Procedure

```bash
# 1. Identify the last known-good image tag or commit SHA
git log --oneline -10

# 2. Check out the previous release
git checkout <previous-sha>

# 3. Roll back the last migration batch (if schema changed)
php artisan migrate:rollback

# 4. Rebuild containers with the old code
docker compose up -d --build

# 5. Verify
curl -sf http://localhost/up && echo "OK"
```

If a migration rollback is not safe (destructive), restore from a database backup instead of rolling back the migration.

---

## Monitoring & Health Checks

| Endpoint | Description |
|---|---|
| `GET /up` | Laravel health check (HTTP 200 = healthy) |
| `GET /en/admin-panel/booking-health` | Booking system health dashboard |

### Key artisan monitors

```bash
# Check in-progress bookings and alert if threshold exceeded
php artisan booking:monitor --alert-threshold=10

# Manually trigger reconciliation of failed-paid bookings
php artisan reservations:reconcile-failed-paid

# View failed queue jobs
php artisan queue:failed
```

### Logs

Application logs go to `storage/logs/laravel.log`.
Container process logs (php-fpm, scheduler) go to `storage/logs/php-fpm.*.log` and `storage/logs/scheduler.*.log` respectively.

Set `LOG_LEVEL=error` in production and `LOG_LEVEL=debug` when diagnosing issues.
