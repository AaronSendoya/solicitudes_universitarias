<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\HistorialResource;
use App\Models\Solicitud;
use Illuminate\Http\Request;

class HistorialController extends Controller
{
    public function index(Request $request, Solicitud $solicitud)
    {
        $this->authorize('view', $solicitud);

        return HistorialResource::collection(
            $solicitud->historial()->with('usuarioCambio')->get()
        );
    }
}
