#!/bin/bash

# Start cron in background
cron &

# Start nginx in background
#nginx -g "daemon off;" &

# Start PHP-FPM in foreground
exec php-fpm
