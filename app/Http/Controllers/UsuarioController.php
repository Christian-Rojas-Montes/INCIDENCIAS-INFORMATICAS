<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UsuarioController extends Controller
{
    public function index()
    {
        $usuarios = Usuario::withCount('incidencias')->latest()->paginate(12)->withQueryString();

        return view('usuarios.index', compact('usuarios'));
    }

    public function create()
    {
        return view('usuarios.form', ['usuario' => new Usuario()]);
    }

    public function store(Request $request)
    {
        Usuario::create($this->validated($request));

        return redirect()->route('usuarios.index')->with('success', 'Usuario registrado correctamente.');
    }

    public function show(Usuario $usuario)
    {
        $usuario->load('incidencias.prioridad', 'incidencias.tecnico');

        return view('usuarios.show', compact('usuario'));
    }

    public function edit(Usuario $usuario)
    {
        return view('usuarios.form', compact('usuario'));
    }

    public function update(Request $request, Usuario $usuario)
    {
        $usuario->update($this->validated($request, $usuario));

        return redirect()->route('usuarios.index')->with('success', 'Datos del usuario actualizados.');
    }

    public function destroy(Usuario $usuario)
    {
        if ($usuario->incidencias()->exists()) {
            return back()->with('error', 'No se puede eliminar el usuario porque tiene incidencias registradas.');
        }

        $usuario->delete();

        return redirect()->route('usuarios.index')->with('success', 'Usuario eliminado.');
    }

    private function validated(Request $request, ?Usuario $usuario = null): array
    {
        return $request->validate([
            'nombre' => ['required', 'string', 'max:150'],
            'email' => ['required', 'email', 'max:255', Rule::unique('usuarios', 'email')->ignore($usuario)],
            'telefono' => ['nullable', 'string', 'max:30'],
            'departamento' => ['nullable', 'string', 'max:150'],
        ]);
    }
}
