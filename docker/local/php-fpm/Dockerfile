FROM php:8.3-fpm-alpine

# Установка системных зависимостей
RUN apk add --no-cache \
    git \
    unzip \
    postgresql-dev \
    $PHPIZE_DEPS

# Установка PHP расширений
RUN docker-php-ext-install pdo pdo_pgsql

# Установка Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer


WORKDIR /var/www/html

# Копирование composer файлов
COPY composer.json composer.lock ./

# Установка зависимостей
RUN composer install --no-scripts --no-autoloader

# Копирование остального кода
COPY . .

# Завершение установки composer
RUN composer dump-autoload --optimize

EXPOSE 8000
