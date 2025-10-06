FROM php:8.1-fpm

COPY php.ini /usr/local/etc/php/

RUN apt-get update \
    && apt-get install -y --no-install-recommends \
    default-mysql-client \
    zlib1g-dev libzip-dev unzip \
    libpng-dev libjpeg62-turbo-dev \
    libicu-dev \
    && docker-php-ext-configure gd --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
    pdo_mysql zip gd intl bcmath opcache \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer

RUN usermod -u 1000 www-data && groupmod -g 1000 www-data
WORKDIR /var/www
