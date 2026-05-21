FROM php:8.4-fpm

RUN apt-get update && apt-get install -y \
    libsqlite3-dev libpq-dev libzip-dev zip unzip nginx supervisor gettext \
    && docker-php-ext-install pdo pdo_sqlite pdo_pgsql pgsql zip opcache \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN curl -fsSL https://deb.nodesource.com/setup_22.x | bash - \
    && apt-get install -y nodejs && rm -rf /var/lib/apt/lists/*

WORKDIR /var/www/html

COPY . .

ENV COMPOSER_ALLOW_SUPERUSER=1
RUN composer install --no-dev --optimize-autoloader --prefer-dist \
    && npm ci && npm run build \
    && php artisan storage:link \
    && mkdir -p storage/framework/{sessions,cache,views} storage/logs storage/app \
    && chown -R www-data:www-data /var/www/html

COPY docker/nginx.conf /etc/nginx/sites-enabled/default
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY docker/start.sh /start.sh

RUN chmod +x /start.sh

EXPOSE 8080

CMD ["/start.sh"]
