<?php

namespace App\Http\Controllers;

use App\Models\Incidencia;
use App\Models\Prioridad;
use App\Models\Seguimiento;
use App\Models\Tecnico;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class IncidenciaController extends Controller
{
    public function index(Request $request)
    {
        $incidencias = Incidencia::with(['usuario', 'tecnico', 'prioridad'])
            ->when($request->filled('q'), fn ($query) => $query->where(function ($query) use ($request) {
                $term = '%'.$request->input('q').'%';
                $query->where('titulo', 'like', $term)
                    ->orWhereHas('usuario', fn ($userQuery) => $userQuery->where('nombre', 'like', $term));
            }))
            ->when($request->filled('estado'), fn ($query) => $query->where('estado', $request->input('estado')))
            ->when($request->filled('nivel'), fn ($query) => $query->whereHas('prioridad', fn ($priorityQuery) => $priorityQuery->where('nivel', $request->integer('nivel'))))
            ->latest()->paginate(12)->withQueryString();

        return view('incidencias.index', [
            'incidencias' => $incidencias,
            'estados' => Incidencia::ESTADOS,
            'prioridades' => Prioridad::orderBy('nivel')->get(),
        ]);
    }

    public function create()
    {
        return view('incidencias.form', $this->formOptions(new Incidencia(['estado' => 'registrada'])));
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        DB::transaction(function () use ($data) {
            $incidencia = Incidencia::create($data);
            Seguimiento::create([
                'incidencia_id' => $incidencia->id,
                'tecnico_id' => $incidencia->tecnico_id,
                'estado' => $incidencia->estado,
                'comentario' => 'Incidencia registrada.',
            ]);
        });

        return redirect()->route('incidencias.index')->with('success', 'Incidencia registrada correctamente.');
    }

    public function show(Incidencia $incidencia)
    {
        $incidencia->load(['usuario', 'tecnico', 'prioridad', 'seguimientos.tecnico']);

        return view('incidencias.show', ['incidencia' => $incidencia, 'estados' => Incidencia::ESTADOS]);
    }

    public function edit(Incidencia $incidencia)
    {
        return view('incidencias.form', $this->formOptions($incidencia));
    }

    public function update(Request $request, Incidencia $incidencia)
    {
        $data = $this->validated($request);
        $previous = $incidencia->only(['estado', 'tecnico_id']);
        $previousTechnicianId = (int) ($previous['tecnico_id'] ?? 0);

        DB::transaction(function () use ($incidencia, $data, $previous, $previousTechnicianId) {
            $incidencia->update($data);

            if ($previous['estado'] !== $incidencia->estado || $previousTechnicianId !== (int) ($incidencia->tecnico_id ?? 0)) {
                $changes = [];
                if ($previous['estado'] !== $incidencia->estado) {
                    $changes[] = 'Estado actualizado a '.Incidencia::ESTADOS[$incidencia->estado].'.';
                }
                if ($previousTechnicianId !== (int) ($incidencia->tecnico_id ?? 0)) {
                    $changes[] = $incidencia->tecnico_id ? 'Técnico asignado o actualizado.' : 'Técnico desasignado.';
                }
                Seguimiento::create([
                    'incidencia_id' => $incidencia->id,
                    'tecnico_id' => $incidencia->tecnico_id,
                    'estado' => $incidencia->estado,
                    'comentario' => implode(' ', $changes),
                ]);
            }
        });

        return redirect()->route('incidencias.show', $incidencia)->with('success', 'Incidencia actualizada.');
    }

    public function destroy(Incidencia $incidencia)
    {
        $incidencia->delete();

        return redirect()->route('incidencias.index')->with('success', 'Incidencia y su historial eliminados.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'titulo' => ['required', 'string', 'max:180'],
            'descripcion' => ['required', 'string', 'max:5000'],
            'fecha_ocurrencia' => ['required', 'date_format:Y-m-d'],
            'hora_ocurrencia' => ['required', 'date_format:H:i'],
            'lugar' => ['required', 'string', 'max:255'],
            'estado' => ['required', Rule::in(array_keys(Incidencia::ESTADOS))],
            'usuario_id' => ['required', 'integer', 'exists:usuarios,id'],
            'tecnico_id' => ['nullable', 'integer', 'exists:tecnicos,id'],
            'prioridad_id' => ['required', 'integer', 'exists:prioridades,id'],
        ]);
    }

    private function formOptions(Incidencia $incidencia): array
    {
        return [
            'incidencia' => $incidencia,
            'usuarios' => Usuario::orderBy('nombre')->get(),
            'tecnicos' => Tecnico::where('activo', true)
                ->when($incidencia->tecnico_id, fn ($query, $id) => $query->orWhere('id', $id))
                ->orderBy('nombre')->get(),
            'prioridades' => Prioridad::orderBy('nivel')->get(),
            'estados' => Incidencia::ESTADOS,
        ];
    }
}
