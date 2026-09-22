<?php

namespace App\Http\Resources;

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

            'estado_id' => $this->estado_id,
            'estado' => $this->whenLoaded('estado', fn () => $this->estado->nombre),
            'tipo_id' => $this->tipo_id,
            'tipo' => $this->whenLoaded('tipo', fn () => $this->tipo->nombre),
            'prioridad_id' => $this->prioridad_id,
            'prioridad' => $this->whenLoaded('prioridad', fn () => $this->prioridad->nombre),
            'ubicacion_id' => $this->ubicacion_id,
            'ubicacion' => $this->whenLoaded('ubicacion', fn () => $this->ubicacion->nombre),
            'recurso_id' => $this->recurso_id,
            'recurso' => $this->whenLoaded('recurso', fn () => $this->recurso?->nombre),

            'solicitante' => new UsuarioResource($this->whenLoaded('solicitante')),
            'responsable_actual' => new UsuarioResource($this->whenLoaded('responsableActual')),

            'comentarios' => ComentarioResource::collection($this->whenLoaded('comentarios')),
            'archivos_adjuntos' => ArchivoAdjuntoResource::collection($this->whenLoaded('archivosAdjuntos')),
            'asignaciones' => AsignacionResource::collection($this->whenLoaded('asignaciones')),
            'historial' => HistorialResource::collection($this->whenLoaded('historial')),
        ];
    }
}
