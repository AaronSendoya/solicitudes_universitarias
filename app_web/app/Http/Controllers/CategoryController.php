<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Lista todas las categorías.
     */
    public function index()
    {
        $categories = Category::orderBy('name')->paginate(15);
        return view('manager.categories.index', compact('categories'));
    }

    /**
     * Muestra el formulario para crear una categoría.
     */
    public function create()
    {
        return view('manager.categories.create');
    }

    /**
     * Almacena una nueva categoría.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:categories,name'],
            'description' => ['nullable', 'string'],
        ]);

        Category::create([
            'name' => $request->name,
            'description' => $request->description,
            'is_active' => true,
        ]);

        return redirect()->route('manager.categories.index')->with('success', 'Categoría registrada correctamente.');
    }

    /**
     * Muestra el formulario de edición de una categoría.
     */
    public function edit($id)
    {
        $category = Category::findOrFail($id);
        return view('manager.categories.edit', compact('category'));
    }

    /**
     * Actualiza una categoría.
     */
    public function update(Request $request, $id)
    {
        $category = Category::findOrFail($id);

        $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:categories,name,'.$category->id],
            'description' => ['nullable', 'string'],
        ]);

        $category->update([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        return redirect()->route('manager.categories.index')->with('success', 'Categoría actualizada correctamente.');
    }

    /**
     * Alterna el estado activo/inactivo de una categoría.
     */
    public function toggleActive($id)
    {
        $category = Category::findOrFail($id);
        $category->update([
            'is_active' => !$category->is_active,
        ]);

        $statusMessage = $category->is_active ? 'activada' : 'desactivada';
        return redirect()->back()->with('success', "Categoría {$statusMessage} correctamente.");
    }
}
