FROM php:8.2-apache

# Install PDO MySQL extension
RUN docker-php-ext-install pdo pdo_mysql

# Allow Apache .htaccess overrides in project
COPY apache.conf /etc/apache2/conf-enabled/z-dev.conf

# Enable Apache rewrite module for .htaccess support
RUN a2enmod rewrite

# Default Apache listens on 80; docker-compose will map to 8080 on host
EXPOSE 80
