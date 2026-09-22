<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AsignarResponsableRequest;
use App\Http\Requests\CambiarEstadoRequest;
use App\Http\Requests\ClasificarSolicitudRequest;
use App\Http\Requests\StoreSolicitudRequest;
use App\Http\Requests\UpdateSolicitudRequest;
use App\Http\Resources\AsignacionResource;
use App\Http\Resources\SolicitudResource;
use App\Models\AsignacionSolicitud;
use App\Models\EstadoSolicitud;
use App\Models\HistorialSolicitud;
use App\Models\Prioridad;
use App\Models\Solicitud;
use App\Models\Usuario;
use Illuminate\Http\Request;

class SolicitudController extends Controller
{
    private const RELACIONES_LISTADO = ['estado', 'tipo', 'prioridad', 'ubicacion', 'solicitante', 'responsableActual'];

    private const RELACIONES_DETALLE = [
        'estado', 'tipo', 'prioridad', 'ubicacion', 'recurso',
        'solicitante', 'responsableActual',
        'comentarios.autor', 'archivosAdjuntos.subidoPor',
        'asignaciones.usuarioAsignado', 'asignaciones.asignadoPor',
        'historial.usuarioCambio',
    ];

    public function index(Request $request)
    {
        $usuario = $request->user();

        $query = Solicitud::query()->with(self::RELACIONES_LISTADO);

        match ($usuario->rol?->nombre) {
            'Administrativo' => null,
            'Tecnico' => $query->where('usuario_responsable_actual_id', $usuario->id),
            default => $query->where('usuario_solicitante_id', $usuario->id),
        };

        if ($request->filled('estado_id')) {
            $query->where('estado_id', $request->integer('estado_id'));
        }

        if ($request->filled('tipo_id')) {
            $query->where('tipo_id', $request->integer('tipo_id'));
        }

        if ($request->filled('prioridad_id')) {
            $query->where('prioridad_id', $request->integer('prioridad_id'));
        }

        if ($request->filled('q')) {
            $termino = '%'.$request->string('q').'%';
            $query->where(fn ($q) => $q->where('titulo', 'ilike', $termino)->orWhere('descripcion', 'ilike', $termino));
        }

        $solicitudes = $query->orderByDesc('fecha_creacion')->paginate($request->integer('per_page', 15));

        return SolicitudResource::collection($solicitudes);
    }

    public function store(StoreSolicitudRequest $request)
    {
        $this->authorize('create', Solicitud::class);

        $prioridadId = $request->prioridad_id
            ?? Prioridad::where('nombre', 'Media')->value('id');

        $estadoAbierta = EstadoSolicitud::where('nombre', EstadoSolicitud::ABIERTA)->firstOrFail();

        $solicitud = Solicitud::create([
            'titulo' => $request->titulo,
            'descripcion' => $request->descripcion,
            'tipo_id' => $request->tipo_id,
            'ubicacion_id' => $request->ubicacion_id,
            'recurso_id' => $request->recurso_id,
            'prioridad_id' => $prioridadId,
            'estado_id' => $estadoAbierta->id,
            'usuario_solicitante_id' => $request->user()->id,
        ]);

        $this->registrarHistorial($solicitud, $request->user(), 'creacion', null, 'Solicitud registrada');

        return new SolicitudResource($solicitud->load(self::RELACIONES_DETALLE));
    }

    public function show(Request $request, Solicitud $solicitud)
    {
        $this->authorize('view', $solicitud);

        return new SolicitudResource($solicitud->load(self::RELACIONES_DETALLE));
    }

    public function update(UpdateSolicitudRequest $request, Solicitud $solicitud)
    {
        $this->authorize('update', $solicitud);

        $original = $solicitud->only(['titulo', 'descripcion', 'tipo_id', 'ubicacion_id', 'recurso_id']);

        $solicitud->fill($request->validated());
        $solicitud->save();

        foreach ($original as $campo => $valorAnterior) {
            if ($solicitud->wasChanged($campo)) {
                $this->registrarHistorial($solicitud, $request->user(), $campo, (string) $valorAnterior, (string) $solicitud->$campo);
            }
        }

        return new SolicitudResource($solicitud->load(self::RELACIONES_DETALLE));
    }

    public function destroy(Request $request, Solicitud $solicitud)
    {
        $this->authorize('delete', $solicitud);

        $solicitud->delete();

        return response()->json(['message' => 'Solicitud eliminada correctamente.']);
    }

