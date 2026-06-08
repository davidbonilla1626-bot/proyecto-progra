<?php

namespace App\Http\Controllers;

use App\Models\ChatMessage;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class ChatController extends Controller
{
    /**
     * Muestra la bandeja de entrada o lista de chats.
     */
    public function index()
    {
        $user = Auth::user();

        if ($user->isStaff()) {
            // Personal ve pedidos activos que tengan mensajes
            $chats = Order::has('chatMessages')
                ->with(['user', 'chatMessages' => function ($q) {
                    $q->latest()->take(1);
                }])
                ->get()
                ->map(function ($order) {
                    $latest = $order->chatMessages->first();
                    $unreadCount = ChatMessage::where('order_id', $order->id)
                        ->where('sender_id', '!=', Auth::id())
                        ->where('is_read', false)
                        ->count();

                    return [
                        'order_id' => $order->id,
                        'order_number' => $order->order_number,
                        'client_name' => $order->user->name,
                        'latest_message' => $latest ? $latest->message : '',
                        'latest_time' => $latest ? $latest->created_at->diffForHumans() : '',
                        'unread_count' => $unreadCount
                    ];
                });

            return Inertia::render('ChatDashboard', [
                'chats' => $chats
            ]);
        } else {
            // Clientes ven sus propios pedidos recientes
            $orders = Order::where('user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->take(10)
                ->get()
                ->map(function ($order) {
                    $unreadCount = ChatMessage::where('order_id', $order->id)
                        ->where('sender_id', '!=', Auth::id())
                        ->where('is_read', false)
                        ->count();

                    return [
                        'id' => $order->id,
                        'order_number' => $order->order_number,
                        'status' => $order->status,
                        'total' => (float) $order->total,
                        'unread_count' => $unreadCount,
                        'created_at' => $order->created_at->format('d/m/Y H:i')
                    ];
                });

            return Inertia::render('ClientChatView', [
                'orders' => $orders
            ]);
        }
    }

    /**
     * Muestra los mensajes de un pedido específico.
     */
    public function orderChat(Order $order)
    {
        $user = Auth::user();

        // Control de acceso
        if (!$user->isStaff() && $order->user_id !== $user->id) {
            abort(403, 'No tienes permiso para ver este chat.');
        }

        // Marcar mensajes como leídos
        ChatMessage::where('order_id', $order->id)
            ->where('sender_id', '!=', $user->id)
            ->update(['is_read' => true]);

        $messages = ChatMessage::where('order_id', $order->id)
            ->with('sender')
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json([
            'order' => [
                'id' => $order->id,
                'order_number' => $order->order_number,
                'status' => $order->status,
                'client_name' => $order->user->name
            ],
            'messages' => $messages
        ]);
    }

    /**
     * Envía un mensaje nuevo en un chat.
     */
    public function store(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'message' => 'required|string|max:1000'
        ]);

        $order = Order::findOrFail($request->order_id);
        $user = Auth::user();

        // Control de acceso
        if (!$user->isStaff() && $order->user_id !== $user->id) {
            abort(403, 'No tienes permiso para chatear en este pedido.');
        }

        // Determinar destinatario
        $receiverId = null;
        if ($user->isStaff()) {
            // Si el personal responde, el destinatario es el cliente del pedido
            $receiverId = $order->user_id;
        } else {
            // Si el cliente envía, el destinatario es el primer admin/staff que responda (se deja null o se asocia al admin de turno)
            $receiverId = null; 
        }

        $message = ChatMessage::create([
            'order_id' => $order->id,
            'sender_id' => $user->id,
            'receiver_id' => $receiverId,
            'message' => $request->message,
            'is_read' => false
        ]);

        return response()->json([
            'success' => true,
            'message' => $message->load('sender')
        ]);
    }
}
