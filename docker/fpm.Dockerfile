# Base PHP Image with PHP 8.3
FROM php:8.3-fpm-alpine AS base
ENV EXT_APCU_VERSION=master

# Install dependencies and PHP extensions in a single RUN to reduce layers
RUN apk add --no-cache zlib-dev libpng-dev libzip-dev git icu-dev $PHPIZE_DEPS \
    && docker-php-ext-install exif gd zip pdo_mysql intl \
    && git clone --branch $EXT_APCU_VERSION --depth 1 https://github.com/krakjoe/apcu.git /usr/src/php/ext/apcu \
    && docker-php-ext-install apcu \
    && docker-php-ext-enable apcu \
    && docker-php-source delete

# Development Stage
FROM base AS dev
WORKDIR /var/www/html
COPY . .  # Copy entire project (avoids multiple COPY statements)

# Build Stage
FROM base AS build-fpm
WORKDIR /var/www/html

# Copy Composer first to leverage caching
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
COPY composer.json composer.lock . 

# Install production dependencies
RUN composer install --prefer-dist --no-ansi --no-dev --no-autoloader --no-interaction

# Copy the application code after dependencies are installed
COPY . .

# Optimise autoload
RUN composer dump-autoload -o

# Production FPM Stage
FROM build-fpm AS fpm
WORKDIR /var/www/html
COPY --from=build-fpm /var/www/html /var/www/html

# Install SQLite
RUN apk add --no-cache sqlite sqlite-dev $PHPIZE_DEPS \
    && docker-php-ext-install pdo_sqlite

# Create SQLite database directory
RUN mkdir -p /var/www/html/database \
    && touch /var/www/html/database/database.sqlite \
    && chown -R www-data:www-data /var/www/html/database \
    && chmod -R 755 /var/www/html/database \
    && chown -R www-data:www-data /var/www/html/storage \
    && chmod -R 755 /var/www/html/storage