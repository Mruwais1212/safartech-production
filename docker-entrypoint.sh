# #!/bin/bash

# # Set error handling
# set -e

# # Change to application directory
# cd /var/www/html

# # Install/update composer dependencies if needed
# echo "Installing composer dependencies..."
# composer install --no-dev --optimize-autoloader --no-interaction

# # Wait for database to be ready
# echo "Waiting for database..."
# while ! php artisan migrate:status > /dev/null 2>&1; do
#     echo "Database not ready, waiting..."
#     sleep 2
# done

# # Run migrations
# echo "Running database migrations..."
# php artisan migrate --force

# # Run any other setup commands
# echo "Optimizing application..."
# php artisan config:cache
# php artisan route:cache
# php artisan view:cache

# # Start php-fpm
# echo "Starting PHP-FPM..."
# exec php-fpm

#!/bin/sh

# Wait for database to be ready (if using MySQL)
if [ -n "$DB_HOST" ]; then
  echo "Waiting for database..."
  while ! nc -z $DB_HOST $DB_PORT; do
    sleep 0.5
  done
  echo "Database ready!"
fi

# Generate Laravel key if not set
if [ ! -f ".env" ]; then
  cp .env.example .env
  php artisan key:generate
fi

# Run database migrations
php artisan migrate --force

# Start PHP-FPM in background
php-fpm -D

# Start Nginx in foreground
nginx -g "daemon off;"