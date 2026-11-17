<?php

namespace Database\Seeders;

use App\Models\Incidencia;
use App\Models\Prioridad;
use App\Models\Seguimiento;
use App\Models\Tecnico;
use App\Models\Usuario;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $usuarios = collect([
            ['nombre' => 'Ana Torres', 'email' => 'ana.torres@empresa.test', 'telefono' => '999 123 456', 'departamento' => 'Contabilidad'],
            ['nombre' => 'Luis Mendoza', 'email' => 'luis.mendoza@empresa.test', 'telefono' => '988 234 567', 'departamento' => 'Ventas'],
            ['nombre' => 'María Rojas', 'email' => 'maria.rojas@empresa.test', 'telefono' => '977 345 678', 'departamento' => 'Recursos Humanos'],
        ])->map(fn ($attributes) => Usuario::firstOrCreate(['email' => $attributes['email']], $attributes));

        $tecnicos = collect([
            ['nombre' => 'Carlos Vega', 'email' => 'carlos.vega@empresa.test', 'telefono' => '966 111 222', 'especialidad' => 'Hardware y redes', 'activo' => true],
            ['nombre' => 'Sofía Paredes', 'email' => 'sofia.paredes@empresa.test', 'telefono' => '955 222 333', 'especialidad' => 'Sistemas y software', 'activo' => true],
        ])->map(fn ($attributes) => Tecnico::firstOrCreate(['email' => $attributes['email']], $attributes));

        $prioridades = collect([
            ['nombre' => 'Baja', 'nivel' => 1, 'descripcion' => 'Impacto menor; no interrumpe el trabajo.'],
            ['nombre' => 'Media', 'nivel' => 2, 'descripcion' => 'Afecta parcialmente las actividades del usuario.'],
            ['nombre' => 'Alta', 'nivel' => 3, 'descripcion' => 'Interrumpe una actividad importante.'],
            ['nombre' => 'Crítica', 'nivel' => 4, 'descripcion' => 'Interrupción general o servicio esencial fuera de línea.'],
        ])->map(fn ($attributes) => Prioridad::firstOrCreate(['nombre' => $attributes['nombre']], $attributes));

        $examples = [
            ['titulo' => 'Equipo no enciende', 'descripcion' => 'La computadora del área contable no inicia al presionar el botón de encendido.', 'fecha_ocurrencia' => '2026-10-26', 'hora_ocurrencia' => '08:30', 'lugar' => 'Pabellón de Sistemas, 2do piso - Clase 2', 'estado' => 'en_proceso', 'usuario' => 0, 'tecnico' => 0, 'prioridad' => 2],
            ['titulo' => 'Restablecer acceso al correo', 'descripcion' => 'El usuario olvidó su contraseña y necesita recuperar el acceso al correo corporativo.', 'fecha_ocurrencia' => '2026-10-26', 'hora_ocurrencia' => '09:15', 'lugar' => 'Oficina de Ventas', 'estado' => 'asignada', 'usuario' => 1, 'tecnico' => 1, 'prioridad' => 1],
            ['titulo' => 'Conexión de red intermitente', 'descripcion' => 'La conexión inalámbrica se desconecta varias veces durante la jornada.', 'fecha_ocurrencia' => '2026-10-26', 'hora_ocurrencia' => '10:00', 'lugar' => 'Recursos Humanos', 'estado' => 'registrada', 'usuario' => 2, 'tecnico' => null, 'prioridad' => 1],
        ];

        foreach ($examples as $example) {
            $incidencia = Incidencia::firstOrCreate(
                ['titulo' => $example['titulo'], 'usuario_id' => $usuarios[$example['usuario']]->id],
                [
                    'descripcion' => $example['descripcion'],
                    'fecha_ocurrencia' => $example['fecha_ocurrencia'],
                    'hora_ocurrencia' => $example['hora_ocurrencia'],
                    'lugar' => $example['lugar'],
                    'estado' => $example['estado'],
                    'tecnico_id' => $example['tecnico'] === null ? null : $tecnicos[$example['tecnico']]->id,
                    'prioridad_id' => $prioridades[$example['prioridad']]->id,
                ],
            );

            if ($incidencia->wasRecentlyCreated) {
                Seguimiento::create([
                    'incidencia_id' => $incidencia->id,
                    'tecnico_id' => $incidencia->tecnico_id,
                    'estado' => $incidencia->estado,
                    'comentario' => 'Incidencia registrada. '.($incidencia->tecnico_id ? 'Se asignó un técnico para su atención.' : 'Pendiente de asignación.'),
                ]);
            }
        }
    }
}
