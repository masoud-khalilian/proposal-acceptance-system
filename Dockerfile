FROM php:8.3-fpm-alpine AS base

RUN apk add --no-cache postgresql-dev libzip-dev icu-dev \
    && docker-php-ext-install pdo pdo_pgsql intl opcache

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

COPY composer.json composer.lock* ./
RUN composer install --no-dev --no-scripts --no-interaction --no-progress --optimize-autoloader \
    && rm -rf /root/.composer/cache

COPY . .

RUN mkdir -p var/cache/twig && chown -R www-data:www-data /app/var

USER www-data

CMD ["php-fpm"]
