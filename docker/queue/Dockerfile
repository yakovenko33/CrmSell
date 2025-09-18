FROM php:8.2-cli-alpine

# Установка системных зависимостей для Queue Worker
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

RUN apk add --no-cache supervisor
# Копирование supervisord конфигурации
COPY docker/queue/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Установка правильных прав доступа
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage \
    && chmod -R 755 /var/www/html/bootstrap/cache

# Создание директории для логов supervisor
RUN mkdir -p /var/log/supervisor && chown www-data:www-data /var/log/supervisor

USER www-data

CMD ["/usr/bin/supervisord", "-n", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
