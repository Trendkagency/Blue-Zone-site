<?php

$baseDir = realpath(__DIR__ . '/..');

// 1. Specific custom files:
$appServiceProvider = <<<'PHP'
<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if ($this->app->environment('production') || config('app.env') === 'production' || str_starts_with(config('app.url', ''), 'https://')) {
            URL::forceScheme('https');
        }

        Gate::before(function ($user, string $ability) {
            if ($user instanceof User && $user->hasRole(['super_admin', 'Super Admin', 'admin'])) {
                return true;
            }
        });

        $permissions = [
            'manage_products',
            'manage_inventory',
            'manage_orders',
            'manage_customers',
            'manage_content',
            'manage_settings',
            'manage_users',
            'view_reports',
            'create_offline_sales',
            'manage_invoices',
        ];

        foreach ($permissions as $permission) {
            Gate::define($permission, function ($user) use ($permission) {
                return $user instanceof User && $user->hasPermission($permission);
            });
        }
    }
}
PHP;

$bootstrapApp = <<<'PHP'
<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->trustProxies(at: '*');

        $middleware->web(append: [
            \App\Http\Middleware\SetLocaleMiddleware::class,
            \App\Http\Middleware\SecurityAndPerformanceHeaders::class,
        ]);

        $middleware->validateCsrfTokens(except: [
            'webhooks/*',
        ]);

        $middleware->redirectGuestsTo(function (Request $request) {
            if ($request->is('admin/*') || $request->is('admin')) {
                return route('filament.admin.auth.login');
            }
            return route('customer.auth.login');
        });

        $middleware->alias([
            'role' => \App\Http\Middleware\EnsureUserHasRole::class,
            'permission' => \App\Http\Middleware\EnsureUserHasPermission::class,
            'auth.customer' => \App\Http\Middleware\AuthenticateCustomer::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );
    })->create();
PHP;

$dockerfile = <<<'DOCKERFILE'
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
DOCKERFILE;

$dockerignore = <<<'DOCKERIGNORE'
.git
.github
node_modules
vendor
tests
storage/logs/*
storage/framework/cache/*
storage/framework/sessions/*
storage/framework/views/*
.env
.env.example
*.md
docker-compose*.yml
.idea
.vscode
DOCKERIGNORE;

$gitignore = <<<'GITIGNORE'
*.log
.DS_Store
.env
.env.backup
.env.production
.phpactor.json
.phpunit.result.cache
/.codex
/.cursor/
/.idea
/.nova
/.phpunit.cache
/.vscode
/.zed
/auth.json
/node_modules
/public/fonts-manifest.dev.json
/public/hot
/public/storage
/storage/*.key
/storage/pail
/vendor
_ide_helper.php
Homestead.json
Homestead.yaml
Thumbs.db
GITIGNORE;

$composerJson = <<<'JSON'
{
    "$schema": "https://getcomposer.org/schema.json",
    "name": "laravel/laravel",
    "type": "project",
    "description": "The skeleton application for the Laravel framework.",
    "keywords": ["laravel", "framework"],
    "license": "MIT",
    "require": {
        "php": "^8.3",
        "filament/filament": "^5.7",
        "laravel/framework": "^13.17",
        "laravel/tinker": "^3.0"
    },
    "require-dev": {
        "fakerphp/faker": "^1.23",
        "laravel/pail": "^1.2.5",
        "laravel/pao": "^1.0.6",
        "laravel/pint": "^1.27",
        "mockery/mockery": "^1.6",
        "nunomaduro/collision": "^8.6",
        "phpunit/phpunit": "^12.5.12"
    },
    "autoload": {
        "psr-4": {
            "App\\": "app/",
            "Database\\Factories\\": "database/factories/",
            "Database\\Seeders\\": "database/seeders/"
        }
    },
    "autoload-dev": {
        "psr-4": {
            "Tests\\": "tests/"
        }
    },
    "scripts": {
        "setup": [
            "composer install",
            "@php -r \"file_exists('.env') || copy('.env.example', '.env');\"",
            "@php artisan key:generate",
            "@php artisan migrate --force",
            "npm install --ignore-scripts",
            "npm run build"
        ],
        "dev": [
            "Composer\\Config::disableProcessTimeout",
            "@php artisan dev"
        ],
        "test": [
            "@php artisan config:clear --ansi @no_additional_args",
            "@php artisan test"
        ],
        "post-autoload-dump": [
            "Illuminate\\Foundation\\ComposerScripts::postAutoloadDump",
            "@php artisan package:discover --ansi",
            "@php artisan filament:upgrade"
        ],
        "post-update-cmd": [
            "@php artisan vendor:publish --tag=laravel-assets --ansi --force"
        ],
        "post-root-package-install": [
            "@php -r \"file_exists('.env') || copy('.env.example', '.env');\""
        ],
        "post-create-project-cmd": [
            "@php artisan key:generate --ansi",
            "@php -r \"file_exists('database/database.sqlite') || touch('database/database.sqlite');\"",
            "@php artisan migrate --graceful --ansi"
        ],
        "pre-package-uninstall": [
            "Illuminate\\Foundation\\ComposerScripts::prePackageUninstall"
        ]
    },
    "extra": {
        "laravel": {
            "dont-discover": []
        }
    },
    "config": {
        "optimize-autoloader": true,
        "preferred-install": "dist",
        "sort-packages": true,
        "allow-plugins": {
            "pestphp/pest-plugin": true,
            "php-http/discovery": true
        }
    },
    "minimum-stability": "stable",
    "prefer-stable": true
}
JSON;

file_put_contents($baseDir . '/app/Providers/AppServiceProvider.php', $appServiceProvider);
file_put_contents($baseDir . '/bootstrap/app.php', $bootstrapApp);
file_put_contents($baseDir . '/Dockerfile', $dockerfile);
file_put_contents($baseDir . '/.dockerignore', $dockerignore);
file_put_contents($baseDir . '/.gitignore', $gitignore);
file_put_contents($baseDir . '/composer.json', $composerJson);

// 2. Iterate all remaining files and resolve conflicts by selecting ORIGIN (the database-driven implementation)
$rii = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($baseDir, RecursiveDirectoryIterator::SKIP_DOTS));

$fixedCount = 0;
foreach ($rii as $file) {
    if ($file->isDir()) continue;
    $path = $file->getPathname();
    if (strpos($path, 'node_modules') !== false || strpos($path, 'vendor') !== false || strpos($path, '.git') !== false || strpos($path, 'scratch') !== false) {
        continue;
    }

    $content = file_get_contents($path);
    if (strpos($content, '<<<<<<<') !== false) {
        // Resolve nested / standard conflict markers
        // Pattern matches <<<<<<< ... ======= ... >>>>>>> ...
        $newContent = preg_replace_callback('/<{7}[^\r\n]*\r?\n(.*?)\r?\n={7}\r?\n(.*?)\r?\n>{7}[^\r\n]*/s', function ($matches) {
            // $matches[2] is ORIGIN (the complete version)
            return $matches[2];
        }, $content);

        // Also clean up any lone conflict markers
        $newContent = preg_replace('/^<{7}[^\r\n]*\r?\n/m', '', $newContent);
        $newContent = preg_replace('/^={7}\r?\n/m', '', $newContent);
        $newContent = preg_replace('/^>{7}[^\r\n]*\r?\n/m', '', $newContent);

        if ($newContent !== $content) {
            file_put_contents($path, $newContent);
            $fixedCount++;
            echo "Resolved: " . str_replace($baseDir, '', $path) . "\n";
        }
    }
}

echo "Total resolved files: $fixedCount\n";
