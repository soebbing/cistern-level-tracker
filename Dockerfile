FROM alpine:3.11

ENV PROJECT_ROOT=/app
ENV COMPOSER_HOME=/var/cache/composer
ENV ARTIFACTS_DIR=/artifacts
ENV LD_PRELOAD=/usr/lib/preloadable_libiconv.so

# Adding a new repository for the more recent PHP 7.4 versions
ADD https://dl.bintray.com/php-alpine/key/php-alpine.rsa.pub /etc/apk/keys/php-alpine.rsa.pub

RUN apk --update add ca-certificates \
    && echo "https://dl.bintray.com/php-alpine/v3.11/php-7.4" >> /etc/apk/repositories \
    && apk --no-cache add \
        nginx supervisor curl zip git rsync bash openssh-client \
        php7 php7-fpm php7-soap php7-redis \
        php7-ctype php7-curl php7-dom php7-gd \
        php7-iconv php7-intl php7-json php7-mbstring \
        php7-mysqli php7-openssl php7-pdo_mysql php7-pdo_sqlite \
        php7-session php7-xml php7-xmlreader \
        php7-zip php7-zlib php7-phar php7-xdebug \
        gnu-libiconv \
        composer \
    && ln -sf /usr/bin/php7 /usr/bin/php \
    && mkdir -p $PROJECT_ROOT/var/cache $PROJECT_ROOT/var/log /var/cache/composer \
    && rm -f .env* /etc/nginx/conf.d/default.conf \
    && adduser -u 1000 -D -h $PROJECT_ROOT app app

# Copy system configs
COPY config/etc /etc

# Make sure files/folders needed by the processes are accessable when they run under the app
RUN chown -R app.app /run \
    && chown -R app.app /var/lib/nginx \
    && chown -R app.app /var/log/nginx \
    && chown -R app.app /var/cache/composer

WORKDIR $PROJECT_ROOT

ADD --chown=app . $PROJECT_ROOT

RUN rm -rf $PROJECT_ROOT/var/cache \
        $PROJECT_ROOT/var/log \
        $PROJECT_ROOT/var/vendor  \
    && chown -R app:app $PROJECT_ROOT/var

USER app

RUN composer install --optimize-autoloader --no-scripts --no-interaction \
    && bin/console doctrine:migration:migrate --allow-no-migration --no-interaction

# Expose the port nginx is reachable on
EXPOSE 8000

ENTRYPOINT ["./bin/entrypoint.sh"]

# Configure a healthcheck to validate that everything is up&running
HEALTHCHECK --timeout=10s CMD curl --silent --fail http://127.0.0.1:8000/fpm-ping