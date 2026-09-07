<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = [
        'key',
        'value',
        'group',
        'type',
    ];

    /**
     * In-memory runtime cache to eliminate repeated queries within the same request lifecycle.
     *
     * @var array<string, mixed>
     */
    protected static array $runtimeCache = [];

    /**
     * Flag indicating whether all settings have been bulk-preloaded.
     */
    protected static bool $allLoaded = false;

    /**
     * Model boot hooks to ensure runtime caches are invalidated on save/delete.
     */
    protected static function booted(): void
    {
        static::saved(function () {
            static::flushRuntimeCache();
            if (class_exists(\App\Services\TypographyService::class)) {
                \App\Services\TypographyService::clearCache();
            }
            Cache::forget('all_settings_array');
        });

        static::deleted(function () {
            static::flushRuntimeCache();
            if (class_exists(\App\Services\TypographyService::class)) {
                \App\Services\TypographyService::clearCache();
            }
            Cache::forget('all_settings_array');
        });
    }

    /**
     * Preload all settings into runtime memory at once.
     */
    public static function preloadAll(): void
    {
        try {
            $settings = static::all();
            foreach ($settings as $s) {
                static::$runtimeCache[$s->key] = static::castValue($s->value, $s->type);
            }
            static::$allLoaded = true;
        } catch (\Throwable) {}
    }

    /**
     * Flush the in-memory runtime cache (useful for tests or setting updates).
     */
    public static function clearCache(): void
    {
        static::flushRuntimeCache();
        \Illuminate\Support\Facades\Cache::forget('all_settings_array');
    }

    public static function flushRuntimeCache(): void
    {
        static::$runtimeCache = [];
        static::$allLoaded = false;
    }

    /**
     * Get a setting value with fallback and high-speed in-memory caching.
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        if (app()->runningUnitTests()) {
            $setting = static::where('key', $key)->first();
            return $setting !== null ? static::castValue($setting->value, $setting->type) : $default;
        }

        if (array_key_exists($key, static::$runtimeCache)) {
            return static::$runtimeCache[$key];
        }

        // On first get, bulk load all settings into memory at once
        if (!static::$allLoaded) {
            static::preloadAll();
            if (array_key_exists($key, static::$runtimeCache)) {
                return static::$runtimeCache[$key];
            }
        }

        // If key was not in DB table, memoize the default value to avoid querying again
        static::$runtimeCache[$key] = $default;
        return $default;
    }

    /**
     * Set/save a setting value with group, type, and cache invalidation.
     *
     * @param string $key
     * @param mixed $value
     * @param string $group
     * @param string|null $type
     * @return static
     */
    public static function set(string $key, mixed $value, string $group = 'general', ?string $type = null): static
    {
        if ($type === null) {
            $type = static::detectType($value);
        }

        $serializedValue = is_array($value) || is_object($value) ? json_encode($value) : (string) $value;

        $setting = static::updateOrCreate(
            ['key' => $key],
            [
                'value' => $serializedValue,
                'group' => $group,
                'type' => $type,
            ]
        );

        $cast = static::castValue($serializedValue, $type);
        static::$runtimeCache[$key] = $cast;

        if (class_exists(\App\Services\TypographyService::class)) {
            \App\Services\TypographyService::clearCache();
        }

        Cache::forget("setting_{$key}");
        Cache::forget('all_settings_array');

        return $setting;
    }

    /**
     * Get all settings grouped as an associative array.
     *
     * @return array<string, mixed>
     */
    public static function getAll(): array
    {
        try {
            return Cache::rememberForever('all_settings_array', function () {
                $settings = static::all();
                $result = [];

                foreach ($settings as $setting) {
                    $result[$setting->key] = static::castValue($setting->value, $setting->type);
                }

                return $result;
            });
        } catch (\Throwable $e) {
            return [];
        }
    }

    /**
     * Cast raw database value to appropriate PHP type.
     */
    protected static function castValue(?string $value, string $type): mixed
    {
        if ($value === null) {
            return null;
        }

        return match ($type) {
            'integer', 'int' => (int) $value,
            'float', 'double', 'decimal' => (float) $value,
            'boolean', 'bool' => filter_var($value, FILTER_VALIDATE_BOOLEAN),
            'json', 'array' => json_decode($value, true) ?? [],
            default => $value,
        };
    }

    /**
     * Detect type from value.
     */
    protected static function detectType(mixed $value): string
    {
        if (is_bool($value)) {
            return 'boolean';
        }
        if (is_int($value)) {
            return 'integer';
        }
        if (is_float($value)) {
            return 'float';
        }
        if (is_array($value) || is_object($value)) {
            return 'json';
        }

        return 'string';
    }
}
