#!/bin/bash
set -e

cd /var/www/html

echo "📝 Creating .env file..."

cat > .env << EOF
APP_NAME="${APP_NAME}"
APP_ENV="${APP_ENV}"
APP_KEY="${APP_KEY}"
APP_DEBUG="${APP_DEBUG}"
APP_URL="${APP_URL}"
ASSET_URL="https://sim-coffee-shop-production.up.railway.app"

LOG_CHANNEL="${LOG_CHANNEL}"
LOG_LEVEL="${LOG_LEVEL}"

DB_CONNECTION="${DB_CONNECTION}"
DB_HOST="${DB_HOST}"
DB_PORT="${DB_PORT}"
DB_DATABASE="${DB_DATABASE}"
DB_USERNAME="${DB_USERNAME}"
DB_PASSWORD="${DB_PASSWORD}"

SESSION_DRIVER="${SESSION_DRIVER}"
CACHE_STORE="${CACHE_STORE}"
FILESYSTEM_DISK="${FILESYSTEM_DISK}"
EOF

echo "✅ .env created"

echo "🔧 Clearing cache..."
php artisan config:clear 2>/dev/null || true

echo "⚡ Caching..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "🗄️ Running migrations..."
php artisan migrate --force

echo "🌱 Seeding..."
php artisan db:seed --force 2>/dev/null || echo "⚠️ Seeding skipped or failed"

echo "🚀 Starting server on port ${PORT}..."
exec php artisan serve --host=0.0.0.0 --port=${PORT}