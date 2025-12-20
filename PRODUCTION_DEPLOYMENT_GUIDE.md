# 🚀 Production Deployment Guide for Booking Details Monitoring System

## 📋 Overview

This guide provides step-by-step instructions for deploying and monitoring the booking details compliance system in production.

### ✅ What This System Provides
- **Unique Booking Reference IDs**: Max 25 characters, format: `BK{timestamp}{random}`
- **Automatic Booking Details Fetching**: Delayed by exactly 120 seconds after booking confirmation
- **Comprehensive Monitoring**: Health checks, commands, and HTTP endpoints
- **Production-Ready Error Handling**: Graceful degradation and detailed logging

---

## 🛠️ Pre-Deployment Checklist

### 1. Database Setup
```bash
# Run migrations to add required fields
php artisan migrate

# Verify the new columns exist
php artisan tinker
>>> Schema::hasColumn('reservation_hotels', 'booking_reference_id')
>>> Schema::hasColumn('reservation_hotels', 'booking_details_fetched_at')
```

### 2. Queue Configuration
Ensure your `.env` file has:
```env
QUEUE_CONNECTION=database
```

### 3. Verify Code Changes
```bash
# Check that ReservationService generates booking reference IDs
grep -n "generateBookingReferenceId" app/Services/ReservationService.php

# Check that FetchBookingDetailsJob exists
ls -la app/Jobs/FetchBookingDetailsJob.php

# Verify TBO API service is updated
grep -n "bookingDetails.*bookingReferenceId" app/Services/TBOHotelBookingService.php
```

---

## 🚦 Production Deployment Steps

