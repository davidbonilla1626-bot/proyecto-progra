<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo; // Importante para la relación

class Product extends Model
{
    // Mantenemos el fillable para el CRUD y los Seeders
    protected $fillable = [
        'category_id',
        'name',
        'description',
        'price',
        'image',
        'image_path',
        'stock'
    ];

    /**
     * Relación: Un producto pertenece a una categoría.
     * Esto cumple con el requisito de "entidades relacionadas".
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}