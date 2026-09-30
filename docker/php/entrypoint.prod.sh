#!/bin/sh
set -eu

mkdir -p \
    /var/www/bootstrap/cache \
    /var/www/storage/app/public \
    /var/www/storage/framework/cache/data \
    /var/www/storage/framework/sessions \
    /var/www/storage/framework/testing \
    /var/www/storage/framework/views \
    /var/www/storage/logs

if [ ! -L /var/www/public/storage ]; then
    php artisan storage:link >/dev/null 2>&1 || true
fi

php artisan package:discover --ansi

if [ "${RUN_MIGRATIONS:-false}" = "true" ]; then
    php artisan migrate --force --ansi
fi

php artisan config:cache --ansi
php artisan route:cache --ansi
php artisan view:cache --ansi

exec "$@"
