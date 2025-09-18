FROM php:8.2-cli-alpine

# Установка системных зависимостей для Scheduler
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

# Создание crontab файла
RUN echo "* * * * * cd /var/www/html && /usr/local/bin/php artisan schedule:run >> /var/log/cron.log 2>&1" > /var/spool/cron/crontabs/www-data
RUN chown www-data:www-data /var/spool/cron/crontabs/www-data
RUN chmod 0600 /var/spool/cron/crontabs/www-data

# Создание лог файла для cron
RUN touch /var/log/cron.log && chown www-data:www-data /var/log/cron.log

# Копирование скрипта запуска
COPY docker/scheduler/start-scheduler.sh /start-scheduler.sh
RUN chmod +x /start-scheduler.sh

USER www-data

CMD ["/start-scheduler.sh"]
