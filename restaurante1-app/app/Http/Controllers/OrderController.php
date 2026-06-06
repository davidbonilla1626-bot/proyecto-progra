<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class OrderController extends Controller
{
    /**
     * Muestra el listado de pedidos.
     * Si es Admin, muestra todos los del sistema.
     * Si es Cliente, muestra solo los suyos.
     */
    public function index()
    {
        $user = Auth::user();
        
        if ($user->isAdmin()) {
            $orders = Order::with(['user', 'items.product'])->orderBy('created_at', 'desc')->get();
        } else {
            $orders = Order::with(['items.product'])->where('user_id', $user->id)->orderBy('created_at', 'desc')->get();
        }

        return Inertia::render('OrdersView', [
            'orders' => $orders,
            'isAdmin' => $user->isAdmin()
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
            'notes' => 'nullable|string|max:500'
        ], [
            'items.required' => 'El carrito no puede estar vacío.',
            'items.min' => 'El carrito debe contener al menos un producto.',
            'items.*.quantity.min' => 'La cantidad de productos debe ser mayor que 0.',
            'total.required' => 'El total del pedido es obligatorio.'
        ]);

        try {
            DB::beginTransaction();

            // Generación de número de orden con formato: ORD-2026-001
            $year = date('Y');
            $lastOrder = Order::where('order_number', 'like', "ORD-{$year}-%")->orderBy('id', 'desc')->first();
            if ($lastOrder) {
                $lastNum = (int) substr($lastOrder->order_number, -3);
                $nextNum = str_pad($lastNum + 1, 3, '0', STR_PAD_LEFT);
            } else {
                $nextNum = '001';
            }
            $orderNumber = "ORD-{$year}-{$nextNum}";

            // 1. Crear la orden principal
            $order = Order::create([
                'user_id' => Auth::id(),
                'order_number' => $orderNumber,
                'status' => 'Pendiente',
                'total' => $request->total,
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
                    'price' => $product->price // Guardamos el precio del producto en ese momento
                ]);
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
     * Actualiza el estado de un pedido (Solo Admin).
     */
    public function update(Request $request, Order $order)
    {
        // Doble verificación de seguridad en el controlador
        if (!Auth::user()->isAdmin()) {
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
        if (!$user->isAdmin() && $order->user_id !== $user->id) {
            abort(403, 'No tienes autorización para ver este pedido.');
        }

        $order->load(['user', 'items.product']);

        return response()->json($order);
    }
}
