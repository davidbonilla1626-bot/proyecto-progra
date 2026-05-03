<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    // Esto permite insertar datos en las columnas de tu imagen
    protected $fillable = ['name', 'icon_path']; 
}