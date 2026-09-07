# syntax=docker/dockerfile:1
# =========================================================
# Dockerfile بديل لـ Nixpacks - Laravel + Filament v3
# nginx + php-fpm + queue worker + scheduler عبر supervisor
# =========================================================

FROM php:8.4-fpm-alpine

# حزم النظام: nginx + supervisor + bash + curl + git + unzip + مكتبات الـ PHP extensions
RUN apk add --no-cache \
    nginx \
    supervisor \
    bash \
    curl \
    git \
    unzip \
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

# تثبيت Composer مباشرة من صورته الرسمية
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

ENV COMPOSER_ALLOW_SUPERUSER=1
ENV COMPOSER_MEMORY_LIMIT=-1
ENV COMPOSER_PROCESS_TIMEOUT=600

# 1. نسخ ملفات composer أولاً للاستفادة من الـ Docker layer caching
COPY composer.json composer.lock ./

RUN composer config process-timeout 600 \
    && composer install \
    --no-dev \
    --no-scripts \
    --no-autoloader \
    --prefer-dist \
    --ignore-platform-reqs \
    --no-interaction

# 2. نسخ كود المشروع بالكامل
COPY --chown=www-data:www-data . .

# 3. إنشاء الـ autoloader المحسن
RUN composer dump-autoload --optimize --no-dev --classmap-authoritative

# 4. تنظيف ملفات التطوير وغير المطلوبة في الـ Production
RUN rm -rf \
    tests \
    .git \
    .github \
    .env.example \
    storage/logs/*.log \
    node_modules \
    && composer clear-cache 2>/dev/null || true

# 5. صلاحيات المجلدات اللازمة لـ Laravel
RUN chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# 6. ملفات إعداد nginx / php-fpm / supervisor
COPY docker/nginx.conf /etc/nginx/nginx.conf
COPY docker/php-fpm.conf /usr/local/etc/php-fpm.d/zz-custom.conf
COPY docker/supervisord.conf /etc/supervisord.conf
COPY docker/start.sh /start.sh

RUN chmod +x /start.sh \
    && mkdir -p /var/log/supervisor /var/lib/nginx/tmp \
    && chown -R www-data:www-data /var/lib/nginx /var/log/nginx

EXPOSE 80

CMD ["/start.sh"]