### Step 1: Deploy Code Changes
```bash
# Pull latest code
git pull origin main

# Install/update dependencies
composer install --no-dev --optimize-autoloader

# Run migrations
php artisan migrate --force

# Clear caches
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Step 2: Start Queue Workers

#### Option A: Using Supervisor (Recommended)
Create `/etc/supervisor/conf.d/booking-queue-worker.conf`:
```ini
[program:booking-queue-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /path/to/your/project/artisan queue:work --queue=default --sleep=3 --tries=3 --timeout=90
autostart=true
autorestart=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/path/to/your/project/storage/logs/worker.log
stopwaitsecs=3600
```

```bash
# Reload supervisor
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start booking-queue-worker:*
```

#### Option B: Using systemd
Create `/etc/systemd/system/booking-queue-worker.service`:
```ini
[Unit]
Description=Booking Queue Worker
After=network.target

[Service]
Type=simple
User=www-data
WorkingDirectory=/path/to/your/project
ExecStart=/usr/bin/php artisan queue:work --queue=default --sleep=3 --tries=3 --timeout=90
Restart=always
RestartSec=3

[Install]
WantedBy=multi-user.target
```

```bash
# Enable and start the service
sudo systemctl daemon-reload
sudo systemctl enable booking-queue-worker.service
sudo systemctl start booking-queue-worker.service
```

#### Option C: Simple Background Process (Development Only)
```bash
# NOT recommended for production, but useful for testing
nohup php artisan queue:work --daemon > storage/logs/queue.log 2>&1 &
```

### Step 3: Set Up Cron Jobs
Add to your server's crontab (`crontab -e`):
```bash
# Laravel scheduler (if not already present)
* * * * * cd /path/to/your/project && php artisan schedule:run >> /dev/null 2>&1

# Optional: Health check monitoring (every 5 minutes)
*/5 * * * * curl -f http://your-domain.com/health/status > /dev/null 2>&1 || echo "Booking system down" | mail admin@yourdomain.com
```

---

## 🔍 Production Testing & Verification

### 1. Health Check Endpoints
```bash
# Basic system status (no database required)
curl https://your-domain.com/health/status

# Comprehensive system health check
curl https://your-domain.com/health/booking-system

# Check recent bookings status
curl https://your-domain.com/health/recent-bookings
```

### 2. Command-Line Monitoring Tools
```bash
# Check current booking system status
php artisan booking:check-status

# Monitor system health
php artisan booking:monitor-system

# Test booking details fetching
php artisan booking:test-details
```

### 3. Queue Health Verification
```bash
# Check queue worker status
php artisan queue:work --once

# Monitor queue in real-time
php artisan queue:monitor

# Check failed jobs
php artisan queue:failed
```

### 4. Test Complete Booking Flow
```bash
# Interactive testing command
php artisan booking:test-details --interactive

# Test with specific booking ID
php artisan booking:test-details --booking-id=123

# Background job testing
php artisan booking:test-details --mode=background
```

---

## 📊 Production Monitoring

### 1. Key Metrics to Monitor

#### System Health Indicators
- **Booking Reference ID Generation**: Should be 100% for new bookings
- **120-Second Compliance**: Details should be fetched within 2-5 minutes of booking
- **Queue Processing**: Jobs should not accumulate or get stuck
- **Failed Job Rate**: Should be < 5% under normal conditions

#### Monitoring Commands
```bash
# Get system overview
php artisan booking:monitor-system --summary

# Check for overdue bookings
php artisan booking:check-status --overdue-only

# Get detailed statistics
php artisan booking:check-status --stats
```

### 2. Log Monitoring
Watch these log files:
```bash
# Application logs
tail -f storage/logs/laravel.log

# Queue worker logs (if using supervisor)
tail -f storage/logs/worker.log

# Job processing logs
grep "FetchBookingDetailsJob" storage/logs/laravel.log
```

### 3. Database Monitoring Queries
```sql
-- Check recent booking reference ID generation
SELECT 
    COUNT(*) as total_bookings,
    COUNT(booking_reference_id) as with_reference_id,
    (COUNT(booking_reference_id) * 100.0 / COUNT(*)) as success_rate
FROM reservation_hotels 
WHERE created_at >= NOW() - INTERVAL 24 HOUR;

-- Check booking details fetching compliance
SELECT 
    COUNT(*) as bookings_with_confirmation,
    COUNT(booking_details_fetched_at) as details_fetched,
    (COUNT(booking_details_fetched_at) * 100.0 / COUNT(*)) as fetch_rate
FROM reservation_hotels 
WHERE confirmation_number IS NOT NULL 
AND created_at >= NOW() - INTERVAL 24 HOUR;

-- Find overdue bookings
SELECT id, booking_reference_id, confirmation_number, created_at
FROM reservation_hotels 
WHERE confirmation_number IS NOT NULL 
AND booking_details_fetched_at IS NULL 
AND created_at < NOW() - INTERVAL 10 MINUTE;
```

---

## 🚨 Troubleshooting Guide

### Common Issues and Solutions

#### 1. Booking Reference IDs Not Generated
**Symptoms**: New bookings don't have `booking_reference_id`
```bash
# Check if method exists in ReservationService
grep -n "generateBookingReferenceId" app/Services/ReservationService.php

# Test the generation
php artisan tinker
>>> $service = new App\Services\ReservationService();
>>> $id = $service->generateBookingReferenceId();
>>> echo $id;
```

#### 2. Jobs Not Processing
**Symptoms**: `booking_details_fetched_at` remains null
```bash
# Check queue worker status
ps aux | grep "queue:work"

# Check for failed jobs
php artisan queue:failed

# Restart queue workers
sudo supervisorctl restart booking-queue-worker:*
# OR
sudo systemctl restart booking-queue-worker.service
```

#### 3. Database Connection Issues
**Symptoms**: Health check returns database errors
```bash
# Test database connection
php artisan tinker
>>> DB::connection()->getPdo();

# Check database configuration
php artisan config:show database
```

#### 4. TBO API Issues
**Symptoms**: Jobs complete but no booking details
```bash
# Check TBO API credentials
grep TBO_ .env

# Test API connection manually
php artisan booking:test-details --booking-id=EXISTING_ID --immediate
```

---

## 📈 Performance Optimization

### 1. Queue Performance
```bash
# Run multiple workers for better throughput
# Adjust numprocs in supervisor config or run multiple systemd services

# Monitor queue performance
php artisan horizon:status  # If using Horizon
```

### 2. Database Optimization
```sql
-- Add indexes for better performance
CREATE INDEX idx_booking_reference_id ON reservation_hotels(booking_reference_id);
CREATE INDEX idx_booking_details_fetched_at ON reservation_hotels(booking_details_fetched_at);
CREATE INDEX idx_confirmation_created ON reservation_hotels(confirmation_number, created_at);
```

### 3. Monitoring Optimization
- Set up automated alerts for failed jobs
- Monitor disk space for log files
- Set up log rotation for queue worker logs

---

## 🔒 Security Considerations

### 1. API Rate Limiting
Ensure TBO API calls respect rate limits to avoid blocking.

### 2. Log Security
- Ensure booking details logs don't contain sensitive customer data
- Set appropriate file permissions on log files
- Consider log encryption for highly sensitive environments

### 3. Health Check Security
- Consider adding authentication to health check endpoints in production
- Monitor health check access logs for unusual activity

---

## 📞 Support & Maintenance

### Daily Checks
- [ ] Verify queue workers are running
- [ ] Check health endpoints
- [ ] Review error logs
- [ ] Monitor booking reference ID generation rate

### Weekly Checks
- [ ] Review failed job statistics
- [ ] Check database performance
- [ ] Verify 120-second compliance rate
- [ ] Clean up old log files

### Monthly Checks
- [ ] Review TBO API usage patterns
- [ ] Optimize database indexes
- [ ] Update monitoring thresholds
- [ ] Review and update documentation

---

## 🏁 Quick Start Commands

```bash
# Complete deployment verification
php artisan booking:monitor-system
curl https://your-domain.com/health/booking-system
php artisan queue:work --once

# If everything looks good, start monitoring
sudo supervisorctl start booking-queue-worker:*
```

---

## 📋 Deployment Checklist

- [ ] Database migrations completed
- [ ] Queue workers started and monitored
- [ ] Health check endpoints responding
- [ ] Cron jobs configured
- [ ] Log monitoring set up
- [ ] Test booking flow completed
- [ ] Performance metrics baseline established
- [ ] Alert systems configured
- [ ] Documentation updated
- [ ] Team trained on monitoring tools

**🎉 Your booking details monitoring system is now ready for production!**
