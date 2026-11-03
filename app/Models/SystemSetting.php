<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SystemSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
        'group',
        'type',
    ];

    public static function get(string $key, mixed $default = null): mixed
    {
        $setting = static::where('key', $key)->first();
        if (!$setting) {
            return $default;
        }

        return match ($setting->type) {
            'json', 'array' => json_decode((string)$setting->value, true),
            'boolean' => (bool)$setting->value,
            'integer' => (int)$setting->value,
            default => $setting->value,
        };
    }

    public static function set(string $key, mixed $value, string $group = 'general', string $type = 'string'): self
    {
        $storedValue = is_array($value) ? json_encode($value) : (string)$value;
        return static::updateOrCreate(
            ['key' => $key],
            ['value' => $storedValue, 'group' => $group, 'type' => $type]
        );
    }
}
