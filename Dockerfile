FROM php:8.4-fpm-alpine

WORKDIR /app

RUN apk --update upgrade \
    && apk add --no-cache  \
    autoconf  \
    automake  \
    make  \
    gcc  \
    g++  \
    git  \
    bash  \
    icu-dev  \
    libzip-dev \
    linux-headers

RUN pecl install apcu-5.1.24 \
    && pecl install xdebug-3.4.1

RUN docker-php-ext-install -j$(nproc) \
        bcmath \
        opcache \
        intl \
        zip \
        pdo_mysql

RUN docker-php-ext-enable apcu opcache

RUN apk add --no-cache libpq-dev \
    && docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql \
    && docker-php-ext-install pdo_pgsql pgsql

RUN curl -sS https://get.symfony.com/cli/installer | bash -s - --install-dir /usr/local/bin

COPY etc/infrastructure/php/ /usr/local/etc/php/

# allow non-root users have home
RUN mkdir -p /opt/home
RUN chmod 777 /opt/home
ENV HOME=/opt/home
