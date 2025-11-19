FROM php:8.3-fpm

# Extensões essenciais do Laravel
RUN apt-get update && apt-get install -y \
    zip unzip curl git libpq-dev libzip-dev libpng-dev libonig-dev libxml2-dev \
    && docker-php-ext-install pdo pdo_mysql pdo_pgsql zip gd mbstring exif pcntl \
    && apt-get clean

# Instalar Redis PHP extension
RUN pecl install redis && docker-php-ext-enable redis

# Instalar Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

# Permissões do Laravel
RUN chown -R www-data:www-data /var/www

CMD ["php-fpm"]
