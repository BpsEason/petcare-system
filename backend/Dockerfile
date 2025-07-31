FROM php:8.2-fpm-alpine

# Set working directory
WORKDIR /var/www

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Install dependencies
RUN apk add --no-cache     nginx     git     build-base     autoconf     mysql-client     openssl     libxml2-dev     onig-dev     libzip-dev     gd     gd-dev

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd opcache zip

# Copy existing application files
COPY . .

# Install Composer dependencies
# This step assumes Composer is available in the container
RUN composer install --no-dev --optimize-autoloader

# Generate application key (if not already done)
# This will be run inside the container when it's built or first run
RUN php artisan key:generate --force || true

# Set permissions
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache     && chmod -R 775 /var/www/storage /var/www/bootstrap/cache

# Expose port
EXPOSE 9000

# Start PHP-FPM
CMD ["php-fpm"]
