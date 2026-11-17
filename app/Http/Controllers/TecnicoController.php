<?php

namespace App\Http\Controllers;

use App\Models\Tecnico;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TecnicoController extends Controller
{
    public function index()
    {
        $tecnicos = Tecnico::withCount(['incidencias', 'seguimientos'])->latest()->paginate(12)->withQueryString();

        return view('tecnicos.index', compact('tecnicos'));
    }

    public function create()
    {
        return view('tecnicos.form', ['tecnico' => new Tecnico(['activo' => true])]);
    }

    public function store(Request $request)
    {
        Tecnico::create($this->validated($request));

        return redirect()->route('tecnicos.index')->with('success', 'Técnico registrado correctamente.');
    }

    public function show(Tecnico $tecnico)
    {
        $tecnico->load(['incidencias' => fn ($query) => $query->with(['usuario', 'prioridad'])->latest()]);

        return view('tecnicos.show', compact('tecnico'));
    }

    public function edit(Tecnico $tecnico)
    {
        return view('tecnicos.form', compact('tecnico'));
    }

    public function update(Request $request, Tecnico $tecnico)
    {
        $tecnico->update($this->validated($request, $tecnico));

        return redirect()->route('tecnicos.index')->with('success', 'Datos del técnico actualizados.');
    }

    public function destroy(Tecnico $tecnico)
    {
        if ($tecnico->incidencias()->exists() || $tecnico->seguimientos()->exists()) {
            return back()->with('error', 'No se puede eliminar el técnico porque aparece en incidencias o seguimientos.');
        }

        $tecnico->delete();

        return redirect()->route('tecnicos.index')->with('success', 'Técnico eliminado.');
    }

    private function validated(Request $request, ?Tecnico $tecnico = null): array
    {
        return $request->validate([
            'nombre' => ['required', 'string', 'max:150'],
            'email' => ['required', 'email', 'max:255', Rule::unique('tecnicos', 'email')->ignore($tecnico)],
            'telefono' => ['nullable', 'string', 'max:30'],
            'especialidad' => ['nullable', 'string', 'max:150'],
            'activo' => ['required', 'boolean'],
        ]);
    }
}
