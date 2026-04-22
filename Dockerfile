# Stage 1: Base image with PHP extensions
FROM php:8.3-fpm-alpine AS app_php_base

RUN apk add --no-cache \
    acl \
    fcgi \
    file \
    gettext \
    git \
    icu-data-full \
    icu-dev \
    libzip-dev \
    zlib-dev \
    ;

RUN docker-php-ext-install \
    intl \
    zip \
    opcache \
    ;

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Stage 2: Build dependencies (Production)
FROM app_php_base AS app_php_builder

ENV APP_ENV=prod

COPY composer.json composer.lock symfony.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist

COPY . .

RUN composer dump-autoload --optimize --no-dev --classmap-authoritative
# RUN composer run-script post-install-cmd

# Stage 3: Final Production Image
FROM app_php_base AS app_php_dev

ENV APP_ENV=dev
ENV APP_DEBUG=1

RUN mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"

RUN apk add --no-cache \
    linux-headers \
    $PHPIZE_DEPS \
    && pecl install xdebug \
    && docker-php-ext-enable xdebug

COPY etc/php/opcache.ini "$PHP_INI_DIR/conf.d/opcache.ini"

# USER www-data

EXPOSE 9000

CMD ["php-fpm"]

# Stage 4: Final Production Image
FROM app_php_base AS app_php_prod

ENV APP_ENV=prod
ENV APP_DEBUG=0

COPY --from=app_php_builder --chown=www-data:www-data /var/www/html /var/www/html

RUN chown -R www-data:www-data var public/bundles vendor

RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"
COPY etc/php/opcache.ini "$PHP_INI_DIR/conf.d/opcache.ini"

USER www-data

EXPOSE 9000

CMD ["php-fpm"]
