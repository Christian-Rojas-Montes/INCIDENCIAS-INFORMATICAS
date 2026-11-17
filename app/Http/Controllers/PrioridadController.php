<?php

namespace App\Http\Controllers;

use App\Models\Prioridad;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PrioridadController extends Controller
{
    public function index()
    {
        $prioridades = Prioridad::withCount('incidencias')->orderBy('nivel')->paginate(12);

        return view('prioridades.index', compact('prioridades'));
    }

    public function create()
    {
        return view('prioridades.form', ['prioridad' => new Prioridad()]);
    }

    public function store(Request $request)
    {
        Prioridad::create($this->validated($request));

        return redirect()->route('prioridades.index')->with('success', 'Prioridad creada correctamente.');
    }

    public function show(Prioridad $prioridad)
    {
        $prioridad->load(['incidencias' => fn ($query) => $query->with(['usuario', 'tecnico'])->latest()]);

        return view('prioridades.show', compact('prioridad'));
    }

    public function edit(Prioridad $prioridad)
    {
        return view('prioridades.form', compact('prioridad'));
    }

    public function update(Request $request, Prioridad $prioridad)
    {
        $prioridad->update($this->validated($request, $prioridad));

        return redirect()->route('prioridades.index')->with('success', 'Prioridad actualizada.');
    }

    public function destroy(Prioridad $prioridad)
    {
        if ($prioridad->incidencias()->exists()) {
            return back()->with('error', 'No se puede eliminar una prioridad que está asignada a incidencias.');
        }

        $prioridad->delete();

        return redirect()->route('prioridades.index')->with('success', 'Prioridad eliminada.');
    }

    private function validated(Request $request, ?Prioridad $prioridad = null): array
    {
        return $request->validate([
            'nombre' => ['required', 'string', 'max:80', Rule::unique('prioridades', 'nombre')->ignore($prioridad)],
            'nivel' => ['required', 'integer', 'min:1', 'max:5', Rule::unique('prioridades', 'nivel')->ignore($prioridad)],
            'descripcion' => ['nullable', 'string', 'max:500'],
        ]);
    }
}
