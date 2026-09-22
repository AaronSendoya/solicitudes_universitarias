<?php

namespace App\Http\Resources;

use App\Support\CatalogoCache;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SolicitudResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'titulo' => $this->titulo,
            'descripcion' => $this->descripcion,
            'fecha_creacion' => $this->fecha_creacion,
            'fecha_actualizacion' => $this->fecha_actualizacion,
            'fecha_cierre' => $this->fecha_cierre,

            // Resolved from an in-memory cache rather than an eager-loaded
            // relation: these five catalogs rarely change, and each one
            // used to cost a full round trip to a remote database.
            'estado_id' => $this->estado_id,
            'estado' => CatalogoCache::estadoNombre($this->estado_id),
            'tipo_id' => $this->tipo_id,
            'tipo' => CatalogoCache::tipoNombre($this->tipo_id),
            'prioridad_id' => $this->prioridad_id,
            'prioridad' => CatalogoCache::prioridadNombre($this->prioridad_id),
            'ubicacion_id' => $this->ubicacion_id,
            'ubicacion' => CatalogoCache::ubicacionNombre($this->ubicacion_id),
            'recurso_id' => $this->recurso_id,
            'recurso' => CatalogoCache::recursoNombre($this->recurso_id),

            'solicitante' => new UsuarioResource($this->whenLoaded('solicitante')),
            'responsable_actual' => new UsuarioResource($this->whenLoaded('responsableActual')),

            'comentarios' => ComentarioResource::collection($this->whenLoaded('comentarios')),
            'archivos_adjuntos' => ArchivoAdjuntoResource::collection($this->whenLoaded('archivosAdjuntos')),
            'asignaciones' => AsignacionResource::collection($this->whenLoaded('asignaciones')),
            'historial' => HistorialResource::collection($this->whenLoaded('historial')),
        ];
    }
}
