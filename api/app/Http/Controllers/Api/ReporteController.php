<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\EstadoSolicitud;
use App\Models\Solicitud;
use App\Support\CatalogoCache;
use Illuminate\Http\Request;

class ReporteController extends Controller
{
    /**
     * Reportes de gestión, con soporte de filtrado por métricas
     * (fecha_desde, fecha_hasta, tipo_id, prioridad_id, ubicacion_id).
     */
    public function gestion(Request $request)
    {
        $this->authorize('viewReportes', Solicitud::class);

        $query = Solicitud::query()->with('responsableActual');

        if ($request->filled('fecha_desde')) {
            $query->whereDate('fecha_creacion', '>=', $request->date('fecha_desde'));
        }

        if ($request->filled('fecha_hasta')) {
            $query->whereDate('fecha_creacion', '<=', $request->date('fecha_hasta'));
        }

        foreach (['tipo_id', 'prioridad_id', 'ubicacion_id'] as $filtro) {
            if ($request->filled($filtro)) {
                $query->where($filtro, $request->integer($filtro));
            }
        }

        $solicitudes = $query->get();

        $cerradas = $solicitudes->filter(
            fn ($s) => CatalogoCache::estadoNombre($s->estado_id) === EstadoSolicitud::CERRADA && $s->fecha_cierre
        );

        $tiempoPromedioHoras = $cerradas->isEmpty()
            ? null
            : round($cerradas->avg(fn ($s) => $s->fecha_creacion->diffInMinutes($s->fecha_cierre)) / 60, 1);

        return response()->json([
            'total_solicitudes' => $solicitudes->count(),
            'tiempo_promedio_resolucion_horas' => $tiempoPromedioHoras,
            'por_estado' => $solicitudes->groupBy(fn ($s) => CatalogoCache::estadoNombre($s->estado_id) ?? 'Sin estado')
                ->map->count()
                ->map(fn ($total, $nombre) => ['nombre' => $nombre, 'total' => $total])
                ->values(),
            'por_tipo' => $solicitudes->groupBy(fn ($s) => CatalogoCache::tipoNombre($s->tipo_id) ?? 'Sin tipo')
                ->map->count()
                ->map(fn ($total, $nombre) => ['nombre' => $nombre, 'total' => $total])
                ->values(),
            'por_prioridad' => $solicitudes->groupBy(fn ($s) => CatalogoCache::prioridadNombre($s->prioridad_id) ?? 'Sin prioridad')
                ->map->count()
                ->map(fn ($total, $nombre) => ['nombre' => $nombre, 'total' => $total])
                ->values(),
            'por_tecnico' => $solicitudes->whereNotNull('usuario_responsable_actual_id')
                ->groupBy(fn ($s) => $s->responsableActual?->nombres.' '.$s->responsableActual?->apellidos)
                ->map->count()
                ->map(fn ($total, $nombre) => ['nombre' => trim($nombre), 'total' => $total])
                ->values(),
        ]);
    }
}
