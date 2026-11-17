@extends('layouts.app')
@section('title', 'Usuarios')
@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-4"><div><div class="small text-primary text-uppercase fw-bold mb-1">Directorio</div><h1 class="h2 page-title mb-1">Usuarios</h1><p class="text-muted mb-0">Personas que reportan incidencias al área de soporte.</p></div><a class="btn btn-primary" href="{{ route('usuarios.create') }}">＋ Nuevo usuario</a></div>
<section class="card"><div class="table-responsive"><table class="table mb-0"><thead><tr><th>Nombre</th><th>Correo</th><th>Teléfono</th><th>Área</th><th>Incidencias</th><th class="text-end">Acciones</th></tr></thead><tbody>
@forelse ($usuarios as $usuario)
<tr><td><a class="fw-semibold text-decoration-none" href="{{ route('usuarios.show', $usuario) }}">{{ $usuario->nombre }}</a></td><td>{{ $usuario->email }}</td><td>{{ $usuario->telefono ?: '—' }}</td><td>{{ $usuario->departamento ?: '—' }}</td><td><span class="badge text-bg-light border">{{ $usuario->incidencias_count }}</span></td><td class="text-end"><a class="btn btn-sm btn-light" href="{{ route('usuarios.edit', $usuario) }}">Editar</a> <form class="d-inline" action="{{ route('usuarios.destroy', $usuario) }}" method="POST" onsubmit="return confirm('¿Eliminar este usuario?')">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger" type="submit">Eliminar</button></form></td></tr>
@empty <tr><td colspan="6" class="text-center text-muted py-5">No hay usuarios registrados.</td></tr>@endforelse
</tbody></table></div><div class="p-3">{{ $usuarios->links() }}</div></section>
@endsection
