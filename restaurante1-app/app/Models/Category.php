<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    // Esto permite insertar datos en las columnas de tu imagen
    protected $fillable = ['name', 'icon_path']; 

    /**
     * Relación: Una categoría tiene muchos productos.
     */
    public function products()
    {
        return $this->hasMany(Product::class);
    }
}