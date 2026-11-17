<?php

namespace App\Http\Controllers;

use App\Models\Incidencia;
use App\Models\Prioridad;
use App\Models\Tecnico;
use App\Models\Usuario;

class DashboardController extends Controller
{
    public function __invoke()
    {
        return view('dashboard', [
            'usuariosCount' => Usuario::count(),
            'tecnicosCount' => Tecnico::count(),
            'incidenciasCount' => Incidencia::count(),
            'pendientesCount' => Incidencia::whereIn('estado', ['registrada', 'asignada', 'en_proceso'])->count(),
            'incidenciasRecientes' => Incidencia::with(['usuario', 'tecnico', 'prioridad'])->latest()->take(8)->get(),
            'prioridadesCount' => Prioridad::count(),
        ]);
    }
}
