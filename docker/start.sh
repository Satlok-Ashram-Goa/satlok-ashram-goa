#!/bin/bash
set -e

cd /var/www/html

# Ensure storage directories exist and have correct permissions
mkdir -p storage/framework/{cache,sessions,views,testing} storage/logs bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache

# Generate app key if not set
if [ -z "$APP_KEY" ]; then
    php artisan key:generate --force
fi

# Clear any stale cache
php artisan optimize:clear

# Run migrations
php artisan migrate --force

# Cache config and routes
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Optimize Filament
php artisan filament:optimize

# Start supervisor (manages nginx + php-fpm)
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
