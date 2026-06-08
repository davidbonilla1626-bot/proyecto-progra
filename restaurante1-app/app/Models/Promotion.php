<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Promotion extends Model
{
    protected $fillable = [
        'code',
        'type', // 'fixed' or 'percent'
        'value',
        'expires_at'
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'value' => 'float',
    ];

    /**
     * Verifica si la promoción está activa y no ha vencido.
     */
    public function isValid(): bool
    {
        return $this->expires_at->isFuture();
    }

    /**
     * Calcula el descuento para un subtotal dado.
     */
    public function calculateDiscount(float $subtotal): float
    {
        if (!$this->isValid()) {
            return 0.00;
        }

        if ($this->type === 'percent') {
            return round(($subtotal * ($this->value / 100)), 2);
        }

        // Tipo fijo
        return min($this->value, $subtotal);
    }
}
