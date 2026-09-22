<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreArchivoAdjuntoRequest;
use App\Http\Resources\ArchivoAdjuntoResource;
use App\Models\ArchivoAdjunto;
use App\Models\Solicitud;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ArchivoAdjuntoController extends Controller
{
    public function index(Request $request, Solicitud $solicitud)
    {
        $this->authorize('view', $solicitud);

        return ArchivoAdjuntoResource::collection(
            $solicitud->archivosAdjuntos()->with('subidoPor')->get()
        );
    }

    public function store(StoreArchivoAdjuntoRequest $request, Solicitud $solicitud)
    {
        $this->authorize('adjuntar', $solicitud);

        $archivo = $request->file('archivo');
        $ruta = $archivo->store("adjuntos/{$solicitud->id}", 'public');

        $adjunto = $solicitud->archivosAdjuntos()->create([
            'usuario_subidor_id' => $request->user()->id,
            'nombre_archivo' => $archivo->getClientOriginalName(),
            'url_archivo' => $ruta,
            'tipo_archivo' => $archivo->getClientMimeType(),
            'tamano_bytes' => $archivo->getSize(),
        ]);

        return new ArchivoAdjuntoResource($adjunto->load('subidoPor'));
    }

    public function destroy(Request $request, Solicitud $solicitud, ArchivoAdjunto $archivo)
    {
        $this->authorize('adjuntar', $solicitud);

        if ($archivo->solicitud_id !== $solicitud->id) {
            abort(404);
        }

        Storage::disk('public')->delete($archivo->url_archivo);
        $archivo->delete();

        return response()->json(['message' => 'Archivo eliminado correctamente.']);
    }
}
