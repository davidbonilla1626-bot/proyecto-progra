<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProductController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Models\Product;



Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

// Define que solo usuarios autenticados (logeados) puedan administrar
Route::middleware(['auth', 'verified'])->group(function () {
    
    // Ruta para el panel principal
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');

    // Route::resource genera automáticamente las 7 rutas del CRUD 
    // (index, create, store, show, edit, update, destroy)
    Route::resource('products', ProductController::class);
});


/*
|--------------------------------------------------------------------------
| Web Routes - QuickBite Express
|--------------------------------------------------------------------------
*/

Route::get('/menu', function () {
    return Inertia::render('Menu', [
        'products' => [
            // --- HAMBURGUESAS ---
            [
                'id' => 1, 
                'category' => 'Hamburguesas', 
                'name' => 'Hamburguesa Gran Megabyte', 
                'description' => 'La clásica de la casa: doble carne de res, queso cheddar fundido y nuestra salsa secreta de 8 bits.', 
                'price' => 14.99, 
                'image' => 'https://images.unsplash.com/photo-1568901346375-23c9450c58cd?w=500'
            ],
            [
                'id' => 2, 
                'category' => 'Hamburguesas', 
                'name' => 'Cyber-Bacon Pro Max', 
                'description' => 'Hamburguesa premium con tiras de bacon crujiente, cebolla caramelizada y doble ración de memoria.', 
                'price' => 15.50, 
                'image' => 'https://images.unsplash.com/photo-1585238341267-1cfec2046a55?w=500'
            ],
            [
                'id' => 7, 
                'category' => 'Hamburguesas', 
                'name' => 'Hamburguesa Glitch Veggie', 
                'description' => 'El error que salió bien: medallón de garbanzos, aguacate fresco y hummus de pimientos.', 
                'price' => 13.00, 
                'image' => 'https://images.unsplash.com/photo-1512152272829-e3139592d56f?w=500'
            ],
            [
                'id' => 9, 
                'category' => 'Hamburguesas', 
                'name' => 'La Torre Superusuario (Root)', 
                'description' => 'Triple carne, huevo frito, aros de cebolla y acceso total a los mejores ingredientes.', 
                'price' => 18.00, 
                'image' => 'https://images.unsplash.com/photo-1596662951482-0c4ba74a6df6?w=500'
            ],

            // --- HOT DOGS ---
            [
                'id' => 3, 
                'category' => 'Hot Dogs', 
                'name' => 'Hot Dog Supersónico 5G', 
                'description' => 'Salchicha jumbo de 30cm, mostaza artesanal y un ancho de banda de sabores con nuestro relish especial.', 
                'price' => 9.50, 
                'image' => 'https://images.unsplash.com/photo-1612392062631-94dd858cba88?w=500'
            ],
            [
                'id' => 4, 
                'category' => 'Hot Dogs', 
                'name' => 'Nitro Chilli Dog', 
                'description' => 'Cargado con chilli con carne picante, jalapeños y una latencia mínima entre mordisco y mordisco.', 
                'price' => 11.00, 
                'image' => 'https://images.unsplash.com/photo-1619740455993-9e47519a8844?w=500'
            ],

            // --- POLLO ---
            [
                'id' => 5, 
                'category' => 'Pollo', 
                'name' => 'Alitas Terabyte BBQ', 
                'description' => '10 alitas de pollo bañadas en salsa BBQ coreana con un toque ahumado de alta densidad.', 
                'price' => 12.99, 
                'image' => 'https://images.unsplash.com/photo-1567620832903-9fc6debc209f?w=500'
            ],
            [
                'id' => 6, 
                'category' => 'Pollo', 
                'name' => 'Sándwich Infinite Loop', 
                'description' => 'Pollo frito extra crujiente, lechuga fresca y mayonesa picante en un ciclo de sabor sin fin.', 
                'price' => 13.50, 
                'image' => 'https://images.unsplash.com/photo-1606755962773-53240004f14a?w=500'
            ],

            // --- ENSALADAS ---
            [
                'id' => 8, 
                'category' => 'Ensaladas', 
                'name' => 'Ensalada Clean Code', 
                'description' => 'Mix de verdes orgánicos, quinoa, arándanos y una vinagreta cítrica libre de errores.', 
                'price' => 10.99, 
                'image' => 'https://images.unsplash.com/photo-1512621776951-a57141f2eefd?w=500'
            ],

            // --- ACOMPAÑAMIENTOS ---
            [
                'id' => 10, 
                'category' => 'Acompañamientos', 
                'name' => 'Papas Overclocked', 
                'description' => 'Papas fritas con potencia extra: queso fundido, bacon bits y cebollín fresco.', 
                'price' => 6.99, 
                'image' => 'https://images.unsplash.com/photo-1573080496219-bb080dd4f877?w=500'
            ],
            [
                'id' => 11, 
                'category' => 'Acompañamientos', 
                'name' => 'Nuggets Cuánticos', 
                'description' => '6 piezas de pollo crujiente que pueden estar en tu boca y en el plato al mismo tiempo.', 
                'price' => 7.50, 
                'image' => 'https://images.unsplash.com/photo-1562967914-608f82629710?w=500'
            ],
            [
                'id' => 12, 
                'category' => 'Acompañamientos', 
                'name' => 'Aros de Token Ring', 
                'description' => 'Aros de cebolla tempurizados con una topología circular perfecta y crujiente.', 
                'price' => 5.50, 
                'image' => 'https://images.unsplash.com/photo-1639024471283-03518883512d?w=500'
            ],

            // --- BEBIDAS ---
            [
                'id' => 101, 
                'category' => 'Bebidas', 
                'name' => 'Turbo Batido Choco-Script', 
                'description' => 'Batido espeso de chocolate belga con trozos de brownie.', 
                'price' => 7.25, 
                'image' => 'https://images.unsplash.com/photo-1572490122747-3968b75cc699?w=500'
            ],
            [
                'id' => 102, 
                'category' => 'Bebidas', 
                'name' => 'Soda Azul Eléctrico', 
                'description' => 'Infusión refrescante de arándano azul con burbujas de alta velocidad.', 
                'price' => 4.50, 
                'image' => 'https://images.unsplash.com/photo-1513558161293-cdaf765ed2fd?w=500'
            ],
            [
                'id' => 103, 
                'category' => 'Bebidas', 
                'name' => 'Café Helado Java Runtime', 
                'description' => 'Café frío de grano seleccionado con una capa de espuma de vainilla.', 
                'price' => 5.00, 
                'image' => 'https://images.unsplash.com/photo-1517701604599-bb29b565090c?w=500'
            ],
            [
                'id' => 104, 
                'category' => 'Bebidas', 
                'name' => 'Limonada Neón', 
                'description' => 'Limonada natural de fresa con un color vibrante fuera de la gama RGB.', 
                'price' => 4.00, 
                'image' => 'https://images.unsplash.com/photo-1523362628745-0c100150b504?w=500'
            ],
            [
                'id' => 105, 
                'category' => 'Bebidas', 
                'name' => 'Jugo Multifrutas V-8', 
                'description' => 'Compilado de frutas tropicales frescas para un boost de energía.', 
                'price' => 5.50, 
                'image' => 'https://images.unsplash.com/photo-1613478223719-2ab802602423?w=500'
            ],
            [
                'id' => 106, 
                'category' => 'Bebidas', 
                'name' => 'Cola Clásica Legacy', 
                'description' => 'La receta de siempre, compatible con todos tus sistemas.', 
                'price' => 3.00, 
                'image' => 'https://images.unsplash.com/photo-1581006852262-e4307cf6283a?w=500'
            ],
            [
                'id' => 107, 
                'category' => 'Bebidas', 
                'name' => 'Nitro Cold Brew', 
                'description' => 'Café extraído en frío durante 24 horas y servido con nitrógeno.', 
                'price' => 6.00, 
                'image' => 'https://images.unsplash.com/photo-1495474472287-4d71bcdd2085?w=500'
            ],
            [
                'id' => 108, 
                'category' => 'Bebidas', 
                'name' => 'Té Matcha Latte', 
                'description' => 'Té verde ceremonial con leche de avena y una textura suave.', 
                'price' => 6.50, 
                'image' => 'https://images.unsplash.com/photo-1515823064-d6e0c04616a7?w=500'
            ],
            [
                'id' => 109, 
                'category' => 'Bebidas', 
                'name' => 'Ráfaga de Ginger Ale', 
                'description' => 'Jengibre natural muy refrescante con un toque de menta.', 
                'price' => 4.25, 
                'image' => 'https://images.unsplash.com/photo-1513558161293-cdaf765ed2fd?w=500'
            ],
            [
                'id' => 110, 
                'category' => 'Bebidas', 
                'name' => 'Smoothie de Mango y Mora', 
                'description' => 'Mango y Mora natural recién licuado con una textura optimizada.', 
                'price' => 7.00, 
                'image' => 'https://images.unsplash.com/photo-1481671703460-040cb8a2d909?w=500'
            ],
            [
                'id' => 111, 
                'category' => 'Bebidas', 
                'name' => 'Cerveza de Raíz Root-Beer', 
                'description' => 'Bebida artesanal dulce con extractos de raíces y vainilla.', 
                'price' => 4.50, 
                'image' => 'https://images.unsplash.com/photo-1556679343-c7306c1976bc?w=500'
            ],
            [
                'id' => 112, 
                'category' => 'Bebidas', 
                'name' => 'Cerveza Artesanal IP-A', 
                'description' => 'Cerveza local con un amargor intenso y notas cítricas (Solo +18).', 
                'price' => 8.00, 
                'image' => 'https://images.unsplash.com/photo-1535958636474-b021ee887b13?w=500'
            ],
        ]
    ]);
});  

// RUTA PÚBLICA: Cualquier cliente puede entrar aquí sin registrarse
// Al final de tu archivo web.php

Route::get('/menu', function () {
    return Inertia::render('MenuView', [
        'products' => Product::with('category')->get()
    ]);
})->name('public.menu');

Route::get('/cart', function () {
    return Inertia::render('CartView');
})->name('public.cart');

// NUEVA RUTA: About (Acerca de)
Route::get('/about', function () {
    return Inertia::render('AboutView');
})->name('public.about');

// NUEVA RUTA: Orders (Pedidos)
// Usamos middleware auth para los pedidos, ya que requiere estar logeado para ver el historial
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/orders', function () {
        // En una app real, aquí se buscarían los pedidos del usuario o todos si es admin.
        // Por ahora pasaremos el usuario actual para decidir qué vista mostrar en Vue.
        return Inertia::render('OrdersView');
    })->name('public.orders');
});

require __DIR__.'/auth.php';
