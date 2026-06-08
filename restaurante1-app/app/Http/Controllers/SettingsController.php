<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SettingsController extends Controller
{
    /**
     * Actualiza las horas de atención del restaurante (Solo Admin).
     */
    public function updateHours(Request $request)
    {
        if (!Auth::user()->isAdmin()) {
            return redirect()->back()->with('error', 'No tienes autorización para realizar esta acción.');
        }

        $request->validate([
            'opening_time' => 'required|date_format:H:i',
            'closing_time' => 'required|date_format:H:i',
        ]);

        Setting::setVal('opening_time', $request->opening_time);
        Setting::setVal('closing_time', $request->closing_time);

        \App\Models\AuditLog::log("Actualizó el horario de atención: {$request->opening_time} a {$request->closing_time}");

        return redirect()->back()->with('message', 'Horario de atención actualizado correctamente.');
    }
}
