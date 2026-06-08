<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Inertia\Inertia;

class TrackingController extends Controller
{
    /**
     * Muestra el estado del pedido para seguimiento en tiempo real.
     */
    public function show(string $orderNumber)
    {
        $order = Order::with(['items.product'])
            ->where('order_number', $orderNumber)
            ->first();

        if (!$order) {
            abort(404, 'Pedido no encontrado.');
        }

        // Estimación de tiempo restante
        $estimatedTime = '20 minutos';
        if ($order->status === 'Pendiente') {
            $estimatedTime = '25-30 minutos';
        } elseif ($order->status === 'En preparación') {
            $estimatedTime = '15-20 minutos';
        } elseif ($order->status === 'Listo para entrega') {
            $estimatedTime = '5-10 minutos';
        } elseif ($order->status === 'Entregado') {
            $estimatedTime = 'Entregado';
        } elseif ($order->status === 'Cancelado') {
            $estimatedTime = 'Cancelado';
        }

        return Inertia::render('TrackingView', [
            'order' => $order,
            'estimatedTime' => $estimatedTime
        ]);
    }
}
