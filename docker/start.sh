#!/bin/sh
set -e

# Ensure storage directories exist and have proper permissions
mkdir -p /app/storage/framework/cache/data \
         /app/storage/framework/sessions \
         /app/storage/framework/views \
         /app/storage/logs \
         /app/bootstrap/cache

chown -R www-data:www-data /app/storage /app/bootstrap/cache || true
chmod -R 775 /app/storage /app/bootstrap/cache || true

# Run standard Laravel optimizations
php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true

# Start Supervisor (starts Nginx and PHP-FPM)
exec /usr/bin/supervisord -c /etc/supervisord.conf
