#!/bin/bash

# Start PHP-FPM
php-fpm &

# Start Laravel development server
php artisan serve --host=0.0.0.0 --port=8000 &

# Start Reverb server
php artisan reverb:start &

# Start Laravel queue worker
php artisan queue:work &

# Wait for all background processes
wait
