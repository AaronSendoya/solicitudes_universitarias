<?php

namespace Database\Seeders;

use App\Models\ComentarioSolicitud;
use App\Models\EstadoSolicitud;
use App\Models\HistorialSolicitud;
use App\Models\Prioridad;
use App\Models\Solicitud;
use App\Models\TipoSolicitud;
use App\Models\Ubicacion;
use App\Models\Usuario;
use Illuminate\Database\Seeder;

class SolicitudDemoSeeder extends Seeder
{
    public function run(): void
    {
        $estudiante = Usuario::where('correo_institucional', 'estudiante@campusconnect.edu')->first();
        $tecnico = Usuario::where('correo_institucional', 'tecnico@campusconnect.edu')->first();

        if (! $estudiante || Solicitud::exists()) {
            return;
        }

        $abierta = EstadoSolicitud::where('nombre', EstadoSolicitud::ABIERTA)->firstOrFail();
        $tipo = TipoSolicitud::where('nombre', 'Soporte Tecnológico')->firstOrFail();
        $prioridad = Prioridad::where('nombre', 'Media')->firstOrFail();
        $ubicacion = Ubicacion::where('nombre', 'Laboratorio de Cómputo')->firstOrFail();

        $solicitud = Solicitud::create([
            'titulo' => 'Computadora sin encender en el laboratorio',
            'descripcion' => 'La PC número 5 del laboratorio de cómputo no enciende desde ayer.',
            'estado_id' => $abierta->id,
            'tipo_id' => $tipo->id,
            'prioridad_id' => $prioridad->id,
            'ubicacion_id' => $ubicacion->id,
            'usuario_solicitante_id' => $estudiante->id,
        ]);

        HistorialSolicitud::create([
            'solicitud_id' => $solicitud->id,
            'usuario_cambio_id' => $estudiante->id,
            'campo_cambiado' => 'creacion',
            'valor_anterior' => null,
            'valor_nuevo' => 'Solicitud registrada',
        ]);

        ComentarioSolicitud::create([
            'solicitud_id' => $solicitud->id,
            'usuario_autor_id' => $estudiante->id,
            'texto_comentario' => 'La PC estaba funcionando normalmente hasta ayer por la tarde.',
        ]);

        if ($tecnico) {
            $solicitud->update(['usuario_responsable_actual_id' => $tecnico->id]);

            $solicitud->asignaciones()->create([
                'usuario_asignado_id' => $tecnico->id,
                'comentarios_asignacion' => 'Revisar fuente de poder y cableado.',
            ]);

            HistorialSolicitud::create([
                'solicitud_id' => $solicitud->id,
                'campo_cambiado' => 'usuario_responsable_actual_id',
                'valor_anterior' => null,
                'valor_nuevo' => $tecnico->nombres.' '.$tecnico->apellidos,
            ]);
        }
    }
}
