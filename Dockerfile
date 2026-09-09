FROM composer:2 AS composer

FROM php:8.4-cli

RUN apt-get update \
    && apt-get install -y libpq-dev libzip-dev unzip \
    && docker-php-ext-install pdo_pgsql zip \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

COPY app/composer.json app/composer.lock ./

RUN composer install --no-interaction --prefer-dist --optimize-autoloader --no-scripts

COPY app /var/www

RUN php artisan package:discover

RUN echo "IMAGE VERSION 2" > /image-version.txt

CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]