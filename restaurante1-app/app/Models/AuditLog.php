<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditLog extends Model
{
    // No usa updated_at ya que es solo un log de auditoría
    const UPDATED_AT = null;

    protected $fillable = [
        'user_id',
        'action',
        'ip_address'
    ];

    /**
     * Helper para registrar auditoría de forma sencilla.
     */
    public static function log(string $action): void
    {
        self::create([
            'user_id' => \Illuminate\Support\Facades\Auth::id(),
            'action' => $action,
            'ip_address' => request()->ip()
        ]);
    }

    /**
     * Relación con el usuario responsable.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
