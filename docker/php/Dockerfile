FROM php:8.4-fpm

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

# 1. Copia arquivos que mudam com pouca frequência (composer.json e lock)
COPY composer.json composer.lock ./

# 2. COPIA O CÓDIGO FONTE COMPLETO (NECESSÁRIO PARA O ARTISAN)
COPY . .

# 3. Instala dependências (agora que o 'artisan' existe)
RUN composer install --no-dev --optimize-autoloader

# 4. COPIA O ARQUIVO DE AMBIENTE
COPY .env.example .env

# Permissões do Laravel
RUN chown -R www-data:www-data /var/www

RUN chmod -R 775 storage bootstrap/cache

CMD ["php-fpm"]
