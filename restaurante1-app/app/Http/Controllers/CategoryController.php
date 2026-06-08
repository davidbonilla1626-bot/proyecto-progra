<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('Categories/Index', [
            'categories' => Category::withCount('products')->get()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:50|unique:categories,name',
            'icon_path' => 'nullable|string|max:255',
        ], [
            'name.required' => 'El nombre de la categoría es obligatorio.',
            'name.unique' => 'Esta categoría ya existe.',
            'name.max' => 'El nombre no debe superar los 50 caracteres.'
        ]);

        $category = Category::create($validated);

        \App\Models\AuditLog::log("Creó la categoría: {$category->name}");

        return redirect()->back()->with('message', 'Categoría creada con éxito.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:50|unique:categories,name,' . $category->id,
            'icon_path' => 'nullable|string|max:255',
        ], [
            'name.required' => 'El nombre de la categoría es obligatorio.',
            'name.unique' => 'Esta categoría ya existe.',
            'name.max' => 'El nombre no debe superar los 50 caracteres.'
        ]);

        $category->update($validated);

        return redirect()->back()->with('message', 'Categoría actualizada con éxito.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        // Verificar si la categoría tiene productos asociados
        if ($category->products()->count() > 0) {
            return redirect()->back()->with('error', 'No se puede eliminar la categoría porque tiene productos asociados.');
        }

        $categoryName = $category->name;
        $categoryId = $category->id;
        $category->delete();

        \App\Models\AuditLog::log("Eliminó la categoría ID {$categoryId}: {$categoryName}");

        return redirect()->back()->with('message', 'Categoría eliminada con éxito.');
    }
}
