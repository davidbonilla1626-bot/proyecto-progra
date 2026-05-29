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
            'total.required' => 'El total del pedido es obligatorio.'
        ]);

        try {
            DB::beginTransaction();

            // 1. Crear la orden principal
            $order = Order::create([
                'user_id' => Auth::id(),
                'status' => 'Pendiente',
                'total' => $request->total,
                'notes' => $request->notes
            ]);

            // 2. Crear los detalles de la orden (order_items)
            foreach ($request->items as $item) {
                $product = Product::findOrFail($item['product_id']);
                
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

        $order->update([
            'status' => $validated['status']
        ]);

        return redirect()->back()->with('message', 'Estado del pedido actualizado a "' . $validated['status'] . '"');
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
