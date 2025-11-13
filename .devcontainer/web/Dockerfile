FROM php:8.2-apache

# Install PDO MySQL extension
RUN docker-php-ext-install pdo pdo_mysql

# Allow Apache .htaccess overrides in project
COPY apache.conf /etc/apache2/conf-enabled/z-dev.conf

# Enable Apache rewrite module for .htaccess support
RUN a2enmod rewrite

# Use the project subfolder as the web root
COPY 000-default.conf /etc/apache2/sites-available/000-default.conf

# Default Apache listens on 80; docker-compose will map to 8080 on host
EXPOSE 80
