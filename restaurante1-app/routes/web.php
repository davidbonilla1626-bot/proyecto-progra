<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\KitchenController;
use App\Http\Controllers\PromotionController;
use App\Http\Controllers\TrackingController;
use App\Http\Controllers\RewardController;
use App\Http\Controllers\AuditController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\RankingController;
use App\Http\Controllers\ChatController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Models\Product;

/*
|--------------------------------------------------------------------------
| Web Routes - QuickBite Express
|--------------------------------------------------------------------------
*/

// Redirección inicial: Si es admin va a dashboard, si es cocina va a cocina, si es cliente va al menú
Route::get('/', function () {
    if (auth()->check()) {
        if (auth()->user()->isAdmin()) {
            return redirect()->route('dashboard');
        }
        if (auth()->user()->isEmployee()) {
            return redirect()->route('kitchen.index');
        }
        return redirect()->route('public.menu');
    }
    return redirect()->route('public.menu');
});

// Rutas Públicas de Visualización
Route::get('/menu', function () {
    return Inertia::render('MenuView', [
        'products' => Product::with('category')->get(),
        'categories' => \App\Models\Category::all(),
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
    ]);
})->name('public.menu');

Route::get('/cart', function () {
    return Inertia::render('CartView');
})->name('public.cart');

Route::get('/about', function () {
    // Calculamos el promedio de calificaciones para mostrar en el "Nosotros" o sobre el restaurante
    $avgRating = \App\Models\Rating::avg('rating') ?: 5.0;
    return Inertia::render('AboutView', [
        'averageRating' => round($avgRating, 1),
        'totalRatings' => \App\Models\Rating::count()
    ]);
})->name('public.about');

// Ruta Pública de Ubicación del Restaurante
Route::get('/location', function () {
    return Inertia::render('LocationView');
})->name('public.location');

// Ruta Pública de Seguimiento de Pedidos
Route::get('/tracking/{order_number}', [TrackingController::class, 'show'])->name('orders.tracking');

