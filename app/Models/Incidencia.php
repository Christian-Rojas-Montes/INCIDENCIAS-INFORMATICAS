<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Incidencia extends Model
{
    use HasFactory;

    protected $table = 'incidencias';

    public const ESTADOS = [
        'registrada' => 'Registrada',
        'asignada' => 'Asignada',
        'en_proceso' => 'En proceso',
        'resuelta' => 'Resuelta',
        'cerrada' => 'Cerrada',
    ];

    protected $fillable = [
        'titulo',
        'descripcion',
        'fecha_ocurrencia',
        'hora_ocurrencia',
        'lugar',
        'estado',
        'usuario_id',
        'tecnico_id',
        'prioridad_id',
    ];

    protected function casts(): array
    {
        return [
            'fecha_ocurrencia' => 'date:Y-m-d',
        ];
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class);
    }

    public function tecnico(): BelongsTo
    {
        return $this->belongsTo(Tecnico::class);
    }

    public function prioridad(): BelongsTo
    {
        return $this->belongsTo(Prioridad::class);
    }

    public function seguimientos(): HasMany
    {
        return $this->hasMany(Seguimiento::class);
    }
}
