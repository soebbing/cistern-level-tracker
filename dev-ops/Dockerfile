FROM alpine:3.19.4 AS base

ENV PROJECT_ROOT=/app
ENV COMPOSER_HOME=$PROJECT_ROOT/.composer
ENV ARTIFACTS_DIR=/artifacts
ENV LD_PRELOAD=/usr/lib/preloadable_libiconv.so

RUN apk --update add ca-certificates \
    && apk --no-cache add \
        nginx supervisor curl zip git bash openssh-client gnu-libiconv \
        php83 php83-fpm php83-ctype php83-curl php83-dom php83-fileinfo php83-gd \
        php83-iconv php83-intl php83-mbstring php83-openssl php83-pdo_mysql \
        php83-pdo_sqlite php83-session php83-tokenizer php83-xml php83-simplexml \
        php83-xmlwriter php83-zip php83-phar php83-opcache \
        \
    && ln -sf /usr/bin/php83 /usr/bin/php \
    && mkdir -p $PROJECT_ROOT/var/cache $PROJECT_ROOT/var/log $COMPOSER_HOME \
    && rm -f /etc/nginx/conf.d/default.conf \
        /var/cache/apk/* \
    && mkdir -p $COMPOSER_HOME \
    && adduser -u 1000 -D -h $PROJECT_ROOT app app

# Copy system configs
COPY config/etc /etc

FROM base AS build-fpm-composer

WORKDIR $PROJECT_ROOT

COPY --from=composer:2.2 /usr/bin/composer /usr/bin/composer

COPY ./composer.json $PROJECT_ROOT/composer.json
COPY ./composer.lock $PROJECT_ROOT/composer.lock

RUN composer --optimize-autoloader --no-scripts --no-interaction --no-dev --prefer-dist install

FROM base AS build-fpm

WORKDIR $PROJECT_ROOT

COPY --from=build-fpm-composer $PROJECT_ROOT $PROJECT_ROOT

FROM build-fpm-composer AS test

RUN apk --no-cache --repository=http://dl-cdn.alpinelinux.org/alpine/edge/testing add php8-pecl-pcov

RUN composer --optimize-autoloader --no-scripts --no-interaction --no-dev --prefer-dist install
RUN composer test-coverage

ADD --chown=app . $PROJECT_ROOT

RUN rm -rf $PROJECT_ROOT/var/cache \
        $PROJECT_ROOT/var/log \
        $PROJECT_ROOT/vendor \
        $PROJECT_ROOT/tests \
        $PROJECT_ROOT/composer \
    && chown -R app:app $PROJECT_ROOT/var

USER app

RUN composer --optimize-autoloader --no-scripts --no-interaction --no-dev --prefer-dist install

FROM build-fpm-composer AS final

COPY ./bin $PROJECT_ROOT/bin
COPY ./config $PROJECT_ROOT/config
COPY ./src $PROJECT_ROOT/src
COPY ./migrations $PROJECT_ROOT/migrations
COPY ./templates $PROJECT_ROOT/templates
COPY ./translations $PROJECT_ROOT/translations
COPY ./public $PROJECT_ROOT/public

# Make sure files/folders needed by the processes are accessible when they run under the app
RUN chown -R app.app /run \
    && chown -R app.app /var/lib/nginx \
    && chown -R app.app /var/log/nginx \
    && mkdir -p $PROJECT_ROOT/var

ADD --chown=app . $PROJECT_ROOT

USER app

# Expose the port nginx is reachable on
EXPOSE 8000

ENTRYPOINT ["/app/bin/entrypoint.sh"]

# Configure a healthcheck to validate that everything is up&running
HEALTHCHECK --timeout=10s CMD curl --silent --fail http://127.0.0.1:8000/fpm-ping