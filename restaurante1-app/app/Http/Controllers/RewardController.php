<?php

namespace App\Http\Controllers;

use App\Models\PointTransaction;
use App\Models\Promotion;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Inertia\Inertia;

class RewardController extends Controller
{
    /**
     * Muestra la interfaz de mis recompensas para el cliente.
     */
    public function index()
    {
        $user = Auth::user();
        $transactions = PointTransaction::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return Inertia::render('RewardsView', [
            'points' => $user->points,
            'transactions' => $transactions
        ]);
    }

    /**
     * Procesa el canje de puntos por un cupón de recompensa.
     */
    public function redeem(Request $request)
    {
        $request->validate([
            'reward_type' => 'required|in:drink,fries,discount'
        ]);

        $user = Auth::user();
        $cost = 0;
        $value = 0.00;
        $name = '';
        $prefix = '';

        switch ($request->reward_type) {
            case 'drink':
                $cost = 50;
                $value = 3.00;
                $name = 'Bebida Gratis';
                $prefix = 'BEBIDA';
                break;
            case 'fries':
                $cost = 80;
                $value = 4.00;
                $name = 'Papas Fritas';
                $prefix = 'PAPAS';
                break;
            case 'discount':
                $cost = 120;
                $value = 10.00;
                $name = 'Descuento Especial ($10)';
                $prefix = 'DESC10';
                break;
        }

        if ($user->points < $cost) {
            return redirect()->back()->withErrors(['points' => 'No tienes suficientes puntos para realizar este canje.']);
        }

        try {
            DB::beginTransaction();

            // 1. Restar puntos del usuario
            $user->decrement('points', $cost);

            // 2. Registrar transacción
            PointTransaction::create([
                'user_id' => $user->id,
                'type' => 'spent',
                'points' => $cost,
                'description' => "Canje de puntos por: {$name}"
            ]);

            // 3. Crear código de promoción único
            $code = 'REC-' . $prefix . '-' . Str::upper(Str::random(6));
            Promotion::create([
                'code' => $code,
                'type' => 'fixed',
                'value' => $value,
                'expires_at' => now()->addDays(30)
            ]);

            DB::commit();

            return redirect()->back()->with('message', "¡Canje exitoso! Usa el código de cupón en tu carrito: {$code}");

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => 'Ocurrió un error al procesar el canje: ' . $e->getMessage()]);
        }
    }
}
