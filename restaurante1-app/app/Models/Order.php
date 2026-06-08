<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'order_number',
        'status',
        'total',
        'discount',
        'promotion_code',
        'notes'
    ];

    /**
     * Relación: Un pedido pertenece a un usuario cliente.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relación: Un pedido tiene varios ítems.
     */
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Relación: Un pedido puede tener una calificación.
     */
    public function rating()
    {
        return $this->hasOne(Rating::class);
    }

    /**
     * Relación: Un pedido puede tener muchos mensajes de chat.
     */
    public function chatMessages(): HasMany
    {
        return $this->hasMany(ChatMessage::class);
    }
}
