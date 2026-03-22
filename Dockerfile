FROM php:8.2-apache

# Install system dependencies
RUN apt-get update && apt-get install -y \
    libzip-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libxml2-dev \
    libonig-dev \
    unzip \
    git \
    pkg-config \
    && docker-php-ext-configure gd --with-jpeg --with-freetype \
    && docker-php-ext-install -j$(nproc) mysqli pdo pdo_mysql zip gd xml mbstring \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Enable Apache rewrite
RUN a2enmod rewrite
RUN sed -i 's/AllowOverride None/AllowOverride All/g' /etc/apache2/apache2.conf

# 🔥 FIX: match host user (UID 1000)
RUN usermod -u 1000 www-data && groupmod -g 1000 www-data

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy project files
COPY . .

# Fix PHP sessions
RUN mkdir -p /var/www/html/sf9_files/php_sessions \
    && chown -R www-data:www-data /var/www/html/sf9_files/php_sessions \
    && chmod 775 /var/www/html/sf9_files/php_sessions \
    && echo "session.save_path = /var/www/html/sf9_files/php_sessions" > /usr/local/etc/php/conf.d/session.ini
    
# 🔥 FIX: set proper ownership INSIDE container
RUN chown -R www-data:www-data /var/www/html

# Install Composer dependencies
RUN composer install --no-interaction --no-scripts --optimize-autoloader

EXPOSE 80