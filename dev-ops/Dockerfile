FROM frolvlad/alpine-glibc:alpine-3.16 AS base

ENV PROJECT_ROOT=/app
ENV COMPOSER_HOME=/var/cache/composer
ENV ARTIFACTS_DIR=/artifacts
ENV LD_PRELOAD=/usr/lib/preloadable_libiconv.so

RUN apk --update add ca-certificates \
    && apk --no-cache add \
        nginx supervisor curl zip git rsync bash unzip wget openssh-client gnu-libiconv \
        php81 php81-fpm php81-soap php81-redis php81-tokenizer \
        php81-ctype php81-curl php81-dom php81-gd \
        php81-iconv php81-intl php81-json php81-mbstring \
        php81-mysqli php81-openssl php81-pdo_mysql php81-pdo_sqlite \
        php81-session php81-xml php81-xmlreader php81-xmlwriter php81-simplexml \
        php81-zip php81-zlib php81-phar php81-opcache \
        unzip wget \
    && ln -sf /usr/bin/php81 /usr/bin/php \
    \
    && mkdir -p $PROJECT_ROOT/var/cache $PROJECT_ROOT/var/log \
    && rm /etc/nginx/conf.d/default.conf \
    ; adduser -u 1000 -D -h $PROJECT_ROOT app app

COPY ./config/etc /etc

FROM base AS build-fpm-composer

WORKDIR $PROJECT_ROOT

COPY --from=composer:2.2 /usr/bin/composer /usr/bin/composer

COPY ./composer.json $PROJECT_ROOT/composer.json
COPY ./composer.lock $PROJECT_ROOT/composer.lock

RUN composer install --no-dev --no-scripts
RUN composer dump-autoload --optimize

FROM base AS build-fpm

WORKDIR $PROJECT_ROOT

COPY --from=build-fpm-composer $PROJECT_ROOT $PROJECT_ROOT

FROM build-fpm-composer AS test

RUN apk --no-cache --repository=http://dl-cdn.alpinelinux.org/alpine/edge/testing add php81-pecl-pcov

RUN composer install --dev
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
    && chown -R app.app /var/cache/composer \
    && mkdir -p $PROJECT_ROOT/var

ADD --chown=app . $PROJECT_ROOT

USER app

# Expose the port nginx is reachable on
EXPOSE 8000

ENTRYPOINT ["/app/bin/entrypoint.sh"]

# Configure a healthcheck to validate that everything is up&running
HEALTHCHECK --timeout=10s CMD curl --silent --fail http://127.0.0.1:8000/fpm-ping