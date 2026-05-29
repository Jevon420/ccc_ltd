<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Setting extends Model
{
    use LogsActivity;

    protected $fillable = ['key', 'value', 'type', 'group', 'description', 'is_public'];

    protected $casts = [
        'is_public' => 'boolean',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['key', 'value'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn ($event) => "Setting [{$this->key}] {$event}");
    }

    // -------------------------------------------------------------------------
    // Static Helpers
    // -------------------------------------------------------------------------

    /**
     * Get a setting value by key, with optional default.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        return Cache::rememberForever("setting:{$key}", function () use ($key, $default) {
            $setting = static::where('key', $key)->first();

            if (! $setting) {
                return $default;
            }

            return match ($setting->type) {
                'boolean' => filter_var($setting->value, FILTER_VALIDATE_BOOLEAN),
                'integer' => (int) $setting->value,
                'json'    => json_decode($setting->value, true),
                default   => $setting->value,
            };
        });
    }

    /**
     * Set a setting value by key.
     */
    public static function set(string $key, mixed $value, string $type = 'string'): static
    {
        $setting = static::updateOrCreate(
            ['key' => $key],
            [
                'value' => is_array($value) ? json_encode($value) : (string) $value,
                'type'  => $type,
            ]
        );

        Cache::forget("setting:{$key}");

        return $setting;
    }

    /**
     * Clear cached value for a key.
     */
    public static function clearCache(string $key): void
    {
        Cache::forget("setting:{$key}");
    }

    /**
     * Flush all setting caches.
     */
    public static function flushCache(): void
    {
        // Individual keys must be cleared; use tagged cache if your driver supports it
        $keys = static::pluck('key');
        foreach ($keys as $key) {
            Cache::forget("setting:{$key}");
        }
    }
}
