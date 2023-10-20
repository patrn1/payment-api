FROM php:8.1.21RC1-fpm-bookworm

ARG GROUP_ID

RUN groupadd -g ${GROUP_ID} www

RUN usermod -a -G www www-data

RUN apt-get update && apt-get install -y \
       lsb-release ca-certificates apt-transport-https software-properties-common gnupg2 wget libxml2-dev git zip unzip libzip-dev

RUN docker-php-ext-install mysqli pdo_mysql soap zip

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# COPY php.ini /usr/local/etc/php/php.ini

WORKDIR /var/www

ENV COMPOSER_ALLOW_SUPERUSER=1

# CMD chmod -R 777 /var/www

# CMD ["php-fpm"]
