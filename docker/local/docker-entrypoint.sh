#!/bin/bash

# Start cron in foreground
cron -f &

# Start PHP-FPM
php-fpm
