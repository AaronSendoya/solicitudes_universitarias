<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ArchivoAdjuntoResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'solicitud_id' => $this->solicitud_id,
            'nombre_archivo' => $this->nombre_archivo,
            // Relative path on purpose: the API host seen by the client (emulator
            // alias, LAN IP, production domain, ...) can differ from APP_URL, so
            // the client resolves this against whatever base URL it used to call
            // the API rather than trusting a server-guessed absolute URL.
            'url_archivo' => '/storage/'.$this->url_archivo,
            'tipo_archivo' => $this->tipo_archivo,
            'tamano_bytes' => $this->tamano_bytes,
            'fecha_subida' => $this->fecha_subida,
            'subido_por' => new UsuarioResource($this->whenLoaded('subidoPor')),
        ];
    }
}
