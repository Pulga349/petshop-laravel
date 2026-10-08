<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = ['key', 'value'];

    /**
     * Lazy defaults applied when a key has no stored row, so migrate:fresh
     * works without a settings seeder (tests rely on this).
     */
    public const DEFAULTS = [
        'iva_rate' => '21',
        'discount_bronze' => '0',
        'discount_silver' => '5',
        'discount_gold' => '10',
        'discount_platinum' => '15',
    ];

    /**
     * Resolve a setting value: stored row > lazy default > caller default.
     * Cached forever under "settings.{key}" until set() invalidates it.
     */
    public static function get(string $key, $default = null)
    {
        return Cache::rememberForever("settings.{$key}", function () use ($key, $default) {
            $stored = static::query()->where('key', $key)->value('value');

            if ($stored !== null) {
                return $stored;
            }

            return static::DEFAULTS[$key] ?? $default;
        });
    }

    /**
     * Persist a setting and forget its cache entry.
     */
    public static function set(string $key, string $value): void
    {
        static::updateOrCreate(['key' => $key], ['value' => $value]);

        Cache::forget("settings.{$key}");
    }
}
