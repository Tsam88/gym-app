#!/bin/sh
set -e

cd /var/www/html

if [ ! -f .env ]; then
  cp .env.example .env
fi

# Normalize line endings to avoid false non-empty values on Windows mounts
if [ -f .env ]; then
  tr -d '\r' < .env > .env.tmp && mv .env.tmp .env
fi
APP_KEY_VALUE=$(grep '^APP_KEY=' .env | cut -d '=' -f2- | tr -d '\r')
if [ -z "$APP_KEY_VALUE" ]; then
  php artisan key:generate --force
fi

if [ ! -f vendor/autoload.php ]; then
  composer install --no-interaction --prefer-dist --optimize-autoloader
fi

if [ ! -d node_modules ] || [ -z "$(ls -A node_modules 2>/dev/null)" ]; then
  npm install
fi

DB_MIGRATION_STATUS=0
for attempt in 1 2 3 4 5; do
  if php artisan migrate --force >/tmp/migrate.log 2>&1; then
    DB_MIGRATION_STATUS=1
    break
  fi
  echo "Waiting for database to be ready (attempt $attempt/5)..."
  sleep 2
done

if [ "$DB_MIGRATION_STATUS" -ne 1 ]; then
  cat /tmp/migrate.log
  exit 1
fi

if [ ! -f public/js/app.js ] && [ -f package.json ]; then
  npm run production || npm run development || true
fi

chown -R www-data:www-data storage bootstrap/cache || true

exec "$@"
