FROM php:8.3-cli-alpine

# Install system dependencies
RUN apk add --no-cache \
    postgresql-dev \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    zip \
    unzip \
    git \
    curl \
    oniguruma-dev \
    libxml2-dev

# Install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install \
        pdo \
        pdo_pgsql \
        pgsql \
        mbstring \
        xml \
        bcmath \
        gd \
        tokenizer \
        fileinfo \
        opcache

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copy composer files first (layer caching)
COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader --no-scripts --no-interaction

# Copy application files
COPY . .

# Permissions
RUN chmod -R 775 storage bootstrap/cache

# Cache config and routes at build time (best-effort)
RUN php artisan config:cache 2>/dev/null || true \
    && php artisan route:cache 2>/dev/null || true

# Railway sets $PORT dynamically — use shell CMD form so it expands
CMD sh -c "php artisan migrate --force && php artisan db:seed --force --class=DatabaseSeeder 2>/dev/null; php artisan storage:link 2>/dev/null; php artisan serve --host=0.0.0.0 --port=${PORT:-8000}"
