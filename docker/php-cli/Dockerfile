FROM php:8.2-cli-alpine


RUN set -ex \
  && apk --no-cache add \
    postgresql-dev


COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/

RUN install-php-extensions pdo pdo_pgsql sockets pcntl pgsql

COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer
ENTRYPOINT composer install && php ./vendor/bin/phoenix migrate