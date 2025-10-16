#!/bin/bash

# Start cron in background
cron &

# Start Queue Worker in background with auto-restart on failure
while true; do
    php artisan queue:work database --tries=3 --timeout=120 --sleep=3 --max-jobs=1000 --max-time=3600
    echo "Queue worker stopped. Restarting in 5 seconds..." >&2
    sleep 5
done &

# Start nginx in background
#nginx -g "daemon off;" &

# Start PHP-FPM in foreground
exec php-fpm
