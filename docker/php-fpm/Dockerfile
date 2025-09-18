FROM php:8.2-fpm-alpine

# Установка системных зависимостей для PHP-FPM
RUN apk add --no-cache \
    curl \
    git \
    zip \
    unzip \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    libzip-dev \
    icu-dev \
    oniguruma-dev \
    mysql-client

# Установка PHP расширений
RUN docker-php-ext-configure gd \
    && docker-php-ext-install -j$(nproc) \
        pdo_mysql \
        zip \
        gd \
        intl \
        mbstring \
        bcmath \
        opcache

# Установка Redis расширения
RUN apk add --no-cache --virtual .build-deps $PHPIZE_DEPS \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && apk del .build-deps

# Установка Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Установка рабочей директории
WORKDIR /var/www/html

ENV PHP=php
COPY composer.json composer.lock ./

# Установка зависимостей без автозагрузки скриптов
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-scripts

# Копирование остальных файлов проекта
COPY . .

RUN git config --global --add safe.directory /var/www/html

# Запуск post-install скриптов после копирования всех файлов
RUN composer run-script post-autoload-dump --no-interaction || true

# Установка правильных прав доступа
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage \
    && chmod -R 755 /var/www/html/bootstrap/cache

RUN touch /var/log/php-fpm-slow.log
RUN chown www-data:www-data /var/log/php-fpm-slow.log
RUN chmod 640 /var/log/php-fpm-slow.log

RUN touch /var/log/php_errors.log
RUN chown www-data:www-data /var/log/php_errors.log
RUN chmod 640 /var/log/php_errors.log

RUN touch /var/log/opcache_errors.log
RUN chown www-data:www-data /var/log/opcache_errors.log
RUN chmod 640 /var/log/opcache_errors.log

# /var/log/php-fpm-slow.log /var/log/php_errors.log /var/log/opcache_errors.log

# Создание директории для PID файлов
RUN mkdir -p /var/run/php-fpm && chown www-data:www-data /var/run/php-fpm

USER www-data

EXPOSE 9000

CMD ["php-fpm", "-F"]
