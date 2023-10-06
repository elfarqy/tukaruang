# Use an official PHP-Apache base image based on Debian
FROM php:8.1-apache

# Install system dependencies
RUN apt-get update && \
    apt-get install -y libpng-dev libjpeg-dev libfreetype6-dev libzip-dev unzip libpq-dev libxml2-dev libcurl4-openssl-dev && \
    apt-get clean

# Install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg && \
    docker-php-ext-install gd pdo pdo_pgsql zip dom xml intl bcmath curl

# Enable Apache modules
RUN a2enmod rewrite proxy proxy_http

# Install Composer globally
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Change the working directory to the Yii folder and run composer install
# COPY --from=composer /usr/bin/composer /usr/bin/composer

# RUN composer self-update

WORKDIR /var/www/html/backend

# COPY . .

ENV COMPOSER_ALLOW_SUPERUSER=1

# RUN composer install

# Run PHP initialization script to set production mode
# RUN php /var/www/html/backend/init --env=Production --overwrite=All

# # Run Yii database migrations
# RUN php /var/www/html/backend/yii migrate --interactive=0

# # Run Yii database migrations
# RUN php /var/www/html/backend/yii generator/data

# Expose port 80
EXPOSE 80

# Start the Apache web server
# ENTRYPOINT ["apache2-foreground"]
