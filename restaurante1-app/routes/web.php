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
        // Enviar estadísticas reales a la vista del Dashboard
        $stats = [
            'daily_revenue' => \App\Models\Order::whereDate('created_at', today())->where('status', '!=', 'Cancelado')->sum('total'),
            'orders_to_grill' => \App\Models\Order::whereIn('status', ['Pendiente', 'En preparación'])->count(),
            'delivered_orders' => \App\Models\Order::where('status', 'Entregado')->count(),
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
});

require __DIR__.'/auth.php';
