FROM php:8.4-fpm-alpine

WORKDIR /var/www/html

RUN apk add --no-cache \
        bash \
        curl \
        git \
        icu-dev \
        libpq-dev \
        libzip-dev \
        nodejs \
        npm \
        oniguruma-dev \
        postgresql-client \
        unzip \
        zip \
    && docker-php-ext-install \
        intl \
        mbstring \
        pdo \
        pdo_pgsql \
        pgsql \
        zip \
    && apk del oniguruma-dev

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

COPY . /var/www/html

RUN if [ -f composer.json ]; then composer install --no-interaction; fi \
    && if [ -f package.json ]; then npm install && npm run build; fi \
    && chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

CMD ["php-fpm"]
