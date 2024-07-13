#!/bin/bash

# Start PHP-FPM
php-fpm &

# Start Laravel development server
php artisan serve --host=0.0.0.0 --port=8000 &

# Wait for all background processes
wait
