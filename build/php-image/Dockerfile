#FROM docker.io/php:7.3-apache
FROM docker.io/php:7.3-apache-buster

LABEL maintainer="getlaminas.org" \
    org.label-schema.docker.dockerfile="/Dockerfile" \
    org.label-schema.name="Laminas MVC Skeleton" \
    org.label-schema.url="https://docs.getlaminas.org/mvc/" \
    org.label-schema.vcs-url="https://github.com/laminas/laminas-mvc-skeleton"

## Update package information
RUN apt-get update

## Configure Apache
RUN a2enmod rewrite \
    && sed -i 's!/var/www/html!/var/www/public!g' /etc/apache2/sites-available/000-default.conf \
    && mv /var/www/html /var/www/public

## Install Composer
RUN curl -sS https://getcomposer.org/installer \
  | php -- --install-dir=/usr/local/bin --filename=composer

## Install zip libraries and extension
RUN apt-get install --yes git zlib1g-dev libzip-dev libapache2-mod-shib2 libicu-dev \
    && docker-php-ext-install zip \
    && docker-php-ext-configure intl \
    && docker-php-ext-install intl \
    && docker-php-ext-install mbstring

ADD docker/etc /etc
ADD docker/bin/container-entrypoint.sh /container-entrypoint.sh
RUN chmod ugo+x /container-entrypoint.sh

ENTRYPOINT [ "/container-entrypoint.sh" ]
