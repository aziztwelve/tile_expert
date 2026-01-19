#FROM php:8.5-fpm-alpine
#
## Установка основных зависимостей
#RUN apk --no-cache add \
#    git curl bash unzip zip \
#    oniguruma-dev libxml2-dev libzip-dev icu-dev icu-libs icu-data-full \
#    postgresql-dev libxslt \
#    freetype libpng libjpeg-turbo freetype-dev libpng-dev libjpeg-turbo-dev \
#    linux-headers ttf-liberation \
#    && rm -fr /var/cache/apk/*
#
## Настройка и установка расширений
#RUN docker-php-ext-configure intl \
#    && docker-php-ext-configure gd --with-freetype --with-jpeg \
#    && docker-php-ext-install -j$(nproc) \
#        pdo \
#        pdo_pgsql \
#        pgsql \
#        intl \
#        mbstring \
#        pcntl \
#        bcmath \
#        zip \
#        gd
#
## Установка Composer
#RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/bin --filename=composer \
#    && rm -rf /root/.composer/cache
#
## Установка Xdebug
#RUN apk add --no-cache $PHPIZE_DEPS \
#    && pecl install xdebug \
#    && docker-php-ext-enable xdebug
#
## Настройка Xdebug в одном файле
#RUN { \
#    echo "zend_extension=$(php-config --extension-dir)/xdebug.so"; \
#    echo "xdebug.mode=debug"; \
#    echo "xdebug.start_with_request=yes"; \
#    echo "xdebug.client_host=host.docker.internal"; \
#} > /usr/local/etc/php/conf.d/xdebug.ini
#
## Рабочая директория
#WORKDIR /var/www
#
## Запуск PHP-FPM
#CMD ["php-fpm"]

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
