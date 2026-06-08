<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class RankingController extends Controller
{
    /**
     * Muestra el ranking de clientes (solo admin/personal).
     */
    public function index()
    {
        $clients = User::where('role', 'user')
            ->withCount(['orders' => function ($query) {
                $query->where('status', 'Entregado');
            }])
            ->get()
            ->map(function ($client) {
                $totalSpent = $client->orders()->where('status', 'Entregado')->sum('total');
                
                // Determinar Nivel de Fidelización basado en los puntos del usuario
                $points = $client->points;
                $level = 'Bronce';
                if ($points >= 1000) {
                    $level = 'Platino';
                } elseif ($points >= 300) {
                    $level = 'Oro';
                } elseif ($points >= 100) {
                    $level = 'Plata';
                }

                return [
                    'id' => $client->id,
                    'name' => $client->name,
                    'email' => $client->email,
                    'orders_count' => $client->orders_count,
                    'total_spent' => (float) $totalSpent,
                    'points' => $points,
                    'level' => $level
                ];
            })
            ->sortByDesc('total_spent')
            ->values();

        return Inertia::render('ClientRankingView', [
            'clients' => $clients
        ]);
    }
}
