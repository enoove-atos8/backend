#!/bin/bash

# Corrige permissões da pasta storage após o volume ser montado
echo "🔧 Ajustando permissões da pasta storage..."
chown -R www-data:www-data /var/www/backend/html/storage
chmod -R 775 /var/www/backend/html/storage

# Garante que a pasta temp de relatórios exista
mkdir -p /var/www/backend/html/storage/tenants/iebrd/reports/temp
chown -R www-data:www-data /var/www/backend/html/storage/tenants
chmod -R 775 /var/www/backend/html/storage/tenants

# Start cron em background
cron &

# Start Queue Worker em background com auto-restart
while true; do
    php artisan queue:work database --tries=3 --timeout=120 --sleep=3 --max-jobs=1000 --max-time=3600
    echo "Queue worker stopped. Restarting in 5 seconds..." >&2
    sleep 5
done &

# Start PHP-FPM em foreground
exec php-fpm
