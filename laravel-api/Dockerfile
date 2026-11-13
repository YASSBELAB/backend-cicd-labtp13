FROM php:8.2-apache
RUN docker-php-ext-install pdo_mysql
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
COPY . /var/www/html
RUN composer install --no-dev --ignore-platform-reqs
EXPOSE 80
CMD ["apache2-foreground"]
