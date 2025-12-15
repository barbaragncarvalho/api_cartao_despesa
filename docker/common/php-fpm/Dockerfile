# Usa uma imagem base oficial do PHP com FPM
FROM php:8.4-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
        bash \
        curl \
        git \
        libpng-dev \
        libzip-dev \
        zlib1g-dev \
        sqlite3 \
        libsqlite3-dev \
        libonig-dev \
        libjpeg-dev \
        libfreetype-dev \
        nodejs \
        npm \
        && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo pdo_sqlite mbstring exif pcntl bcmath gd sockets

# Instala o Composer globalmente
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

RUN usermod -u 1000 www-data

EXPOSE 9000
