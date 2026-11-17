@extends('layouts.app')

@section('title', 'Resumen')

@section('content')
<div class="row g-4 align-items-stretch mb-4">
    <div class="col-12 col-xl-8">
        <section class="card hero-panel h-100 p-4 p-lg-5">
            <div class="row align-items-center h-100">
                <div class="col-lg-8">
                    <div class="small text-white-50 text-uppercase fw-bold mb-2">Centro de soporte · Sistema 6</div>
                    <h1 class="display-6 fw-bold mb-3">Atiende cada incidencia<br class="d-none d-md-block"> desde un solo lugar.</h1>
                    <p class="text-white-50 mb-4">Registra solicitudes, asigna técnicos y sigue el avance hasta su resolución.</p>
                    <a class="btn btn-light text-primary px-4" href="{{ route('incidencias.create') }}">＋ Registrar incidencia</a>
                </div>
                <div class="col-lg-4 d-none d-lg-flex justify-content-center"><div class="rounded-circle border border-3 border-white border-opacity-25 d-grid place-items-center" style="width:150px;height:150px;font-size:4rem">✦</div></div>
            </div>
        </section>
    </div>
    <div class="col-12 col-xl-4">
        <div class="card h-100 p-4">
            <div class="d-flex justify-content-between align-items-start mb-3"><div><div class="text-muted small">Pendientes de atención</div><div class="display-4 metric-number">{{ $pendientesCount }}</div></div><span class="metric-icon">◷</span></div>
            <p class="text-muted mb-4">Incidencias registradas, asignadas o en proceso.</p>
            <a class="btn btn-outline-primary mt-auto" href="{{ route('incidencias.index', ['estado' => 'registrada']) }}">Revisar incidencias <span aria-hidden="true">→</span></a>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    @foreach ([['Incidencias', $incidenciasCount, 'incidencias.index', '↗'], ['Usuarios', $usuariosCount, 'usuarios.index', '◎'], ['Técnicos activos', $tecnicosCount, 'tecnicos.index', '⚙'], ['Prioridades', $prioridadesCount, 'prioridades.index', '◈']] as [$label, $count, $route, $icon])
        <div class="col-6 col-xl-3"><a class="text-decoration-none text-reset" href="{{ route($route) }}"><div class="card metric-card p-3 p-lg-4"><div class="d-flex justify-content-between align-items-start"><div><div class="text-muted small mb-2">{{ $label }}</div><div class="h2 metric-number mb-0">{{ $count }}</div></div><span class="metric-icon">{{ $icon }}</span></div></div></a></div>
    @endforeach
</div>

<section class="card">
    <div class="card-header p-3 p-lg-4 d-flex flex-wrap justify-content-between align-items-center gap-3">
        <div><h2 class="h5 fw-bold mb-1">Incidencias recientes</h2><div class="small text-muted">Últimas solicitudes registradas en el sistema</div></div>
        <a class="btn btn-outline-primary btn-sm" href="{{ route('incidencias.index') }}">Ver todas</a>
    </div>
    <div class="table-responsive">
        <table class="table mb-0">
            <thead><tr><th>Incidencia</th><th>Solicitante</th><th>Técnico</th><th>Prioridad</th><th>Estado</th><th></th></tr></thead>
            <tbody>
            @forelse ($incidenciasRecientes as $incidencia)
                <tr>
                    <td><a class="fw-semibold text-decoration-none" href="{{ route('incidencias.show', $incidencia) }}">{{ $incidencia->titulo }}</a><div class="small text-muted">#{{ $incidencia->id }} · Ocurrió: {{ $incidencia->fecha_ocurrencia?->format('d/m/Y') ?? 'Sin fecha' }}{{ $incidencia->hora_ocurrencia ? ' '.substr($incidencia->hora_ocurrencia, 0, 5) : '' }}</div><div class="small text-muted">{{ $incidencia->lugar ?: 'Lugar no especificado' }}</div></td>
                    <td>{{ $incidencia->usuario->nombre }}</td><td>{{ $incidencia->tecnico?->nombre ?? 'Sin asignar' }}</td>
                    <td><span class="badge rounded-pill text-bg-light border">{{ $incidencia->prioridad->nombre }}</span></td><td><x-estado-badge :estado="$incidencia->estado" /></td>
                    <td><a class="btn btn-sm btn-light" href="{{ route('incidencias.show', $incidencia) }}">Abrir</a></td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center text-muted py-5">Aún no hay incidencias. Registra la primera para empezar.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</section>
@endsection
