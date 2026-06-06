<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\OrderController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Models\Product;

/*
|--------------------------------------------------------------------------
| Web Routes - QuickBite Express
|--------------------------------------------------------------------------
*/

// Redirección inicial: Si es admin va a dashboard, si es cliente o visita va al menú
Route::get('/', function () {
    if (auth()->check()) {
        if (auth()->user()->isAdmin()) {
            return redirect()->route('dashboard');
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
    return Inertia::render('AboutView');
})->name('public.about');

// Rutas protegidas para Clientes y Administradores (Autenticados)
Route::middleware(['auth'])->group(function () {
    // Pedidos
    Route::get('/orders', [OrderController::class, 'index'])->name('public.orders');
    Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    
    // Perfil de usuario
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Rutas Exclusivas para el Administrador (Protegidas por middleware de rol)
Route::middleware(['auth', 'admin'])->group(function () {
    // Dashboard principal
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

        $stats = [
            'orders_today' => \App\Models\Order::whereDate('created_at', today())->count(),
            'revenue_today' => (float) \App\Models\Order::whereDate('created_at', today())->where('status', 'Entregado')->sum('total'),
            'low_stock' => \App\Models\Product::where('stock', '<=', 3)->where('stock', '>', 0)->count(),
            'out_of_stock' => \App\Models\Product::where('stock', 0)->count(),
            'orders_to_grill' => \App\Models\Order::whereIn('status', ['Pendiente', 'En preparación'])->count(),
            'weekly_sales' => $weeklySales,
            'live_orders' => \App\Models\Order::with(['user', 'items.product'])->orderBy('created_at', 'desc')->take(10)->get()
        ];
        
        return Inertia::render('Dashboard', [
            'stats' => $stats
        ]);
    })->name('dashboard');

    // CRUD de Productos (completo)
    Route::resource('products', ProductController::class);

    // CRUD de Categorías (completo)
    Route::resource('categories', CategoryController::class)->only(['index', 'store', 'update', 'destroy']);

    // Actualizar estado del pedido desde el dashboard
    Route::patch('/orders/{order}/status', [OrderController::class, 'update'])->name('orders.updateStatus');

    // Generar reporte en PDF
    Route::get('/reports/pdf', [\App\Http\Controllers\ReportController::class, 'downloadPdf'])->name('reports.pdf');
});

require __DIR__.'/auth.php';
