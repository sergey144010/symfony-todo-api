FROM php:8.2.1-fpm-buster

# Tools
RUN apt-get update && \
  apt-get install -y htop nano vim procps net-tools && \
  rm -rf /var/lib/apt/lists/*

# Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Compressors
RUN apt-get update && \
    apt-get install zip unzip && \
    rm -rf /var/lib/apt/lists/*

# Wget
RUN apt-get update && \
    apt-get install -y wget && \
    rm -rf /var/lib/apt/lists/*

# Lib
RUN apt-get update && \
    apt-get install -y libpq-dev libssl-dev && \
    rm -rf /var/lib/apt/lists/*

# For Memcache
RUN apt-get update && \
    apt-get install -y libz-dev libmemcached-dev && \
    rm -rf /var/lib/apt/lists/*

# Php extensions
RUN docker-php-ext-install \
    pdo \
    pdo_mysql \
    pdo_pgsql \
    sockets \
    bcmath \
    fileinfo \
    gettext \
    pcntl

# Git
RUN apt-get update && \
    apt-get install -y git && \
    rm -rf /var/lib/apt/lists/*

# Memcache
RUN pecl install memcached && \
    rm -rf /tmp/pear && \
    docker-php-ext-enable memcached

# Xdebug
RUN pecl install xdebug;

WORKDIR /var/www/project/current

CMD ([ "$PHP_EXTENSION_XDEBUG" = "true" ] && docker-php-ext-enable xdebug) ; \
    php-fpm
