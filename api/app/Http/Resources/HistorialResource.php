<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HistorialResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'solicitud_id' => $this->solicitud_id,
            'campo_cambiado' => $this->campo_cambiado,
            'valor_anterior' => $this->valor_anterior,
            'valor_nuevo' => $this->valor_nuevo,
            'fecha_cambio' => $this->fecha_cambio,
            'usuario' => new UsuarioResource($this->whenLoaded('usuarioCambio')),
        ];
    }
}
