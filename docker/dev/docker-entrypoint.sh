#!/bin/bash

# Start cron in foreground
cron -f &

# Start Octane server
php artisan octane:start --server=swoole --host=0.0.0.0 --port=8000
