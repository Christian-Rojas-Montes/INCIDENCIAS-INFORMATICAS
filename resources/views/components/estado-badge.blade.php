@props(['estado'])
@php
    $label = \App\Models\Incidencia::ESTADOS[$estado] ?? ucfirst(str_replace('_', ' ', $estado));
    $style = match ($estado) {
        'registrada' => 'text-bg-secondary',
        'asignada' => 'text-bg-info',
        'en_proceso' => 'text-bg-primary',
        'resuelta' => 'text-bg-success',
        'cerrada' => 'text-bg-dark',
        default => 'text-bg-light',
    };
@endphp
<span {{ $attributes->class(['badge rounded-pill', $style]) }}>{{ $label }}</span>
