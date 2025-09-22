FROM php:8.3-fpm

# Аргументы для настройки пользователя
ARG USER_ID=1000
ARG GROUP_ID=1000

# Установка зависимостей
RUN apt-get update && apt-get install -y \
    build-essential \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    libicu-dev \
    locales \
    zip \
    jpegoptim optipng pngquant gifsicle \
    vim \
    unzip \
    git \
    curl \
    libzip-dev \
    libonig-dev \
    libxml2-dev \
    libpq-dev \
    libwebp-dev \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Установка расширений PHP
RUN docker-php-ext-install pdo_mysql mbstring zip exif pcntl bcmath gd sockets intl

# Установка Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Установка Node.js 20.x
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs \
    && npm install -g npm@latest

# Настройка пользователя и прав
RUN usermod -u ${USER_ID} www-data && \
    groupmod -g ${GROUP_ID} www-data && \
    mkdir -p /var/www/html && \
    chown -R www-data:www-data /var/www/html

# Создание рабочей директории
WORKDIR /var/www/html

# Копирование файлов проекта с правильными правами
COPY --chown=www-data:www-data . .

# Установка прав для необходимых директорий
RUN mkdir -p storage/framework/{cache,sessions,testing,views} \
    && mkdir -p storage/logs \
    && mkdir -p bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# Установка зависимостей Laravel
USER www-data
RUN composer install --optimize-autoloader --no-dev \
    && npm install \
    && npm run build

# Возврат к root для запуска php-fpm
USER root

# Опционально: запуск миграций и сидов
# RUN php artisan migrate --seed

EXPOSE 9000
CMD ["php-fpm"]