    public function clasificar(ClasificarSolicitudRequest $request, Solicitud $solicitud)
    {
        $this->authorize('clasificar', Solicitud::class);

        if ($request->filled('tipo_id') && $request->integer('tipo_id') !== $solicitud->tipo_id) {
            $this->registrarHistorial($solicitud, $request->user(), 'tipo_id', (string) $solicitud->tipo_id, (string) $request->tipo_id);
            $solicitud->tipo_id = $request->integer('tipo_id');
        }

        if ($request->filled('prioridad_id') && $request->integer('prioridad_id') !== $solicitud->prioridad_id) {
            $this->registrarHistorial($solicitud, $request->user(), 'prioridad_id', (string) $solicitud->prioridad_id, (string) $request->prioridad_id);
            $solicitud->prioridad_id = $request->integer('prioridad_id');
        }

        $solicitud->save();

        return new SolicitudResource($solicitud->load(self::RELACIONES_DETALLE));
    }

    public function asignar(AsignarResponsableRequest $request, Solicitud $solicitud)
    {
        $this->authorize('asignar', Solicitud::class);

        $tecnico = Usuario::with('rol')->findOrFail($request->usuario_asignado_id);

        if (! $tecnico->esTecnico()) {
            return response()->json([
                'message' => 'El usuario seleccionado no tiene el rol de Técnico.',
            ], 422);
        }

        $solicitud->asignaciones()
            ->where('estado_asignacion', AsignacionSolicitud::ACTIVA)
            ->update(['estado_asignacion' => AsignacionSolicitud::COMPLETADA]);

        $solicitud->asignaciones()->create([
            'usuario_asignado_id' => $tecnico->id,
            'asignado_por_id' => $request->user()->id,
            'comentarios_asignacion' => $request->comentarios_asignacion,
        ]);

        $anteriorResponsable = $solicitud->usuario_responsable_actual_id;
        $solicitud->usuario_responsable_actual_id = $tecnico->id;

        $enProceso = EstadoSolicitud::where('nombre', EstadoSolicitud::EN_PROCESO)->value('id');
        if (in_array($solicitud->estado?->nombre, [EstadoSolicitud::ABIERTA, EstadoSolicitud::PENDIENTE_REVISION])) {
            $solicitud->estado_id = $enProceso;
        }

        $solicitud->save();

        $this->registrarHistorial(
            $solicitud,
            $request->user(),
            'usuario_responsable_actual_id',
            $anteriorResponsable ? (string) $anteriorResponsable : null,
            trim("{$tecnico->nombres} {$tecnico->apellidos}")
        );

        return new SolicitudResource($solicitud->load(self::RELACIONES_DETALLE));
    }

    public function cambiarEstado(CambiarEstadoRequest $request, Solicitud $solicitud)
    {
        $this->authorize('cambiarEstado', $solicitud);

        $nuevoEstado = EstadoSolicitud::findOrFail($request->estado_id);
        $estadoAnterior = $solicitud->estado?->nombre;

        $solicitud->estado_id = $nuevoEstado->id;
        $solicitud->fecha_cierre = $nuevoEstado->nombre === EstadoSolicitud::CERRADA ? now() : null;
        $solicitud->save();

        $this->registrarHistorial($solicitud, $request->user(), 'estado_id', $estadoAnterior, $nuevoEstado->nombre);

        if ($request->filled('comentario')) {
            $solicitud->comentarios()->create([
                'usuario_autor_id' => $request->user()->id,
                'texto_comentario' => $request->comentario,
            ]);
        }

        return new SolicitudResource($solicitud->load(self::RELACIONES_DETALLE));
    }

    public function asignaciones(Request $request, Solicitud $solicitud)
    {
        $this->authorize('view', $solicitud);

        return AsignacionResource::collection(
            $solicitud->asignaciones()->with(['usuarioAsignado', 'asignadoPor'])->orderByDesc('fecha_asignacion')->get()
        );
    }

    private function registrarHistorial(Solicitud $solicitud, Usuario $usuario, string $campo, ?string $anterior, ?string $nuevo): void
    {
        HistorialSolicitud::create([
            'solicitud_id' => $solicitud->id,
            'usuario_cambio_id' => $usuario->id,
            'campo_cambiado' => $campo,
            'valor_anterior' => $anterior,
            'valor_nuevo' => $nuevo,
        ]);
    }
}