// Rutas protegidas para Clientes y Personal (Autenticados)
Route::middleware(['auth'])->group(function () {
    // Pedidos e Historial
    Route::get('/orders', [OrderController::class, 'index'])->name('public.orders');
    Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    
    // Ticket y PDF de Pedidos
    Route::get('/orders/{order}/ticket', [OrderController::class, 'showTicket'])->name('orders.ticket');
    Route::get('/orders/{order}/ticket/pdf', [OrderController::class, 'downloadTicketPdf'])->name('orders.ticketPdf');
    
    // Calificar Pedido
    Route::post('/orders/{order}/rate', [OrderController::class, 'rate'])->name('orders.rate');

    // Aplicar Promociones
    Route::post('/promotions/apply', [PromotionController::class, 'apply'])->name('promotions.apply');

    // Sistema de Puntos y Fidelización (Recompensas)
    Route::get('/rewards', [RewardController::class, 'index'])->name('rewards.index');
    Route::post('/rewards/redeem', [RewardController::class, 'redeem'])->name('rewards.redeem');

    // Chat Interno Cliente-Restaurante
    Route::get('/chat', [ChatController::class, 'index'])->name('chat.index');
    Route::post('/chat', [ChatController::class, 'store'])->name('chat.store');
    Route::get('/chat/order/{order}', [ChatController::class, 'orderChat'])->name('chat.orderChat');
    
    // Perfil de usuario
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Rutas para el Personal de Cocina y Administradores (Staff Middleware)
Route::middleware(['auth', 'staff'])->group(function () {
    Route::get('/kitchen', [KitchenController::class, 'index'])->name('kitchen.index');
    Route::patch('/kitchen/orders/{order}/status', [KitchenController::class, 'updateStatus'])->name('kitchen.updateStatus');
});

// Rutas Exclusivas para el Administrador (Protegidas por middleware de rol admin)
Route::middleware(['auth', 'admin'])->group(function () {
    // Dashboard principal con Analíticas Avanzadas
    Route::get('/dashboard', function () {
        $weeklySales = collect();
        for ($i = 6; $i >= 0; $i--) {
            $date = today()->subDays($i);
            $revenue = \App\Models\Order::whereDate('created_at', $date)
                ->where('status', 'Entregado')
                ->sum('total');
            $weeklySales->push([
                'date' => $date->format('d/m'),
                'revenue' => (float) $revenue
            ]);
        }

        $topProducts = \App\Models\OrderItem::select('product_id', \Illuminate\Support\Facades\DB::raw('SUM(quantity) as total_sold'), \Illuminate\Support\Facades\DB::raw('SUM(quantity * price) as total_revenue'))
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.status', 'Entregado')
            ->groupBy('product_id')
            ->orderBy('total_sold', 'desc')
            ->take(5)
            ->with('product')
            ->get();

        $recentRatings = \App\Models\Rating::with(['user', 'order'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // 1. Ventas Semanales (Últimos 7 días)
        $revenueWeekly = (float) \App\Models\Order::where('created_at', '>=', now()->subDays(7))
            ->where('status', 'Entregado')
            ->sum('total');

        // 2. Hora con más pedidos (Con soporte SQLite y MySQL)
        if (config('database.default') === 'sqlite') {
            $busyHourRow = \App\Models\Order::select(\Illuminate\Support\Facades\DB::raw('strftime("%H", created_at) as hour'), \Illuminate\Support\Facades\DB::raw('count(*) as count'))
                ->groupBy('hour')
                ->orderBy('count', 'desc')
                ->first();
        } else {
            $busyHourRow = \App\Models\Order::select(\Illuminate\Support\Facades\DB::raw('HOUR(created_at) as hour'), \Illuminate\Support\Facades\DB::raw('count(*) as count'))
                ->groupBy('hour')
                ->orderBy('count', 'desc')
                ->first();
        }
        $busyHour = $busyHourRow ? $busyHourRow->hour . ':00' : 'N/A';

        // 3. Día con más ventas (Soporte SQLite y MySQL)
        if (config('database.default') === 'sqlite') {
            $busyDayRow = \App\Models\Order::select(\Illuminate\Support\Facades\DB::raw('strftime("%w", created_at) as day'), \Illuminate\Support\Facades\DB::raw('count(*) as count'))
                ->groupBy('day')
                ->orderBy('count', 'desc')
                ->first();
            $days = ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];
            $busyDay = $busyDayRow ? $days[(int)$busyDayRow->day] : 'N/A';
        } else {
            $busyDayRow = \App\Models\Order::select(\Illuminate\Support\Facades\DB::raw('DAYOFWEEK(created_at) as day'), \Illuminate\Support\Facades\DB::raw('count(*) as count'))
                ->groupBy('day')
                ->orderBy('count', 'desc')
                ->first();
            $days = ['', 'Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];
            $busyDay = $busyDayRow ? $days[(int)$busyDayRow->day] : 'N/A';
        }

        // 4. Promedio por pedido
        $orderAverage = (float) \App\Models\Order::where('status', 'Entregado')->avg('total') ?: 0.00;

        // 5. Clientes más activos (Top 5)
        $activeClients = \App\Models\User::where('role', 'user')
            ->withCount(['orders' => function ($q) {
                $q->where('status', 'Entregado');
            }])
            ->orderBy('orders_count', 'desc')
            ->take(5)
            ->get()
            ->map(function ($client) {
                return [
                    'id' => $client->id,
                    'name' => $client->name,
                    'email' => $client->email,
                    'orders_count' => $client->orders_count,
                    'total_spent' => (float) $client->orders()->where('status', 'Entregado')->sum('total'),
                ];
            });

        // Horas actuales configuradas
        $openingTime = \App\Models\Setting::getVal('opening_time', '08:00');
        $closingTime = \App\Models\Setting::getVal('closing_time', '22:00');

        $stats = [
            'orders_today' => \App\Models\Order::whereDate('created_at', today())->count(),
            'revenue_today' => (float) \App\Models\Order::whereDate('created_at', today())->where('status', 'Entregado')->sum('total'),
            'revenue_month' => (float) \App\Models\Order::whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->where('status', 'Entregado')->sum('total'),
            'orders_pending' => \App\Models\Order::where('status', 'Pendiente')->count(),
            'registered_users' => \App\Models\User::where('role', 'user')->count(),
            'registered_products' => \App\Models\Product::count(),
            'low_stock' => \App\Models\Product::where('stock', '<=', 5)->where('stock', '>', 0)->count(),
            'out_of_stock' => \App\Models\Product::where('stock', 0)->count(),
            'orders_to_grill' => \App\Models\Order::whereIn('status', ['Pendiente', 'En preparación'])->count(),
            'weekly_sales' => $weeklySales,
            'top_products' => $topProducts,
            'recent_ratings' => $recentRatings,
            'live_orders' => \App\Models\Order::with(['user', 'items.product'])->orderBy('created_at', 'desc')->take(10)->get(),
            // Analíticas Avanzadas
            'revenue_weekly' => $revenueWeekly,
            'busy_hour' => $busyHour,
            'busy_day' => $busyDay,
            'order_average' => round($orderAverage, 2),
            'active_clients' => $activeClients,
            'opening_time' => $openingTime,
            'closing_time' => $closingTime,
        ];
        
        return Inertia::render('Dashboard', [
            'stats' => $stats
        ]);
    })->name('dashboard');

    // CRUD de Productos (completo)
    Route::resource('products', ProductController::class);

    // CRUD de Categorías (completo)
    Route::resource('categories', CategoryController::class)->only(['index', 'store', 'update', 'destroy']);

    // CRUD de Usuarios
    Route::resource('users', UserController::class);

    // Actualizar estado del pedido desde el dashboard
    Route::patch('/orders/{order}/status', [OrderController::class, 'update'])->name('orders.updateStatus');

    // Historial de Auditoría
    Route::get('/audit-logs', [AuditController::class, 'index'])->name('audit.index');

    // Horarios del Restaurante
    Route::patch('/settings/hours', [SettingsController::class, 'updateHours'])->name('settings.hours');

    // Ranking de Clientes
    Route::get('/client-ranking', [RankingController::class, 'index'])->name('ranking.index');

    // Generar reporte en PDF
    Route::get('/reports/pdf', [\App\Http\Controllers\ReportController::class, 'downloadPdf'])->name('reports.pdf');
});

require __DIR__.'/auth.php';
