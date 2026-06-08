<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = [
        'key',
        'value'
    ];

    /**
     * Helper para obtener un valor por su clave de configuración.
     */
    public static function getVal(string $key, $default = null): ?string
    {
        try {
            $setting = self::where('key', $key)->first();
            return $setting ? $setting->value : $default;
        } catch (\Throwable $e) {
            return $default;
        }
    }

    /**
     * Helper para establecer un valor por su clave de configuración.
     */
    public static function setVal(string $key, ?string $value): self
    {
        return self::updateOrCreate(['key' => $key], ['value' => $value]);
    }
}
