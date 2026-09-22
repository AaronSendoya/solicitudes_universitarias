<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Request as RequestModel;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    /**
     * Muestra el dashboard de métricas y reportes para el Gestor.
     */
    public function index()
    {
        // 1. Métricas KPI (Consultas de agregación rápida)
        $kpis = [
            'total' => RequestModel::count(),
            'active' => RequestModel::whereIn('status', ['Pendiente', 'Asignado', 'En Proceso'])->count(),
            'resolved' => RequestModel::whereIn('status', ['Resuelto', 'Cerrado'])->count(),
            'critical' => RequestModel::where('priority', 'Crítica')->whereNotIn('status', ['Resuelto', 'Cerrado'])->count(),
        ];

        // 2. Distribución por Estado
        $statusDistribution = RequestModel::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');

        // 3. Distribución por Prioridad
        $priorityDistribution = RequestModel::select('priority', DB::raw('count(*) as total'))
            ->groupBy('priority')
            ->pluck('total', 'priority');

        // 4. Distribución por Categoría (Haciendo JOIN para obtener el nombre)
        $categoryDistribution = DB::table('requests')
            ->join('categories', 'requests.category_id', '=', 'categories.id')
            ->select('categories.name', DB::raw('count(requests.id) as total'))
            ->groupBy('categories.id', 'categories.name')
            ->orderBy('total', 'desc')
            ->pluck('total', 'name');

        // 5. Rendimiento de Personal (Técnicos que resolvieron tickets)
        $technicianPerformance = DB::table('requests')
            ->join('users', 'requests.assigned_to', '=', 'users.id')
            ->whereIn('requests.status', ['Resuelto', 'Cerrado'])
            ->select('users.name', DB::raw('count(requests.id) as total'))
            ->groupBy('users.id', 'users.name')
            ->orderBy('total', 'desc')
            ->pluck('total', 'name');

        return view('manager.reports.index', compact(
            'kpis',
            'statusDistribution',
            'priorityDistribution',
            'categoryDistribution',
            'technicianPerformance'
        ));
    }
}
