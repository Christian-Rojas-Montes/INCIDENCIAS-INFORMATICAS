<?php

namespace App\Http\Controllers;

use App\Models\Incidencia;
use App\Models\Seguimiento;
use App\Models\Tecnico;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class SeguimientoController extends Controller
{
    public function index()
    {
        $seguimientos = Seguimiento::with(['incidencia', 'tecnico'])->latest()->paginate(15);

        return view('seguimientos.index', compact('seguimientos'));
    }

    public function create(Request $request)
    {
        $incidencia = $request->filled('incidencia_id') ? Incidencia::find($request->integer('incidencia_id')) : null;
        $seguimiento = new Seguimiento([
            'incidencia_id' => $incidencia?->id,
            'tecnico_id' => $incidencia?->tecnico_id,
            'estado' => $incidencia?->estado ?? 'registrada',
        ]);

        return view('seguimientos.form', $this->formOptions($seguimiento));
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);

        DB::transaction(function () use ($data) {
            Seguimiento::create($data);
            Incidencia::whereKey($data['incidencia_id'])->update([
                'estado' => $data['estado'],
                'tecnico_id' => $data['tecnico_id'] ?? null,
            ]);
        });

        return redirect()->route('seguimientos.index')->with('success', 'Seguimiento registrado y estado de incidencia actualizado.');
    }

    public function show(Seguimiento $seguimiento)
    {
        $seguimiento->load(['incidencia', 'tecnico']);

        return view('seguimientos.show', compact('seguimiento'));
    }

    public function edit(Seguimiento $seguimiento)
    {
        return view('seguimientos.form', $this->formOptions($seguimiento));
    }

    public function update(Request $request, Seguimiento $seguimiento)
    {
        $data = $this->validated($request);

        $previousIncidenciaId = $seguimiento->incidencia_id;
        DB::transaction(function () use ($seguimiento, $data, $previousIncidenciaId) {
            $seguimiento->update($data);
            $this->syncIncidencia((int) $previousIncidenciaId);
            if ((int) $data['incidencia_id'] !== (int) $previousIncidenciaId) {
                $this->syncIncidencia((int) $data['incidencia_id']);
            }
        });

        return redirect()->route('seguimientos.show', $seguimiento)->with('success', 'Seguimiento actualizado.');
    }

    public function destroy(Seguimiento $seguimiento)
    {
        $incidenciaId = $seguimiento->incidencia_id;
        $seguimiento->delete();
        $this->syncIncidencia($incidenciaId);

        return redirect()->route('seguimientos.index')->with('success', 'Seguimiento eliminado.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'incidencia_id' => ['required', 'integer', 'exists:incidencias,id'],
            'tecnico_id' => ['nullable', 'integer', 'exists:tecnicos,id'],
            'estado' => ['required', Rule::in(array_keys(Incidencia::ESTADOS))],
            'comentario' => ['required', 'string', 'max:5000'],
        ]);
    }

    private function formOptions(Seguimiento $seguimiento): array
    {
        return [
            'seguimiento' => $seguimiento,
            'incidencias' => Incidencia::with('usuario')->latest()->get(),
            'tecnicos' => Tecnico::where('activo', true)
                ->when($seguimiento->tecnico_id, fn ($query, $id) => $query->orWhere('id', $id))
                ->orderBy('nombre')->get(),
            'estados' => Incidencia::ESTADOS,
        ];
    }

    private function syncIncidencia(int $incidenciaId): void
    {
        $incidencia = Incidencia::find($incidenciaId);
        if (! $incidencia) {
            return;
        }

        $ultimo = $incidencia->seguimientos()->latest('created_at')->latest('id')->first();
        $incidencia->update([
            'estado' => $ultimo?->estado ?? 'registrada',
            'tecnico_id' => $ultimo?->tecnico_id,
        ]);
    }
}
