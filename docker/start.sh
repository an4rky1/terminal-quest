#!/bin/bash

export PORT="${PORT:-8080}"

envsubst '${PORT}' < /etc/nginx/sites-enabled/default > /tmp/nginx.conf && mv /tmp/nginx.conf /etc/nginx/sites-enabled/default

mkdir -p storage/framework/{sessions,cache,views} storage/logs storage/app
chown -R www-data:www-data storage bootstrap/cache

if [ -z "$APP_KEY" ] || [ "$APP_KEY" = "" ]; then
    export APP_KEY=$(php -r 'echo "base64:" . base64_encode(random_bytes(32));')
fi

php artisan migrate --force

exec /usr/bin/supervisord -n
