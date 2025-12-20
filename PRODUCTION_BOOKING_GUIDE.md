# Booking Details System - Production Deployment Guide

## Overview
This guide ensures the booking details system works correctly in production with the TBO Hotel Booking API compliance requirements:
- Unique booking reference ID generation (max 25 characters)
- Automatic booking details fetching after 120 seconds
- Comprehensive monitoring and health checks

## 🚀 Production Setup Checklist

### 1. Database Setup
Ensure migrations are applied:
```bash
php artisan migrate
```

Check for these new columns:
- `reservations.booking_reference_id`
- `reservation_hotels.booking_reference_id`
- `reservation_hotels.booking_status`
- `reservation_hotels.booking_details_fetched_at`
- `reservation_hotels.booking_details_response`

### 2. Queue Worker Configuration

#### Configure Queue Driver
Ensure `.env` has:
```env
QUEUE_CONNECTION=database
```

#### Start Queue Worker (CRITICAL)
**This is REQUIRED for the 120-second booking details system to work:**

```bash
# For production - use supervisor or systemd
php artisan queue:work --sleep=3 --tries=3 --max-time=3600 --timeout=120

# For testing/development
php artisan queue:work --once
```

#### Production Queue Worker Setup (Recommended)

**Option A: Using Supervisor**
Create `/etc/supervisor/conf.d/laravel-worker.conf`:
```ini
[program:laravel-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /path/to/your/artisan queue:work database --sleep=3 --tries=3 --max-time=3600 --timeout=120
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/log/laravel-worker.log
stopwaitsecs=3600
```

**Option B: Using systemd**
Create `/etc/systemd/system/laravel-queue.service`:
```ini
[Unit]
Description=Laravel Queue Worker
After=redis.service

[Service]
User=www-data
Group=www-data
Restart=always
ExecStart=/usr/bin/php /path/to/your/artisan queue:work database --sleep=3 --tries=3 --max-time=3600 --timeout=120

[Install]
WantedBy=multi-user.target
```

### 3. Laravel Scheduler (Optional Enhancement)
For additional monitoring, add to crontab:
```cron
* * * * * cd /path/to/your/project && php artisan schedule:run >> /dev/null 2>&1
```

## 🔍 Testing & Monitoring

### Health Check Endpoints
Your production monitoring system can check these URLs:

1. **System Health Check**
   ```
   GET /health/booking-system
   ```
   Response includes queue status, recent jobs, and system health

2. **Individual Booking Details**
   ```
   GET /health/booking-details/{booking_id}
   ```
   Check specific booking compliance status

3. **Recent Bookings Status**
   ```
   GET /health/recent-bookings
   ```
   Overview of recent booking compliance

### Command Line Testing

#### 1. Test Booking Details Fetching
```bash
# Interactive testing with options
php artisan booking:test-details

# Test specific booking
php artisan booking:test-details --booking=123

# Test with immediate fetch (bypass 120s delay)
php artisan booking:test-details --booking=123 --immediate
```

#### 2. Monitor Booking Status
```bash
# Check all recent bookings
php artisan booking:check-status

# Check specific booking
php artisan booking:check-status --booking=123

# Check only failed bookings
php artisan booking:check-status --failed-only
```

#### 3. System Health Monitoring
```bash
# Comprehensive system health check
php artisan booking:monitor-system

# Continuous monitoring (runs every 30 seconds)
php artisan booking:monitor-system --continuous

# Alert mode (only shows issues)
php artisan booking:monitor-system --alert-only
```

## 🧪 Production Testing Workflow

### Before Deployment
1. **Test Queue Worker**
   ```bash
   php artisan queue:work --once
   ```
   Ensure it processes jobs without errors

2. **Validate Database Schema**
   ```bash
   php artisan migrate:status
   ```

3. **Test API Connectivity**
   ```bash
   php artisan booking:test-details --dry-run
   ```

### After Deployment
1. **Verify Health Endpoints**
   ```bash
   curl https://yourdomain.com/health/booking-system
   ```

2. **Monitor Queue Processing**
   ```bash
   php artisan queue:monitor
   # OR check database
   SELECT * FROM jobs WHERE queue = 'default' ORDER BY created_at DESC LIMIT 10;
   ```

3. **Test Complete Booking Flow**
   - Create a test booking
   - Verify booking_reference_id is generated (format: BK + 13-digit timestamp + 6-digit random)
   - Wait 2-3 minutes and check if booking details were fetched
   - Verify database updates

## 📊 Monitoring in Production

### Key Metrics to Monitor

1. **Queue Health**
   - Jobs processed successfully
   - Failed jobs count
   - Queue depth

2. **Booking Compliance**
   - Bookings with missing booking_reference_id
   - Failed booking details fetches
   - Timing compliance (120-second rule)

3. **Database Monitoring**
   ```sql
   -- Bookings without reference ID
   SELECT COUNT(*) FROM reservation_hotels WHERE booking_reference_id IS NULL;
   
   -- Failed booking details fetches
   SELECT COUNT(*) FROM reservation_hotels 
   WHERE booking_details_fetched_at IS NULL 
   AND created_at < NOW() - INTERVAL 5 MINUTE;
   
   -- Recent successful fetches
   SELECT COUNT(*) FROM reservation_hotels 
   WHERE booking_details_fetched_at IS NOT NULL 
   AND DATE(booking_details_fetched_at) = CURDATE();
   ```

### Alerting Setup
Monitor these conditions and alert if:
- Queue worker is down for > 5 minutes
- More than 5 failed booking details fetches in 1 hour
- Any booking older than 10 minutes without booking_reference_id
- Health check endpoints return error status

## 🚨 Troubleshooting

### Queue Worker Not Processing Jobs
```bash
# Check if queue worker is running
ps aux | grep "queue:work"

# Check failed jobs
php artisan queue:failed

# Restart queue worker
php artisan queue:restart
```

### Booking Reference ID Not Generated
```bash
# Check recent bookings
php artisan booking:check-status --recent

# Validate ReservationService
php artisan tinker
>>> App\Services\ReservationService::class
```

### Booking Details Not Fetched
```bash
# Check specific booking
php artisan booking:test-details --booking=ID --immediate

# Check queue table
SELECT * FROM jobs WHERE payload LIKE '%FetchBookingDetailsJob%';

# Check failed jobs
php artisan queue:failed
```

### API Connectivity Issues
```bash
# Test TBO API directly
php artisan booking:test-details --dry-run --verbose

# Check logs
tail -f storage/logs/laravel.log | grep "TBOHotelBookingService"
```

## 📋 Production Checklist

- [ ] Database migrations applied
- [ ] Queue connection configured as 'database'
- [ ] Queue worker running with supervisor/systemd
- [ ] Health check endpoints accessible
- [ ] TBO API credentials configured
- [ ] Monitoring commands available
- [ ] Alerting system configured
- [ ] Test booking created and verified
- [ ] 120-second compliance tested
- [ ] Failed job handling verified
- [ ] Backup procedures in place

## 📞 Support Commands Quick Reference

```bash
# System health
php artisan booking:monitor-system

# Test booking details
php artisan booking:test-details --booking=ID

# Check recent status
php artisan booking:check-status --recent

# Queue status
php artisan queue:work --once

# Health check URL
curl /health/booking-system
```

---

**Note**: The queue worker MUST be running for the booking details system to work. This is the most critical component for TBO API compliance.
