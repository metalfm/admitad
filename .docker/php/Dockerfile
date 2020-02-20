FROM php:7.4.0-fpm-alpine

EXPOSE 9000
WORKDIR /var/www/html

RUN mkdir -p /var/cache/apk &&\
    ln -s /var/cache/apk /etc/apk/cache &&\
    apk add --update shadow tzdata icu &&\
    apk add --update --virtual .build-deps freetype-dev libjpeg-turbo-dev libpng-dev icu-dev zlib-dev $PHPIZE_DEPS libmemcached-dev cyrus-sasl-dev imagemagick-dev libtool &&\
    docker-php-ext-install -j$(nproc) pdo_mysql opcache intl

RUN cp /usr/share/zoneinfo/Europe/Moscow /etc/localtime &&\
    echo "Europe/Moscow" > /etc/timezone

ARG APP_DEBUG=0
RUN if [ $APP_DEBUG = 1 ] ; then pecl install xdebug-2.8.1 && docker-php-ext-enable xdebug ; fi

# Cleanup
RUN apk del .build-deps &&\
    apk -v cache clean &&\
    rm -rf /var/cache/apk/* /tmp/* /usr/share/php7/*

RUN rm -rf /usr/local/etc/php-fpm.d/docker.conf &&\
    rm -rf /usr/local/etc/php-fpm.d/zz-docker.conf &&\
    rm -rf /usr/local/etc/php-fpm.d/www.conf &&\
    touch /usr/local/var/log/php-fpm.log && chown www-data:www-data /usr/local/var/log/php-fpm.log

COPY /.docker/php/php.ini /usr/local/etc/php

ARG APP_DEBUG_ADDRESS
RUN if [ $APP_DEBUG = 1 ] ; then echo -e "\n\
  xdebug.remote_enable = 1    \n\
  xdebug.remote_host   = ${APP_DEBUG_ADDRESS} \n\
" | tee -a /usr/local/etc/php/php.ini ; fi
COPY /.docker/php/pool.conf /usr/local/etc/php-fpm.d/
COPY /.docker/php/wait-for.sh /var/run/wait-for.sh
RUN chmod +x /var/run/wait-for.sh

ENV COMPOSER_HOME      /composer/
ENV COMPOSER_CACHE_DIR /composer/.composer-cache/
ENV COMPOSER_HTACCESS_PROTECT 0

RUN mkdir -p /composer/.composer-cache/ &&\
    usermod -u 101 www-data

USER www-data
COPY --chown=www-data composer.phar /var/www/html/composer.phar

USER root
RUN chown -R www-data:www-data /var/www/html/ &&\
    chown -R www-data:www-data /composer/ &&\
    mkdir -p /opt/phpstorm-coverage &&\
    chown -R www-data:www-data /opt/phpstorm-coverage

USER www-data
RUN php composer.phar global require hirak/prestissimo pyrech/composer-changelogs --no-scripts --prefer-dist
COPY --chown=www-data composer.json /var/www/html/composer.json
COPY --chown=www-data composer.lock /var/www/html/composer.lock
COPY --chown=www-data symfony.lock  /var/www/html/symfony.lock

RUN php composer.phar install --no-scripts --no-autoloader

COPY --chown=www-data phpunit.xml.dist /var/www/html/phpunit.xml
COPY --chown=www-data bin/             /var/www/html/bin

RUN bin/phpunit install

COPY --chown=www-data web/             /var/www/html/web
COPY --chown=www-data config/          /var/www/html/config
COPY --chown=www-data tests/           /var/www/html/tests
COPY --chown=www-data templates/       /var/www/html/templates
COPY --chown=www-data translations/    /var/www/html/translations
COPY --chown=www-data .env.test        /var/www/html/.env.test
COPY --chown=www-data src/             /var/www/html/src

RUN php composer.phar dump-autoload --optimize
RUN mkdir -p var/cache/dev &&\
    mkdir -p var/cache/prod &&\
    mkdir -p var/cache/test &&\
    mkdir -p var/log

# Sets a default Doctrine URL.
#
# Doctrine needs a well-formed URL string with a database version even in the build hook.
# It doesn't use it, but it fails if it's not there.  This default meets the minimum
# requirements of the format without actually allowing a connection.
ARG APP_ENV
COPY --chown=www-data .env.dist /var/www/html/.env
RUN DATABASE_URL="mysql://:@localhost:3306/?charset=utf8mb4&serverVersion=mariadb-10.2.12" &&\
    APP_ENV=$APP_ENV &&\
    APP_DEBUG=$APP_DEBUG &&\
    bin/console cache:clear &&\
    bin/console cache:clear --env=test --no-debug &&\
    > .env

USER 101
CMD php-fpm
