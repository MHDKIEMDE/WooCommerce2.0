<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = ['key', 'value', 'group'];

    public static function get(string $key, mixed $default = null): mixed
    {
        return Cache::remember("setting:{$key}", now()->addMinutes(60), function () use ($key, $default) {
            return static::where('key', $key)->value('value') ?? $default;
        });
    }

    public static function set(string $key, mixed $value, string $group = 'general'): void
    {
        static::updateOrCreate(['key' => $key], ['value' => $value, 'group' => $group]);
        Cache::forget("setting:{$key}");
    }

    public static function getGroup(string $group): array
    {
        return Cache::remember("settings:group:{$group}", now()->addMinutes(60), function () use ($group) {
            return static::where('group', $group)->pluck('value', 'key')->toArray();
        });
    }

    public static function setGroup(string $group, array $data): void
    {
        foreach ($data as $key => $value) {
            static::set($key, $value, $group);
        }
        Cache::forget("settings:group:{$group}");
    }
}
