FROM php:8.1-apache

# Install the PDO MySQL extension your config.php requires
RUN docker-php-ext-install pdo_mysql

# Set Apache's document root to your public/ folder (MVC best practice)
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Enable Apache mod_rewrite for clean URLs
RUN a2enmod rewrite

WORKDIR /var/www/html