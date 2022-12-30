ARG TAG

FROM php:${TAG}

ENV DEBIAN_FRONTEND noninteractive
ENV TERM xterm-color

RUN \
    set -ex && \
    curl -sfL https://getcomposer.org/installer | php -- --install-dir=/usr/bin --filename=composer && \
    chmod +x /usr/bin/composer                                                                     && \
    apt-get update              && \
    apt-get install -y             \
        openssl \
        libssl-dev  \
        libcurl4-openssl-dev       \
        libpq-dev                  \
        libssl-dev                 \
        supervisor                 \
        unzip                      \
        zlib1g-dev                 \
        vim \
        procps \
        htop \
        wget \
        git \
        --no-install-recommends

RUN \
    docker-php-ext-install pdo_mysql sockets && \
    pecl channel-update pecl.php.net && \
    pecl install --configureoptions 'enable-redis-igbinary="no" enable-redis-lzf="no" enable-redis-zstd="no"' redis-5.3.7 && \
    docker-php-ext-enable redis

RUN cd /tmp && git clone https://github.com/swoole/swoole-src.git && \
    cd swoole-src && \
    git checkout v5.0.1 && \
    phpize  && \
    ./configure \
      --enable-mysqlnd \
      --enable-swoole-pgsql \
      --enable-openssl \
      --enable-hook-curl \
      --enable-http2 \
      --enable-sockets \
      --enable-swoole-curl && \
    make && make install && \
    docker-php-ext-enable swoole
