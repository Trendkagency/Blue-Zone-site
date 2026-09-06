#!/bin/sh
set -e

# Run standard Laravel optimizations
php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true

# Start Supervisor (starts Nginx and PHP-FPM)
exec /usr/bin/supervisord -c /etc/supervisord.conf
