FROM php:8.4-fpm-alpine

# Dependensi sistem
RUN apk add --no-cache \
    bash git curl unzip \
    libpng-dev libjpeg-turbo-dev freetype-dev \
    libzip-dev icu-dev oniguruma-dev \
    nodejs npm

# Ekstensi PHP yang dibutuhkan Laravel + Livewire
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        pdo_mysql bcmath gd zip intl mbstring pcntl opcache exif

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Samakan UID/GID dengan user host supaya permission file aman
ARG UID=1000
ARG GID=1000
RUN addgroup -g ${GID} laravel \
    && adduser -D -u ${UID} -G laravel laravel \
    && chown -R laravel:laravel /var/www/html

USER laravel

EXPOSE 9000
CMD ["php-fpm"]