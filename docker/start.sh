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

echo "✅ .env created successfully"
cat .env

echo "🔧 Clearing cache..."
php artisan config:clear
php artisan cache:clear

echo "⚡ Caching config..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "🗄️ Running migrations..."
php artisan migrate --force

echo "🌱 Checking if seeding needed..."
php artisan tinker --execute="
\$count = \App\Models\User::count();
echo \$count;
exit;
" > /tmp/usercount.txt 2>&1
USERCOUNT=$(cat /tmp/usercount.txt | grep -o '[0-9]*' | head -1)

if [ -z "$USERCOUNT" ] || [ "$USERCOUNT" = "0" ]; then
    echo "🌱 Seeding database..."
    php artisan db:seed --force
    echo "✅ Seeding done"
else
    echo "⏭️ Skip seeding - $USERCOUNT users already exist"
fi

echo "🚀 Starting Laravel on port ${PORT:-8000}..."
php artisan serve --host=0.0.0.0 --port=${PORT:-8000}