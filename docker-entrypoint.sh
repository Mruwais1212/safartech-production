#!/bin/sh
set -e

# Wait for database to be ready
if [ -n "$DB_HOST" ]; then
    echo "Waiting for database at $DB_HOST:${DB_PORT:-3306}..."
    while ! nc -z "$DB_HOST" "${DB_PORT:-3306}"; do
        sleep 0.5
    done
    echo "Database is ready."
fi

# Bootstrap .env from example if missing (first-run convenience only)
if [ ! -f "/var/www/html/.env" ]; then
    echo "No .env found — copying .env.example as .env"
    cp /var/www/html/.env.example /var/www/html/.env
    php artisan key:generate --force
fi

# Run database migrations
echo "Running database migrations..."
php artisan migrate --force

# Cache Laravel configuration, routes, and views for production performance
echo "Caching application..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "Starting supervisord..."
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
