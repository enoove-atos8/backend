#!/bin/bash

# Start cron in foreground
cron -f &

# Start Octane server
echo "Starting Octane on port: 8000"
#php artisan octane:start --server=swoole --host=0.0.0.0 --port=8000
