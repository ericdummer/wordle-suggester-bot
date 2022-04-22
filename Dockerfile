FROM php:7.3-apache

LABEL MAINTAINER="Eric Dummer"

ENV DEBIAN_FRONTEND noninteractive

# Packages and core utils
RUN apt-get update && apt-get install -y \
    software-properties-common \
    bash-completion \
    apt-utils \
    dnsutils \
    libnss-myhostname \
    libssl-dev \
    git \
    curl \
    unzip \
    vim

# Install PHP extensions
RUN apt-get update && apt-get install -y \
      libxml2-dev \
      libxslt-dev \
    && docker-php-ext-install calendar bcmath json soap xml xmlrpc xsl sockets

RUN apt-get install -y \
      libonig-dev \
    && docker-php-ext-install iconv mbstring

RUN apt-get install -y \
      libcurl4-openssl-dev \
    && docker-php-ext-install curl

RUN apt-get install -y \
      libzip-dev \
      zlib1g-dev \
    && docker-php-ext-install zip

RUN apt-get install -y \
      libgmp-dev \
    && docker-php-ext-install gmp

RUN pecl install xdebug pcov \
 && docker-php-ext-enable xdebug pcov


# Set the working directory
WORKDIR /var/www/html

# Install composer
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

RUN chown -R www-data:www-data /var/www \
 && chmod 755 /var/www

EXPOSE 80 443
