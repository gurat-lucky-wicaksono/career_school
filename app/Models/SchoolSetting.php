<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SchoolSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
        'group',
        'type',
        'label',
        'description',
    ];

    /**
     * Cache key prefix for individual settings.
     */
    public const CACHE_PREFIX = 'school_setting_';

    /**
     * Cache key for all settings combined.
     */
    public const CACHE_ALL_KEY = 'school_settings_all_grouped';

    /**
     * Retrieve a setting value by key with cache layer.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        $cachedValue = Cache::remember(self::CACHE_PREFIX . $key, 86400, function () use ($key) {
            $setting = static::where('key', $key)->first();
            return $setting ? $setting->value : null;
        });

        if ($cachedValue === null) {
            return $default;
        }

        return $cachedValue;
    }

    /**
     * Store or update a setting and invalidate related caches.
     */
    public static function set(
        string $key,
        mixed $value,
        ?string $group = 'general',
        ?string $type = 'text',
        ?string $label = null,
        ?string $description = null
    ): self {
        $setting = static::firstOrNew(['key' => $key]);

        $setting->value = is_bool($value) ? ($value ? '1' : '0') : (string) $value;
        if ($group) {
            $setting->group = $group;
        }
        if ($type) {
            $setting->type = $type;
        }
        if ($label) {
            $setting->label = $label;
        }
        if ($description) {
            $setting->description = $description;
        }

        $setting->save();

        Cache::forget(self::CACHE_PREFIX . $key);
        Cache::forget(self::CACHE_ALL_KEY);

        return $setting;
    }

    /**
     * Retrieve all settings grouped by category.
     */
    public static function getAllGrouped(): array
    {
        return Cache::remember(self::CACHE_ALL_KEY, 86400, function () {
            $records = static::all();
            $grouped = [];

            foreach ($records as $item) {
                $grouped[$item->group][$item->key] = $item->value;
            }

            return $grouped;
        });
    }

    /**
     * Clear all setting caches.
     */
    public static function flushCache(): void
    {
        $keys = static::pluck('key');
        foreach ($keys as $key) {
            Cache::forget(self::CACHE_PREFIX . $key);
        }
        Cache::forget(self::CACHE_ALL_KEY);
    }
}
