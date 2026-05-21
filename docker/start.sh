#!/bin/bash

export PORT="${PORT:-8080}"

envsubst '${PORT}' < /etc/nginx/sites-enabled/default > /tmp/nginx.conf && mv /tmp/nginx.conf /etc/nginx/sites-enabled/default

php artisan migrate --force

exec /usr/bin/supervisord -n
