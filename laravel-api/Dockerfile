# ---------- STAGE 1: BUILDER ----------
FROM php:8.5-fpm-alpine AS builder

WORKDIR /var/www

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

COPY composer.lock composer.json ./

RUN composer install \
        --no-dev \
        --no-scripts \
        --no-interaction \
        --prefer-dist \
        --optimize-autoloader \
        --ignore-platform-reqs

COPY . .

RUN rm -rf /var/www/docker \
    && find /var/www/vendor -type d \( \
        -name "tests" -o -name "Tests" -o -name "test" -o -name "Test" \
        -o -name "docs" -o -name "doc" -o -name "examples" \
        -o -name ".git" -o -name ".github" \
    \) -exec rm -rf {} + \
    && find /var/www/vendor -type f \( \
        -name "*.md" -o -name "*.markdown" -o -name "*.rst" \
        -o -name "phpunit.xml*" -o -name ".travis.yml" -o -name ".scrutinizer.yml" \
        -o -name ".php_cs*" -o -name ".php-cs-fixer*" -o -name "phpstan*" \
        -o -name "Makefile" -o -name "CHANGELOG*" -o -name "UPGRADE*" \
    \) -delete \
    && composer dump-autoload --optimize --no-dev \
    && rm -rf /root/.composer

# ---------- STAGE 2: PHP-FPM (RUNTIME) ----------
FROM php:8.5-fpm-alpine AS php

WORKDIR /var/www

RUN docker-php-ext-install pdo_mysql \
    && rm -rf /tmp/* /var/cache/apk/*

COPY --from=builder --chown=www-data:www-data /var/www /var/www

EXPOSE 9000

CMD ["php-fpm"]

# ---------- STAGE 3: NGINX (RUNTIME) ----------
FROM nginx:alpine AS nginx

COPY ./docker/nginx.conf /etc/nginx/conf.d/default.conf
COPY --from=builder /var/www/public /var/www/public

EXPOSE 80

CMD ["nginx", "-g", "daemon off;"]
