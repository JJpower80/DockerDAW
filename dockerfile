FROM php:8.2-fpm-alpine

WORKDIR /var/www/html

RUN apk add --no-cache \
    mysql-client \
    git \
    npm \
    nodejs \
    composer \
    && rm -rf /var/cache/apk/*

RUN docker-php-ext-install pdo_mysql opcache

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

EXPOSE 9000

CMD ["php-fpm"]