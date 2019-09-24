FROM php:7.3-cli-alpine AS php
COPY . /var/www
WORKDIR /var/www

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN apk add --no-cache \
        mariadb-client \
        mysql-dev \
	;

RUN docker-php-ext-install -j$(nproc) \
            pdo_mysql \
           ;


CMD [ "php", "-a" ]