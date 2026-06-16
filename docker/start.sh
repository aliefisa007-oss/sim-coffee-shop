#!/bin/bash
set -e

cd /var/www/html

# Generate key jika belum ada
php artisan key:generate --force

# Cache
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Migrate
php artisan migrate --force

# Seed
php artisan db:seed --force

# Jalankan server Laravel bukan Apache
php artisan serve --host=0.0.0.0 --port=8000