<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ComentarioResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'solicitud_id' => $this->solicitud_id,
            'texto_comentario' => $this->texto_comentario,
            'fecha_comentario' => $this->fecha_comentario,
            'autor' => new UsuarioResource($this->whenLoaded('autor')),
        ];
    }
}
