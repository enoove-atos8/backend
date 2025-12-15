#!/bin/bash

# Cache config após .env estar montado
php artisan config:cache

# Start cron in background
cron &

# Start Queue Worker in background with auto-restart on failure
while true; do
    php artisan queue:work database --tries=3 --timeout=120 --sleep=3 --max-jobs=1000 --max-time=3600
    echo "Queue worker stopped. Restarting in 5 seconds..." >&2
    sleep 5
done &

# Start Octane server in foreground
php artisan octane:start --server=swoole --host=0.0.0.0 --port=8000
