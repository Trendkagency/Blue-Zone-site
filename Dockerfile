# syntax=docker/dockerfile:1
# =========================================================
#  Dockerfile - Laravel + Filament v3 (No Node / No Queue)
#  Frontend assets are pre-built locally via `npm run build`
#  Services: Nginx + PHP-FPM (Supervisor)
# =========================================================

# ---------- Stage 1: Composer dependencies ----------
FROM composer:2 AS composer_build
WORKDIR /app

ENV COMPOSER_ALLOW_SUPERUSER=1
ENV COMPOSER_MEMORY_LIMIT=-1
ENV COMPOSER_PROCESS_TIMEOUT=600

# Copy composer definition files
COPY composer.json composer.lock ./

RUN composer config process-timeout 600 \
    && composer install \
        --no-dev \
        --no-scripts \
        --no-autoloader \
        --prefer-dist \
        --ignore-platform-reqs \
        --no-interaction

COPY . .
RUN composer dump-autoload --optimize --no-dev --classmap-authoritative


# ---------- Stage 2: Production image ----------
FROM php:8.3-fpm-alpine AS production

# System packages: Nginx, Supervisor, PHP extensions
RUN apk add --no-cache \
        nginx \
        supervisor \
        bash \
        curl \
        libzip \
        libpng \
        freetype \
        libjpeg-turbo \
        icu-libs \
        oniguruma \
        libzip-dev \
        libpng-dev \
        freetype-dev \
        jpeg-dev \
        icu-dev \
        oniguruma-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j"$(nproc)" \
        pdo_mysql \
        zip \
        gd \
        intl \
        mbstring \
        bcmath \
        exif \
    && docker-php-ext-enable opcache \
    && apk del --no-cache \
        libzip-dev libpng-dev freetype-dev jpeg-dev icu-dev oniguruma-dev

WORKDIR /app

# Copy application files and pre-installed composer dependencies
COPY --chown=www-data:www-data . .
COPY --from=composer_build --chown=www-data:www-data /app/vendor ./vendor

# Remove unnecessary files in production
RUN rm -rf \
        tests \
        .git \
        .github \
        .env.example \
        storage/logs/*.log \
        node_modules \
    && composer clear-cache 2>/dev/null || true

# Set storage permissions
RUN chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# Copy Nginx, PHP-FPM, and Supervisor configs
COPY docker/nginx.conf /etc/nginx/nginx.conf
COPY docker/php-fpm.conf /usr/local/etc/php-fpm.d/zz-custom.conf
COPY docker/supervisord.conf /etc/supervisord.conf
COPY docker/start.sh /start.sh

RUN chmod +x /start.sh \
    && mkdir -p /var/log/supervisor /var/lib/nginx/tmp \
    && chown -R www-data:www-data /var/lib/nginx /var/log/nginx

EXPOSE 80

CMD ["/start.sh"]