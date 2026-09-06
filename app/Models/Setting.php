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
     * Get a setting value with fallback and caching.
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        try {
            return Cache::rememberForever("setting_{$key}", function () use ($key, $default) {
                $setting = static::where('key', $key)->first();
                if (!$setting) {
                    return $default;
                }

                return static::castValue($setting->value, $setting->type);
            });
        } catch (\Throwable $e) {
            return $default;
        }
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
