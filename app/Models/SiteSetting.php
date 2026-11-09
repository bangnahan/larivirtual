<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Throwable;

class SiteSetting extends Model
{
    protected $guarded = ['id'];

    public static function get(string $key, mixed $default = null): mixed
    {
        try {
            return Cache::remember("site_setting.{$key}", 3600, function () use ($key, $default) {
                $record = static::where('key', $key)->first();

                return $record !== null ? $record->value : $default;
            });
        } catch (Throwable) {
            return $default;
        }
    }

    public static function set(string $key, mixed $value): void
    {
        static::updateOrCreate(['key' => $key], ['value' => (string) $value]);
        Cache::forget("site_setting.{$key}");
    }
}
