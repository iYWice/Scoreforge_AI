<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

/**
 * Represents the SystemSetting application component.
 */
class SystemSetting extends Model
{
    protected $fillable = ['key', 'value'];

    /**
     * Handles getValue.
     */
    public static function getValue(string $key, mixed $default = null): mixed
    {
        return Cache::remember(
            'aiq.setting.'.$key,
            300,
            fn () => static::where('key', $key)->value('value') ?? $default
        );
    }

    /**
     * Handles setValue.
     */
    public static function setValue(string $key, mixed $value): void
    {
        static::updateOrCreate(
            ['key' => $key],
            ['value' => (string) $value]
        );

        Cache::forget('aiq.setting.'.$key);
    }
}
