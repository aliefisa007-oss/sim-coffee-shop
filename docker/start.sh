#!/bin/bash

# Cache config
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Run migrations
php artisan migrate --force

# Seed database (hanya jika belum ada data)
php artisan db:seed --force

# Start Apache
apache2-foreground