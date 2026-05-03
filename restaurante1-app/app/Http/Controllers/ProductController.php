<?php

namespace App\Http\Controllers;

use App\Models\Product; // Importamos el modelo de Producto
use App\Models\Category; // Importamos el modelo de Categoría
use Illuminate\Http\Request;
use Inertia\Inertia; // Necesario para renderizar vistas de Vue con datos de Laravel

class ProductController extends Controller
{
    /**
     * Muestra la lista de productos en el panel de administración.
     * Es la parte de "Lectura" del CRUD.
     */
    public function index()
    {
        return Inertia::render('Products/Index', [
            // Eager loading: Traemos el producto y su categoría para evitar múltiples consultas
            'products' => Product::with('category')->get() 
        ]);
    }

    /**
     * Muestra el formulario para crear un nuevo platillo.
     */
    public function create()
    {
        return Inertia::render('Products/Create', [
            // Enviamos todas las categorías para llenar el selector (dropdown) del formulario
            'categories' => Category::all() 
        ]);
    }

    /**
     * Procesa los datos enviados desde el formulario y los guarda en la BD.
     */
    public function store(Request $request)
    {
        // Validamos que los datos cumplan con lo requerido para evitar errores en la BD
        $validated = $request->validate([
            'name' => 'required|string|max:100', // Nombre obligatorio
            'description' => 'required|string',   // Descripción obligatoria
            'price' => 'required|numeric|min:0', // El precio no puede ser negativo
            'category_id' => 'required|exists:categories,id', // Debe ser una categoría real
            'image_path' => 'required|string'    // Ruta de la imagen (por ahora texto)
        ]);

        // Creamos el registro en la base de datos
        Product::create($validated);

        // Redirigimos al usuario a la lista de productos con un mensaje de éxito
        return redirect()->route('products.index')->with('message', 'Producto creado con éxito');
    }

    /**
     * Elimina un platillo específico.
     */
    public function destroy(Product $product)
    {
        $product->delete(); // Borra el registro de la tabla 'products'
        return redirect()->route('products.index'); // Recarga la lista
    }
}