FROM php:7.2-alpine

RUN apk --update add wget curl curl-dev supervisor

RUN docker-php-ext-install curl mysqli mbstring pdo pdo_mysql tokenizer

COPY supervisord.conf /etc/supervisord.conf

ENTRYPOINT ["/usr/bin/supervisord", "-n", "-c", "/etc/supervisord.conf"]

RUN rm /var/cache/apk/* \
    && mkdir -p /var/www/html

WORKDIR /etc/supervisor/conf.d/