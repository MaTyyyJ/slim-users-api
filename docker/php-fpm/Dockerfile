FROM php:8.2-fpm-alpine
RUN apk --update --no-cache add build-base \
        autoconf \
        rabbitmq-c-dev

# Add AMQP
RUN pecl install amqp
RUN docker-php-ext-enable amqp

# Add PDO and PGSQL
RUN set -ex \
  && apk --no-cache add \
    postgresql-dev

RUN docker-php-ext-install pdo pdo_pgsql

# Add xdebug
RUN apk add --no-cache --virtual .build-deps $PHPIZE_DEPS
RUN apk add --update linux-headers
RUN pecl install xdebug
RUN docker-php-ext-enable xdebug
RUN apk del -f .build-deps

# Configure Xdebug
RUN echo "xdebug.start_with_request=yes" >> /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.mode=debug" >> /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.discover_client_host=1" >> /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.client_port=9000" >> /usr/local/etc/php/conf.d/xdebug.ini

RUN docker-php-ext-enable xdebug

#Add PCNTL
RUN docker-php-ext-configure pcntl
RUN docker-php-ext-install pcntl

#Add sockets
RUN docker-php-ext-install sockets