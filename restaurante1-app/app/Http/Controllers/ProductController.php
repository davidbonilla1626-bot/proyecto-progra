<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class ProductController extends Controller
{
    /**
     * Muestra la lista de productos en el panel de administración.
     */
    public function index()
    {
        // Traemos el producto y su categoría con Eager Loading
        $products = Product::with('category')->get();
        $categories = Category::all();

        return Inertia::render('Products/Index', [
            'products' => $products,
            'categories' => $categories
        ]);
    }

    /**
     * Muestra el formulario para crear un nuevo producto.
     */
    public function create()
    {
        return Inertia::render('Products/Create', [
            'categories' => Category::all()
        ]);
    }

    /**
     * Guarda un producto en la base de datos (Solo Admin).
     */
    public function store(Request $request)
    {
        // Doble verificación en el controlador
        if (!Auth::user()->isAdmin()) {
            return redirect()->back()->with('error', 'Acceso denegado. Se requieren permisos de administrador.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'required|string|max:1000',
            'price' => 'required|numeric|min:0.01',
            'category_id' => 'required|exists:categories,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'image_url' => 'nullable|string|url'
        ], [
            'name.required' => 'El nombre del producto es obligatorio.',
            'description.required' => 'La descripción es obligatoria.',
            'price.required' => 'El precio es obligatorio.',
            'price.numeric' => 'El precio debe ser un número.',
            'price.min' => 'El precio debe ser mayor a 0.',
            'category_id.required' => 'La categoría es obligatoria.',
            'category_id.exists' => 'La categoría seleccionada no existe.'
        ]);

        $imagePath = 'https://images.unsplash.com/photo-1568901346375-23c9450c58cd?w=500'; // Default

        // Subir archivo de imagen si existe
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('products', 'public');
            $imagePath = '/storage/' . $path;
        } elseif (!empty($validated['image_url'])) {
            $imagePath = $validated['image_url'];
        }

        Product::create([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'price' => $validated['price'],
            'category_id' => $validated['category_id'],
            'image' => $imagePath,
            'image_path' => $imagePath
        ]);

        return redirect()->route('products.index')->with('message', '¡Producto creado con éxito!');
    }

    /**
     * Muestra el formulario para editar un producto.
     */
    public function edit(Product $product)
    {
        return Inertia::render('Products/Edit', [
            'product' => $product,
            'categories' => Category::all()
        ]);
    }

    /**
     * Actualiza un producto específico (Solo Admin).
     */
    public function update(Request $request, Product $product)
    {
        if (!Auth::user()->isAdmin()) {
            return redirect()->back()->with('error', 'Acceso denegado. Se requieren permisos de administrador.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'required|string|max:1000',
            'price' => 'required|numeric|min:0.01',
            'category_id' => 'required|exists:categories,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'image_url' => 'nullable|string|url'
        ], [
            'name.required' => 'El nombre del producto es obligatorio.',
            'description.required' => 'La descripción es obligatoria.',
            'price.required' => 'El precio es obligatorio.',
            'price.numeric' => 'El precio debe ser un número.',
            'price.min' => 'El precio debe ser mayor a 0.',
            'category_id.required' => 'La categoría es obligatoria.',
            'category_id.exists' => 'La categoría seleccionada no existe.'
        ]);

        $imagePath = $product->image;

        // Subir archivo de imagen si se provee uno nuevo
        if ($request->hasFile('image')) {
            // Eliminar imagen anterior si era local
            if (str_starts_with($product->image, '/storage/')) {
                $oldPath = str_replace('/storage/', '', $product->image);
                Storage::disk('public')->delete($oldPath);
            }
            
            $path = $request->file('image')->store('products', 'public');
            $imagePath = '/storage/' . $path;
        } elseif (!empty($validated['image_url'])) {
            $imagePath = $validated['image_url'];
        }

        $product->update([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'price' => $validated['price'],
            'category_id' => $validated['category_id'],
            'image' => $imagePath,
            'image_path' => $imagePath
        ]);

        return redirect()->route('products.index')->with('message', '¡Producto actualizado con éxito!');
    }

    /**
     * Elimina un producto de la base de datos (Solo Admin).
     */
    public function destroy(Product $product)
    {
        if (!Auth::user()->isAdmin()) {
            return redirect()->back()->with('error', 'Acceso denegado. Se requieren permisos de administrador.');
        }

        // Eliminar imagen local si existe
        if (str_starts_with($product->image, '/storage/')) {
            $oldPath = str_replace('/storage/', '', $product->image);
            Storage::disk('public')->delete($oldPath);
        }

        $product->delete();

        return redirect()->route('products.index')->with('message', '¡Producto eliminado con éxito!');
    }
}