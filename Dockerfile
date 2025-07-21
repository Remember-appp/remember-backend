FROM php:8.4-fpm-alpine

# Встановлюємо необхідні пакети
RUN apk add --no-cache \
    bash \
    git \
    curl \
    icu-dev \
    libxml2-dev \
    libzip-dev \
    oniguruma-dev \
    postgresql-dev \
    zlib-dev \
    unzip \
    autoconf \
    g++ \
    make \
    nodejs \
    npm

# PHP extensions
RUN docker-php-ext-install \
    intl \
    pdo \
    pdo_pgsql \
    mbstring \
    exif \
    pcntl \
    bcmath \
    zip

# Встановлюємо Composer
COPY --from=composer:2.7 /usr/bin/composer /usr/bin/composer

# Робоча директорія
WORKDIR /var/www/html

# Копіюємо проєкт (якщо треба, відфільтруй через .dockerignore)
COPY . .

# Встановлення залежностей Laravel
RUN composer install --no-interaction --optimize-autoloader --ignore-platform-reqs

# Права доступу
RUN chown -R www-data:www-data /var/www/html

EXPOSE 9000

CMD ["php-fpm"]
