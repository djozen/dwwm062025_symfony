# Dockerfile for a Symfony application with Apache, PHP, Composer, and Node.js
FROM ubuntu:24.04

# Install composer
ARG COMPOSER_VERSION="2.8.8"
ADD --chmod=755 https://getcomposer.org/download/${COMPOSER_VERSION}/composer.phar /usr/local/bin/composer

# Set DEBIAN_FRONTEND to noninteractive to avoid prompts during apt-get install
ENV DEBIAN_FRONTEND=noninteractive

# Install base dependencies, Apache, PHP, and PHP extensions in a single RUN command
RUN apt-get update && \
    apt-get install -y --no-install-recommends \
    apache2 libapache2-mod-fcgid \
    php php-fpm \
    libicu-dev libzip-dev libpng-dev git unzip \
    php-cli php-mbstring php-xml php-curl php-zip php-gd php-intl php-mysql && \
    # Clean up apt cache to reduce image size
    apt-get clean && \
    rm -rf /var/lib/apt/lists/*

# Config Apache
COPY .docker/vhost.conf /etc/apache2/sites-available/000-default.conf
RUN a2enmod rewrite headers actions fcgid alias proxy_fcgi

# Set APP_ENV - default to dev, can be overridden at build time
ARG APP_ENV=dev
ENV APP_ENV=${APP_ENV}

WORKDIR /var/www/html

# Copy composer files and install dependencies first to leverage Docker cache
COPY composer.json composer.lock symfony.lock ./
# Install symfony/apache-pack first if it's always needed
RUN composer require symfony/apache-pack --prefer-dist --no-scripts --no-progress --no-interaction
RUN composer install --prefer-dist --no-scripts --no-progress --no-interaction \
    # Use --no-dev and --optimize-autoloader for production builds
    # ${APP_ENV_ARGS} will be empty for dev, and "--no-dev --optimize-autoloader" for prod
    $(if [ "$APP_ENV" = "prod" ]; then echo "--no-dev --optimize-autoloader"; fi)

# Copy the rest of the application code
COPY . .

# Create necessary directories and set permissions
RUN mkdir -p /var/www/html/var/log/${APP_ENV} && \
    mkdir -p /var/www/html/var/cache/${APP_ENV} && \
    mkdir -p /var/www/html/var/sessions/${APP_ENV} && \
    mkdir -p /var/www/html/var/tmp/${APP_ENV} && \
    mkdir -p /var/www/html/public/uploads && \
    chown -R www-data:www-data /var/www/html/public/uploads && \
    chmod -R 775 /var/www/html/public/uploads

# Copy environment files
# Note: rm .env and .env.dev are removed as we directly copy the correct ones
COPY ./.docker/.env .env
RUN sed -i "s/APP_ENV=.*/APP_ENV=${APP_ENV}/" .env
# Only copy .env.${APP_ENV} if it exists in the docker directory
# This avoids a build failure if, for example, .env.prod doesn't exist when APP_ENV=prod
RUN if [ -f "./.docker/.env.${APP_ENV}" ]; then cp ./.docker/.env.${APP_ENV} .env.${APP_ENV}; fi

# Run Symfony post-install commands
RUN composer recipes:install --no-interaction
RUN bin/console assets:install --symlink --relative --no-debug
RUN bin/console cache:clear --no-debug $(if [ "$APP_ENV" = "prod" ]; then echo "--no-warmup"; fi)

# Set permissions for var directory
RUN chmod -R 775 /var/www/html/var && \
    chown -R www-data:www-data /var/www/html/var

COPY ./.docker/.htaccess /var/www/html/public/.htaccess

EXPOSE 80

COPY .docker/entrypoint.sh /scripts/entrypoint.sh
RUN chmod +x /scripts/entrypoint.sh
ENTRYPOINT ["/scripts/entrypoint.sh"]