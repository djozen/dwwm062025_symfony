# Dockerfile for a Symfony application with Apache, PHP, Composer, and Node.js
FROM ubuntu:24.04

# Install composer
ARG COMPOSER_VERSION="2.8.8"
ADD --chmod=755 https://getcomposer.org/download/${COMPOSER_VERSION}/composer.phar /usr/local/bin/composer

# installation de Apache
RUN apt-get update && apt-get install -y apache2 libapache2-mod-fcgid

#Installation de PHP
RUN apt update && apt-get install -y php php-fpm

# Extensions utiles Symfony
RUN apt update && apt install -y \
        libicu-dev libzip-dev libpng-dev git unzip \
        php-cli php-mbstring php-xml php-curl php-zip php-gd php-intl php-mysql

# Config Apache
COPY docker/vhost.conf /etc/apache2/sites-available/000-default.conf
RUN a2enmod rewrite headers actions fcgid alias proxy_fcgi

# Code
WORKDIR /var/www/html
COPY . .

# Prod env
ENV APP_ENV=prod \
    APP_DEBUG=0 \
    APACHE_DOCUMENT_ROOT=/var/www/html/public


RUN mkdir -p /var/www/html/var/cache/${APP_ENV} && \
    mkdir -p /var/www/html/var/log/${APP_ENV} && \
    chown -R www-data:www-data /var/www/html/var

RUN composer install --prefer-dist --no-scripts --no-progress 

EXPOSE 80

COPY docker/entrypoint.sh scripts/entrypoint.sh
RUN chmod +x docker/entrypoint.sh   
ENTRYPOINT ["scripts/entrypoint.sh"]
