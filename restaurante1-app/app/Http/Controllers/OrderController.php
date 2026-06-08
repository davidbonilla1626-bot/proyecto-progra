<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Rating;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Barryvdh\DomPDF\Facade\Pdf;

class OrderController extends Controller
{
    /**
     * Muestra el listado de pedidos.
     * Si es Personal (Admin u Cocinero), muestra todos los del sistema.
     * Si es Cliente, muestra solo los suyos.
     */
    public function index()
    {
        $user = Auth::user();
        
        if ($user->isStaff()) {
            $orders = Order::with(['user', 'items.product', 'rating'])->orderBy('created_at', 'desc')->get();
        } else {
            $orders = Order::with(['items.product', 'rating'])->where('user_id', $user->id)->orderBy('created_at', 'desc')->get();
        }

        return Inertia::render('OrdersView', [
            'orders' => $orders,
            'isAdmin' => $user->isAdmin(),
            'isStaff' => $user->isStaff()
        ]);
    }

    /**
     * Guarda un nuevo pedido en la base de datos.
     */
    public function store(Request $request)
    {
        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'total' => 'required|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
            'promotion_code' => 'nullable|string|max:50',
            'notes' => 'nullable|string|max:500'
        ], [
            'items.required' => 'El carrito no puede estar vacío.',
            'items.min' => 'El carrito debe contener al menos un producto.',
            'items.*.quantity.min' => 'La cantidad de productos debe ser mayor que 0.',
            'total.required' => 'El total del pedido es obligatorio.'
        ]);

        try {
            DB::beginTransaction();

            // 0. Validación de horario del restaurante
            $openingTime = \App\Models\Setting::getVal('opening_time', '08:00');
            $closingTime = \App\Models\Setting::getVal('closing_time', '22:00');
            $currentTime = now()->format('H:i');
            if ($currentTime < $openingTime || $currentTime > $closingTime) {
                throw new \Exception("El restaurante se encuentra fuera de horario de atención (Horario: {$openingTime} a {$closingTime}).");
            }

            // Generación de número de orden con formato: ORD-2026-0001
            $year = date('Y');
            $lastOrder = Order::where('order_number', 'like', "ORD-{$year}-%")->orderBy('id', 'desc')->first();
            if ($lastOrder) {
                $lastNum = (int) substr($lastOrder->order_number, -4);
                $nextNum = str_pad($lastNum + 1, 4, '0', STR_PAD_LEFT);
            } else {
                $nextNum = '0001';
            }
            $orderNumber = "ORD-{$year}-{$nextNum}";

            // 1. Crear la orden principal
            $order = Order::create([
                'user_id' => Auth::id(),
                'order_number' => $orderNumber,
                'status' => 'Pendiente',
                'total' => $request->total,
                'discount' => $request->discount ?? 0.00,
                'promotion_code' => $request->promotion_code,
                'notes' => $request->notes
            ]);

            // 2. Crear los detalles de la orden (order_items) y descontar stock
            foreach ($request->items as $item) {
                $product = Product::findOrFail($item['product_id']);
                
                // Validación de stock disponible
                if ($product->stock < $item['quantity']) {
                    throw new \Exception("No hay suficiente stock para {$product->name}. Stock disponible: {$product->stock}");
                }

                // Reducción automática de stock
                $product->decrement('stock', $item['quantity']);
                
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'price' => $item['price'] ?? $product->price, // Guardamos precio personalizado o precio base
                    'customizations' => $item['customizations'] ?? null
                ]);
            }

            // 3. Sistema de Puntos y Fidelización
            $user = Auth::user();
            $pointsEarned = 0;
            if ($user) {
                $pointsEarned = (int) $order->total;
                if ($pointsEarned > 0) {
                    $user->increment('points', $pointsEarned);
                    \App\Models\PointTransaction::create([
                        'user_id' => $user->id,
                        'type' => 'earned',
                        'points' => $pointsEarned,
                        'description' => "Puntos acumulados por pedido #{$order->order_number}"
                    ]);
                }
            }

            // Registrar Auditoría
            \App\Models\AuditLog::log("Confirmó el pedido #{$order->order_number} por un total de \${$order->total}");

            // 4. Enviar correo de confirmación de pedido
            if ($user) {
                try {
                    \Illuminate\Support\Facades\Mail::to($user->email)->send(new \App\Mail\QuickBiteMail(
                        "¡Pedido Confirmado! #{$order->order_number}",
                        $user->name,
                        "Tu pedido #{$order->order_number} ha sido recibido y ya se está preparando en la cocina.\n\nTotal del Pedido: \${$order->total}\nPuntos acumulados: {$pointsEarned} puntos.\n\n¡Muchas gracias por tu compra!",
                        route('orders.tracking', $order->order_number),
                        'Rastrear mi pedido'
                    ));
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error("Error enviando correo de confirmación de pedido: " . $e->getMessage());
                }
            }

            DB::commit();

            return redirect()->route('public.orders')->with('message', '¡Pedido confirmado con éxito! Tu comida está en camino.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors([
                'error' => 'Hubo un error al procesar tu pedido: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Actualiza el estado de un pedido (Solo Personal de Cocina o Admin).
     */
    public function update(Request $request, Order $order)
    {
        // Doble verificación de seguridad en el controlador
        if (!Auth::user()->isStaff()) {
            return redirect()->back()->with('error', 'No tienes autorización para realizar esta acción.');
        }

        $validated = $request->validate([
            'status' => 'required|in:Pendiente,En preparación,Listo para entrega,Entregado,Cancelado'
        ], [
            'status.required' => 'El estado es obligatorio.',
            'status.in' => 'El estado seleccionado no es válido.'
        ]);

        try {
            DB::beginTransaction();

            $oldStatus = $order->status;
            $order->update([
                'status' => $validated['status']
            ]);

            // Auditoría
            \App\Models\AuditLog::log("Cambió el estado del pedido #{$order->order_number} de '{$oldStatus}' a '{$validated['status']}'");

            // Enviar correos según el estado nuevo
            $customer = $order->user;
            if ($customer && $oldStatus !== $validated['status']) {
                try {
                    if ($validated['status'] === 'Listo para entrega') {
                        \Illuminate\Support\Facades\Mail::to($customer->email)->send(new \App\Mail\QuickBiteMail(
                            "¡Tu pedido está listo! - #{$order->order_number}",
                            $customer->name,
                            "Tu pedido #{$order->order_number} ha terminado de prepararse y está LISTO para ser retirado o entregado.\n\n¡Que lo disfrutes!",
                            route('orders.tracking', $order->order_number),
                            'Ver Pedido'
                        ));
                    } elseif ($validated['status'] === 'Entregado') {
                        \Illuminate\Support\Facades\Mail::to($customer->email)->send(new \App\Mail\QuickBiteMail(
                            "¡Pedido Entregado! - #{$order->order_number}",
                            $customer->name,
                            "Tu pedido #{$order->order_number} ha sido marcado como ENTREGADO.\n\nMuchas gracias por preferir QuickBite Express. Te invitamos a dejar tu calificación y comentario sobre el servicio.",
                            route('public.orders'),
                            'Dejar Calificación'
                        ));
                    }
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error("Error enviando correo de cambio de estado de pedido: " . $e->getMessage());
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
                        throw new \Exception("No se puede reactivar el pedido. El producto '{$item->product->name}' no tiene suficiente stock.");
                    }
                    $item->product->decrement('stock', $item->quantity);
                }
            }

            DB::commit();

            return redirect()->back()->with('message', 'Estado del pedido actualizado a "' . $validated['status'] . '"');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors([
                'error' => 'Error al cambiar el estado: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Ver el detalle de un pedido específico.
     */
    public function show(Order $order)
    {
        $user = Auth::user();
        
        // Un usuario común solo puede ver sus propios pedidos
        if (!$user->isStaff() && $order->user_id !== $user->id) {
            abort(403, 'No tienes autorización para ver este pedido.');
        }

        $order->load(['user', 'items.product', 'rating']);

        return response()->json($order);
    }

    /**
     * Mostrar la vista del ticket para imprimir desde el navegador.
     */
    public function showTicket(Order $order)
    {
        $user = Auth::user();
        if (!$user->isStaff() && $order->user_id !== $user->id) {
            abort(403, 'No tienes autorización para ver este ticket.');
        }

        $order->load(['user', 'items.product']);
        return view('reports.ticket', compact('order'));
    }

    /**
     * Descargar el ticket del pedido en PDF.
     */
    public function downloadTicketPdf(Order $order)
    {
        $user = Auth::user();
        if (!$user->isStaff() && $order->user_id !== $user->id) {
            abort(403, 'No tienes autorización para descargar este ticket.');
        }

        $order->load(['user', 'items.product']);
        
        $pdf = Pdf::loadView('reports.ticket', compact('order'));
        return $pdf->download("Ticket_{$order->order_number}.pdf");
    }

    /**
     * Guarda la calificación de la experiencia de un cliente.
     */
    public function rate(Request $request, Order $order)
    {
        $user = Auth::user();
        
        if ($order->user_id !== $user->id) {
            abort(403, 'Solo puedes calificar tus propios pedidos.');
        }

        if ($order->status !== 'Entregado') {
            return redirect()->back()->with('error', 'Solo puedes calificar pedidos entregados.');
        }

        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:500'
        ]);

        Rating::updateOrCreate(
            ['order_id' => $order->id],
            [
                'user_id' => $user->id,
                'rating' => $validated['rating'],
                'comment' => $validated['comment']
            ]
        );

        return redirect()->back()->with('message', '¡Gracias por calificar tu experiencia!');
    }
}
