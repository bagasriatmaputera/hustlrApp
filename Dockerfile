# ---------- Stage 1: install vendor ----------
FROM composer:2 AS vendor
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist --ignore-platform-reqs
COPY . .
RUN composer dump-autoload --optimize --no-dev

# ---------- Stage 2: build aset frontend ----------
FROM node:20-alpine AS assets
WORKDIR /app
COPY package.json package-lock.json ./
RUN npm ci
COPY . .
COPY --from=vendor /app/vendor ./vendor
RUN npm run build

# ---------- Stage 3: runtime ----------
FROM php:8.4-fpm-alpine

RUN apk add --no-cache \
    bash curl unzip \
    libpng-dev libjpeg-turbo-dev freetype-dev \
    libzip-dev icu-dev oniguruma-dev

RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        pdo_mysql bcmath gd zip intl mbstring pcntl opcache exif

WORKDIR /var/www/html

COPY --from=vendor /app /var/www/html
COPY --from=assets /app/public/build ./public/build

ARG UID=1000
ARG GID=1000
RUN addgroup -g ${GID} laravel \
    && adduser -D -u ${UID} -G laravel laravel \
    && chown -R laravel:laravel storage bootstrap/cache

USER laravel
EXPOSE 9000

# ---------- Stage 4: nginx ----------
FROM nginx:alpine AS web
COPY docker/nginx/default.conf /etc/nginx/conf.d/default.conf
COPY --from=app /var/www/html/public /var/www/html/public