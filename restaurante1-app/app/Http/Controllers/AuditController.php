<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AuditController extends Controller
{
    /**
     * Muestra el historial de actividades (solo admin).
     */
    public function index()
    {
        $logs = AuditLog::with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(30);

        return Inertia::render('AuditLogsView', [
            'logs' => $logs
        ]);
    }
}
