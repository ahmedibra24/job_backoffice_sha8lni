#!/bin/sh
set -e

echo "Caching config..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "Waiting for MySQL..."

until php -r "
try {
    new PDO('mysql:host=' . getenv('DB_HOST') . ';port=' . getenv('DB_PORT') . ';dbname=' . getenv('DB_DATABASE'), getenv('DB_USERNAME'), getenv('DB_PASSWORD'));
} catch (Exception \$e) {
    exit(1);
}
"; do
  echo "Database not ready... sleeping"
  sleep 3
done

echo "Database is ready ✔"

echo "Running migrations..."
php artisan migrate --force
php artisan db:seed --force


echo "Starting Laravel server..."
exec php -S 0.0.0.0:80 -t public