<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class KitchenController extends Controller
{
    /**
     * Muestra el panel de cocina con columnas de estados.
     */
    public function index()
    {
        $orders = Order::with(['user', 'items.product'])
            ->whereIn('status', ['Pendiente', 'En preparación', 'Listo para entrega', 'Entregado'])
            ->whereDate('created_at', today()) // Mostrar pedidos de hoy en cocina
            ->orderBy('updated_at', 'asc')
            ->get();

        return Inertia::render('KitchenDashboard', [
            'orders' => $orders
        ]);
    }

    /**
     * Actualiza el estado de un pedido desde el panel de cocina.
     */
    public function updateStatus(Request $request, Order $order)
    {
        $validated = $request->validate([
            'status' => 'required|in:Pendiente,En preparación,Listo para entrega,Entregado,Cancelado'
        ]);

        try {
            DB::beginTransaction();

            $oldStatus = $order->status;
            $order->update([
                'status' => $validated['status']
            ]);

            // Auditoría
            \App\Models\AuditLog::log("Cocina cambió el estado del pedido #{$order->order_number} de '{$oldStatus}' a '{$validated['status']}'");

            // Enviar correos según el estado nuevo
            $customer = $order->user;
            if ($customer && $oldStatus !== $validated['status']) {
                try {
                    if ($validated['status'] === 'Listo para entrega') {
                        \Illuminate\Support\Facades\Mail::to($customer->email)->send(new \App\Mail\QuickBiteMail(
                            "¡Tu pedido está listo! - #{$order->order_number}",
                            $customer->name,
                            "Tu pedido #{$order->order_number} ha terminado de prepararse en la cocina y está LISTO para entrega.\n\n¡Buen provecho!",
                            route('orders.tracking', $order->order_number),
                            'Ver Pedido'
                        ));
                    } elseif ($validated['status'] === 'Entregado') {
                        \Illuminate\Support\Facades\Mail::to($customer->email)->send(new \App\Mail\QuickBiteMail(
                            "¡Pedido Entregado! - #{$order->order_number}",
                            $customer->name,
                            "Tu pedido #{$order->order_number} ha sido entregado.\n\nMuchas gracias por comprar en QuickBite Express. Te invitamos a dejar tu calificación y opinión.",
                            route('public.orders'),
                            'Dejar Calificación'
                        ));
                    }
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error("Error enviando correo de cambio de estado de pedido desde cocina: " . $e->getMessage());
                }
            }

            // Si cambia a Cancelado y antes no lo estaba, devolvemos el stock
            if ($validated['status'] === 'Cancelado' && $oldStatus !== 'Cancelado') {
                foreach ($order->items as $item) {
                    $item->product->increment('stock', $item->quantity);
                }
            }
            // Si cambia de Cancelado a otra cosa, restamos el stock de nuevo (validando que haya suficiente)
            elseif ($oldStatus === 'Cancelado' && $validated['status'] !== 'Cancelado') {
                foreach ($order->items as $item) {
                    if ($item->product->stock < $item->quantity) {
                        throw new \Exception("No hay suficiente stock disponible para reactivar.");
                    }
                    $item->product->decrement('stock', $item->quantity);
                }
            }

            DB::commit();

            return redirect()->back()->with('message', "Pedido {$order->order_number} cambiado a {$validated['status']}.");

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors([
                'error' => 'Error al actualizar cocina: ' . $e->getMessage()
            ]);
        }
    }
}
