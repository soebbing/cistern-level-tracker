FROM alpine:3.15

ENV PROJECT_ROOT=/app
ENV COMPOSER_HOME=/var/cache/composer
ENV ARTIFACTS_DIR=/artifacts
ENV LD_PRELOAD=/usr/lib/preloadable_libiconv.so

# Adding a new repository for the more recent PHP 8.0 versions
ADD https://dl.bintray.com/php-alpine/key/php-alpine.rsa.pub /etc/apk/keys/php-alpine.rsa.pub

RUN apk --update add ca-certificates \
    && echo "https://dl.bintray.com/php-alpine/v3.15/php-8.0" >> /etc/apk/repositories \
    && apk --no-cache add \
        nginx supervisor curl zip git rsync bash openssh-client \
        php8 php8-fpm php8-soap php8-redis \
        php8-ctype php8-curl php8-dom php8-gd \
        php8-iconv php8-intl php8-mbstring \
        php8-mysqli php8-openssl php8-pdo_mysql \
        php8-session php8-xml php8-xmlreader \
        php8-zip php8-zlib php8-phar php8-opcache php8-sqlite3 \
        gnu-libiconv nodejs npm yarn \
    && ln -sf /usr/bin/php8 /usr/bin/php \
    && mkdir -p $PROJECT_ROOT/var/cache $PROJECT_ROOT/var/log $COMPOSER_HOME \
    && rm /etc/nginx/conf.d/default.conf \
    && adduser -u 1000 -D -h $PROJECT_ROOT app app

COPY --from=composer:2.2 /usr/bin/composer /usr/bin/composer

# Copy system configs
COPY config/etc /etc

# Make sure files/folders needed by the processes are accessible when they run under the app
RUN chown -R app.app /run \
    && chown -R app.app /var/lib/nginx \
    && chown -R app.app /var/log/nginx \
    && chown -R app.app /var/cache/composer

WORKDIR $PROJECT_ROOT

ADD --chown=app . $PROJECT_ROOT

RUN rm -rf $PROJECT_ROOT/var/cache \
        $PROJECT_ROOT/var/log \
        $PROJECT_ROOT/vendor \
        $PROJECT_ROOT/tests \
    && chown -R app:app $PROJECT_ROOT/var

USER app

RUN composer --optimize-autoloader --no-scripts --no-interaction --no-dev --prefer-dist install \
    && composer js-ci \
    && composer build-js-prod

# Expose the port nginx is reachable on
EXPOSE 8000

ENTRYPOINT ["/app/bin/entrypoint.sh"]

# Configure a healthcheck to validate that everything is up&running
HEALTHCHECK --timeout=10s CMD curl --silent --fail http://127.0.0.1:8000/fpm-ping