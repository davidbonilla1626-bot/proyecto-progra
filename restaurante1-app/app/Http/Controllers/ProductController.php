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
            'price' => 'required|numeric|gt:0',
            'stock' => 'required|integer|min:0',
            'category_id' => 'required|exists:categories,id',
            'image' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:2048',
            'image_url' => 'nullable|string|max:2048'
        ], [
            'name.required' => 'El nombre del producto es obligatorio.',
            'description.required' => 'La descripción es obligatoria.',
            'price.required' => 'El precio es obligatorio.',
            'price.numeric' => 'El precio debe ser un número.',
            'price.gt' => 'El precio debe ser mayor a 0.',
            'stock.required' => 'El stock inicial es obligatorio.',
            'stock.integer' => 'El stock debe ser un número entero.',
            'stock.min' => 'El stock debe ser mayor o igual a 0.',
            'category_id.required' => 'La categoría es obligatoria.',
            'category_id.exists' => 'La categoría seleccionada no existe.',
            'image.image' => 'El archivo debe ser una imagen.',
            'image.mimes' => 'La imagen debe tener un formato válido (jpeg, jpg, png, webp).',
            'image.max' => 'La imagen no debe pesar más de 2MB.'
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
            'stock' => $validated['stock'],
            'category_id' => $validated['category_id'],
            'image' => $imagePath,
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
            'price' => 'required|numeric|gt:0',
            'stock' => 'required|integer|min:0',
            'category_id' => 'required|exists:categories,id',
            'image' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:2048',
            'image_url' => 'nullable|string|max:2048'
        ], [
            'name.required' => 'El nombre del producto es obligatorio.',
            'description.required' => 'La descripción es obligatoria.',
            'price.required' => 'El precio es obligatorio.',
            'price.numeric' => 'El precio debe ser un número.',
            'price.gt' => 'El precio debe ser mayor a 0.',
            'stock.required' => 'El stock es obligatorio.',
            'stock.integer' => 'El stock debe ser un número entero.',
            'stock.min' => 'El stock debe ser mayor o igual a 0.',
            'category_id.required' => 'La categoría es obligatoria.',
            'category_id.exists' => 'La categoría seleccionada no existe.',
            'image.image' => 'El archivo debe ser una imagen.',
            'image.mimes' => 'La imagen debe tener un formato válido (jpeg, jpg, png, webp).',
            'image.max' => 'La imagen no debe pesar más de 2MB.'
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
            'stock' => $validated['stock'],
            'category_id' => $validated['category_id'],
            'image' => $imagePath,
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