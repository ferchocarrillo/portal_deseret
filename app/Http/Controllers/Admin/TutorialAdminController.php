<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tutorial;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

class TutorialAdminController extends Controller
{
    // Listar todos los tutoriales en el panel de administración
    public function index()
    {
        $tutorials = Tutorial::orderBy('category')->orderBy('order')->get();

        return Inertia::render('admin/Tutorials/Index', [
            'tutorials' => $tutorials
        ]);
    }

    // Formulario para crear un nuevo tutorial
    public function create()
    {
        return Inertia::render('admin/Tutorials/Create');
    }

    // Guardar el nuevo tutorial en la base de datos
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'description' => 'nullable|string',
            'video_url' => 'required|string',
            'order' => 'nullable|integer',
            'is_active' => 'boolean',
        ]);

        $validated['slug'] = Str::slug($validated['title']);
        $validated['order'] = $validated['order'] ?? 0;
        $validated['is_active'] = $request->has('is_active');

        Tutorial::create($validated);

        return redirect()->route('admin.tutorials.index')
            ->with('success', 'Videotutorial creado exitosamente.');
    }

    // Formulario para editar un tutorial existente
    public function edit(Tutorial $tutorial)
    {
        return Inertia::render('admin/Tutorials/Edit', [
            'tutorial' => $tutorial
        ]);
    }

    // Actualizar el tutorial
    public function update(Request $request, Tutorial $tutorial)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'description' => 'nullable|string',
            'video_url' => 'required|string',
            'order' => 'nullable|integer',
            'is_active' => 'boolean',
        ]);

        $validated['slug'] = Str::slug($validated['title']);
        $validated['order'] = $validated['order'] ?? 0;
        $validated['is_active'] = $request->has('is_active');

        $tutorial->update($validated);

        return redirect()->route('admin.tutorials.index')
            ->with('success', 'Videotutorial actualizado exitosamente.');
    }

    // Eliminar un tutorial
    public function destroy(Tutorial $tutorial)
    {
        $tutorial->delete();

        return redirect()->route('admin.tutorials.index')
            ->with('success', 'Videotutorial eliminado correctamente.');
    }
}
