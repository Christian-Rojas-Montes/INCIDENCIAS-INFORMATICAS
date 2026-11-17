<?php

namespace Tests\Feature;

use App\Models\Incidencia;
use App\Models\Prioridad;
use App\Models\Seguimiento;
use App\Models\Tecnico;
use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ModuleCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_module_list_create_show_and_edit_pages_render(): void
    {
        $usuario = $this->createUsuario();
        $tecnico = $this->createTecnico();
        $prioridad = $this->createPrioridad();
        $incidencia = $this->createIncidencia($usuario, $prioridad, $tecnico);
        $seguimiento = Seguimiento::create([
            'incidencia_id' => $incidencia->id,
            'tecnico_id' => $tecnico->id,
            'estado' => 'asignada',
            'comentario' => 'Revisión inicial.',
        ]);

        $this->get('/')->assertOk();

        foreach ([
            ['usuarios', $usuario],
            ['tecnicos', $tecnico],
            ['prioridades', $prioridad],
            ['incidencias', $incidencia],
            ['seguimientos', $seguimiento],
        ] as [$resource, $record]) {
            $this->get(route($resource.'.index'))->assertOk();
            $this->get(route($resource.'.create'))->assertOk();
            $this->get(route($resource.'.show', $record))->assertOk();
            $this->get(route($resource.'.edit', $record))->assertOk();
        }
    }

    public function test_usuario_tecnico_and_prioridad_can_be_created_updated_and_deleted(): void
    {
        $this->post(route('usuarios.store'), [
            'nombre' => 'Ana Torres',
            'email' => 'ana@example.test',
            'telefono' => '999123456',
            'departamento' => 'Contabilidad',
        ])->assertRedirect(route('usuarios.index'));
        $usuario = Usuario::where('email', 'ana@example.test')->firstOrFail();

        $this->put(route('usuarios.update', $usuario), [
            'nombre' => 'Ana Actualizada',
            'email' => 'ana@example.test',
            'telefono' => '999123457',
            'departamento' => 'Finanzas',
        ])->assertRedirect(route('usuarios.index'));
        $this->assertDatabaseHas('usuarios', ['id' => $usuario->id, 'nombre' => 'Ana Actualizada']);

        $this->post(route('tecnicos.store'), [
            'nombre' => 'Carlos Vega',
            'email' => 'carlos@example.test',
            'telefono' => '966111222',
            'especialidad' => 'Redes',
            'activo' => '1',
        ])->assertRedirect(route('tecnicos.index'));
        $tecnico = Tecnico::where('email', 'carlos@example.test')->firstOrFail();

        $this->put(route('tecnicos.update', $tecnico), [
            'nombre' => 'Carlos Actualizado',
            'email' => 'carlos@example.test',
            'telefono' => '966111223',
            'especialidad' => 'Hardware',
            'activo' => '1',
        ])->assertRedirect(route('tecnicos.index'));
        $this->assertDatabaseHas('tecnicos', ['id' => $tecnico->id, 'nombre' => 'Carlos Actualizado']);

        $this->post(route('prioridades.store'), [
            'nombre' => 'Alta',
            'nivel' => 3,
            'descripcion' => 'Atención prioritaria.',
        ])->assertRedirect(route('prioridades.index'));
        $prioridad = Prioridad::where('nombre', 'Alta')->firstOrFail();

        $this->put(route('prioridades.update', $prioridad), [
            'nombre' => 'Urgente',
            'nivel' => 4,
            'descripcion' => 'Atención inmediata.',
        ])->assertRedirect(route('prioridades.index'));
        $this->assertDatabaseHas('prioridades', ['id' => $prioridad->id, 'nombre' => 'Urgente', 'nivel' => 4]);

        $this->delete(route('usuarios.destroy', $usuario))->assertRedirect(route('usuarios.index'));
        $this->delete(route('tecnicos.destroy', $tecnico))->assertRedirect(route('tecnicos.index'));
        $this->delete(route('prioridades.destroy', $prioridad))->assertRedirect(route('prioridades.index'));

        $this->assertDatabaseMissing('usuarios', ['id' => $usuario->id]);
        $this->assertDatabaseMissing('tecnicos', ['id' => $tecnico->id]);
        $this->assertDatabaseMissing('prioridades', ['id' => $prioridad->id]);
    }

    public function test_incidence_crud_creates_history_and_deleting_it_cascades_to_followups(): void
    {
        $usuario = $this->createUsuario();
        $tecnico = $this->createTecnico();
        $prioridad = $this->createPrioridad();

        $this->post(route('incidencias.store'), [
            'titulo' => 'Equipo sin conexión',
            'descripcion' => 'La computadora no puede conectarse a la red.',
            'fecha_ocurrencia' => '2026-11-05',
            'hora_ocurrencia' => '09:30',
            'lugar' => 'Pabellón de Sistemas, 2do piso - Clase 2',
            'estado' => 'registrada',
            'usuario_id' => $usuario->id,
            'tecnico_id' => '',
            'prioridad_id' => $prioridad->id,
        ])->assertRedirect(route('incidencias.index'));

        $incidencia = Incidencia::where('titulo', 'Equipo sin conexión')->firstOrFail();
        $this->assertDatabaseHas('seguimientos', [
            'incidencia_id' => $incidencia->id,
            'estado' => 'registrada',
        ]);
        $this->assertDatabaseHas('incidencias', [
            'id' => $incidencia->id,
            'fecha_ocurrencia' => '2026-11-05',
            'lugar' => 'Pabellón de Sistemas, 2do piso - Clase 2',
        ]);
        $this->assertSame('09:30', substr($incidencia->fresh()->hora_ocurrencia, 0, 5));
        $this->get(route('incidencias.show', $incidencia))
            ->assertOk()
            ->assertSee('05/11/2026')
            ->assertSee('09:30')
            ->assertSee('Pabellón de Sistemas, 2do piso - Clase 2');
        $this->get(route('incidencias.index'))
            ->assertOk()
            ->assertSee('Pabellón de Sistemas, 2do piso - Clase 2');

        $this->put(route('incidencias.update', $incidencia), [
            'titulo' => 'Equipo asignado',
            'descripcion' => 'Se asignó el incidente para diagnóstico.',
            'fecha_ocurrencia' => '2026-11-05',
            'hora_ocurrencia' => '09:30',
            'lugar' => 'Pabellón de Sistemas, 2do piso - Clase 2',
            'estado' => 'asignada',
            'usuario_id' => $usuario->id,
            'tecnico_id' => $tecnico->id,
            'prioridad_id' => $prioridad->id,
        ])->assertRedirect(route('incidencias.show', $incidencia));

        $this->assertDatabaseHas('incidencias', [
            'id' => $incidencia->id,
            'estado' => 'asignada',
            'tecnico_id' => $tecnico->id,
        ]);
        $this->assertDatabaseHas('seguimientos', [
            'incidencia_id' => $incidencia->id,
            'estado' => 'asignada',
            'tecnico_id' => $tecnico->id,
        ]);

        $this->delete(route('incidencias.destroy', $incidencia))
            ->assertRedirect(route('incidencias.index'));

        $this->assertDatabaseMissing('incidencias', ['id' => $incidencia->id]);
        $this->assertDatabaseMissing('seguimientos', ['incidencia_id' => $incidencia->id]);
    }

    public function test_seguimiento_crud_keeps_incidence_state_and_technician_in_sync(): void
    {
        $usuario = $this->createUsuario();
        $tecnico = $this->createTecnico();
        $prioridad = $this->createPrioridad();
        $incidencia = $this->createIncidencia($usuario, $prioridad);

        $this->post(route('seguimientos.store'), [
            'incidencia_id' => $incidencia->id,
            'tecnico_id' => $tecnico->id,
            'estado' => 'en_proceso',
            'comentario' => 'Se inició el diagnóstico.',
        ])->assertRedirect(route('seguimientos.index'));

        $seguimiento = Seguimiento::where('incidencia_id', $incidencia->id)
            ->where('estado', 'en_proceso')
            ->firstOrFail();
        $this->assertDatabaseHas('incidencias', [
            'id' => $incidencia->id,
            'estado' => 'en_proceso',
            'tecnico_id' => $tecnico->id,
        ]);

        $this->put(route('seguimientos.update', $seguimiento), [
            'incidencia_id' => $incidencia->id,
            'tecnico_id' => $tecnico->id,
            'estado' => 'resuelta',
            'comentario' => 'El servicio quedó restablecido.',
        ])->assertRedirect(route('seguimientos.show', $seguimiento));
        $this->assertDatabaseHas('incidencias', [
            'id' => $incidencia->id,
            'estado' => 'resuelta',
            'tecnico_id' => $tecnico->id,
        ]);

        $this->delete(route('seguimientos.destroy', $seguimiento))
            ->assertRedirect(route('seguimientos.index'));
        $this->assertDatabaseHas('incidencias', [
            'id' => $incidencia->id,
            'estado' => 'registrada',
            'tecnico_id' => null,
        ]);
    }

    public function test_forms_reject_invalid_values_and_related_records(): void
    {
        $this->from(route('usuarios.create'))
            ->post(route('usuarios.store'), ['nombre' => '', 'email' => 'no-es-correo'])
            ->assertSessionHasErrors(['nombre', 'email']);

        $this->from(route('incidencias.create'))
            ->post(route('incidencias.store'), [
                'titulo' => '',
                'descripcion' => 'Descripción',
                'fecha_ocurrencia' => '05/11/2026',
                'hora_ocurrencia' => 'nueve y media',
                'lugar' => '',
                'estado' => 'estado-invalido',
                'usuario_id' => 999,
                'tecnico_id' => 999,
                'prioridad_id' => 999,
            ])
            ->assertSessionHasErrors([
                'titulo',
                'fecha_ocurrencia',
                'hora_ocurrencia',
                'lugar',
                'estado',
                'usuario_id',
                'tecnico_id',
                'prioridad_id',
            ]);
    }

    private function createUsuario(): Usuario
    {
        return Usuario::create([
            'nombre' => 'Ana Torres',
            'email' => 'ana@example.test',
            'telefono' => '999123456',
            'departamento' => 'Contabilidad',
        ]);
    }

    private function createTecnico(): Tecnico
    {
        return Tecnico::create([
            'nombre' => 'Carlos Vega',
            'email' => 'carlos@example.test',
            'telefono' => '966111222',
            'especialidad' => 'Redes',
            'activo' => true,
        ]);
    }

    private function createPrioridad(): Prioridad
    {
        return Prioridad::create([
            'nombre' => 'Media',
            'nivel' => 2,
            'descripcion' => 'Atención regular.',
        ]);
    }

    private function createIncidencia(Usuario $usuario, Prioridad $prioridad, ?Tecnico $tecnico = null): Incidencia
    {
        return Incidencia::create([
            'titulo' => 'Incidencia de prueba',
            'descripcion' => 'Descripción de prueba.',
            'fecha_ocurrencia' => '2026-11-05',
            'hora_ocurrencia' => '09:30',
            'lugar' => 'Aula de prueba',
            'estado' => 'registrada',
            'usuario_id' => $usuario->id,
            'tecnico_id' => $tecnico?->id,
            'prioridad_id' => $prioridad->id,
        ]);
    }
}
