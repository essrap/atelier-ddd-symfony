# Development image: PHP CLI with the extensions the project and its quality tools need.
FROM php:8.5.11-cli-alpine

COPY --from=mlocati/php-extension-installer:2.12.0 /usr/bin/install-php-extensions /usr/local/bin/
RUN install-php-extensions intl opcache pdo_pgsql pcov zip

COPY --from=composer:2.10.3 /usr/bin/composer /usr/local/bin/composer

COPY docker/php/php.ini /usr/local/etc/php/conf.d/zz-app.ini

WORKDIR /app/projet
