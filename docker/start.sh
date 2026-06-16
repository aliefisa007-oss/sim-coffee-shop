#!/bin/bash
set -e

cd /var/www/html

# Buat .env dari environment variables
cat > .env << EOF
APP_NAME="${APP_NAME:-Contact Coffee}"
APP_ENV="${APP_ENV:-production}"
APP_KEY="${APP_KEY}"
APP_DEBUG="${APP_DEBUG:-false}"
APP_URL="${APP_URL:-http://localhost}"

LOG_CHANNEL="${LOG_CHANNEL:-stderr}"
LOG_LEVEL="${LOG_LEVEL:-error}"

DB_CONNECTION="${DB_CONNECTION:-mysql}"
DB_HOST="${DB_HOST}"
DB_PORT="${DB_PORT:-3306}"
DB_DATABASE="${DB_DATABASE}"
DB_USERNAME="${DB_USERNAME}"
DB_PASSWORD="${DB_PASSWORD}"

SESSION_DRIVER="${SESSION_DRIVER:-file}"
CACHE_STORE="${CACHE_STORE:-file}"
FILESYSTEM_DISK=local
EOF

echo "✅ .env created"

# Clear & cache config
php artisan config:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "✅ Config cached"

# Migrate
php artisan migrate --force
echo "✅ Migration done"

# Seed hanya jika users belum ada
USER_COUNT=$(php artisan tinker --execute="echo \App\Models\User::count();" 2>/dev/null | tail -1)
if [ "$USER_COUNT" = "0" ] || [ -z "$USER_COUNT" ]; then
    php artisan db:seed --force
    echo "✅ Seeding done"
else
    echo "⏭️ Skip seeding - data already exists"
fi

# Start Laravel
echo "🚀 Starting Laravel server..."
php artisan serve --host=0.0.0.0 --port=${PORT:-8000}