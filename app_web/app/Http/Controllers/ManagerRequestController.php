<?php

namespace App\Http\Controllers;

use App\Models\Request as RequestModel;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ManagerRequestController extends Controller
{
    /**
     * Bandeja de entrada del Gestor.
     */
    public function index()
    {
        // Obtener todas las solicitudes con sus relaciones
        // Ordenadas primero por prioridad (Crítica > Alta > Media > Baja) y luego por fecha.
        // Como 'prioridad' es un string, podemos usar orderByRaw o simplemente obtenerlas y ordenar en colección,
        // o usar una lógica simple orderBy('created_at', 'desc') por ahora para no complicar el SQL de strings.
        // Sin embargo, para cumplir con el requerimiento, usemos orderByRaw si es PostgreSQL:
        $requests = RequestModel::with(['user', 'category', 'assignedTo'])
            ->orderByRaw("
                CASE priority
                    WHEN 'Crítica' THEN 1
                    WHEN 'Alta' THEN 2
                    WHEN 'Media' THEN 3
                    WHEN 'Baja' THEN 4
                    ELSE 5
                END ASC
            ")
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        // Obtener los técnicos activos para poder asignarlos desde la vista si es necesario
        $technicians = User::role('Technician')->where('is_active', true)->get();

        return view('manager.requests.index', compact('requests', 'technicians'));
    }

    /**
     * Muestra el detalle de una solicitud para gestionarla.
     */
    public function show($id)
    {
        $requestModel = RequestModel::with(['user', 'category', 'assignedTo', 'evidences', 'actionHistories.user' => function($query) {
            $query->orderBy('created_at', 'desc');
        }])->findOrFail($id);

        $technicians = User::role('Technician')->where('is_active', true)->get();

        return view('manager.requests.show', compact('requestModel', 'technicians'));
    }

    /**
     * Actualiza el técnico asignado, la prioridad y el estado.
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'assigned_to' => 'nullable|exists:users,id',
            'priority'    => 'required|in:Baja,Media,Alta,Crítica',
            'status'      => 'required|in:Pendiente,Asignado,En Proceso,Resuelto,Cerrado',
        ]);

        DB::transaction(function () use ($validated, $id) {
            $requestModel = RequestModel::findOrFail($id);

            // Si se asigna un técnico y el estado era Pendiente, pasarlo automáticamente a Asignado
            if ($validated['assigned_to'] && $requestModel->status === 'Pendiente' && $validated['status'] === 'Pendiente') {
                $validated['status'] = 'Asignado';
            }

            // Detectar cambios
            $changes = [];
            
            if ($requestModel->status !== $validated['status']) {
                $changes[] = [
                    'action_type' => 'status_change',
                    'old_value' => $requestModel->status,
                    'new_value' => $validated['status']
                ];
            }

            if ($requestModel->priority !== $validated['priority']) {
                $changes[] = [
                    'action_type' => 'priority_change',
                    'old_value' => $requestModel->priority,
                    'new_value' => $validated['priority']
                ];
            }

            if ($requestModel->assigned_to != $validated['assigned_to']) {
                $oldAssignee = $requestModel->assigned_to ? User::find($requestModel->assigned_to)->name : 'Nadie';
                $newAssignee = $validated['assigned_to'] ? User::find($validated['assigned_to'])->name : 'Nadie';
                
                $changes[] = [
                    'action_type' => 'assignment',
                    'old_value' => $oldAssignee,
                    'new_value' => $newAssignee
                ];
            }

            // Actualizar modelo
            $requestModel->update([
                'assigned_to' => $validated['assigned_to'],
                'priority'    => $validated['priority'],
                'status'      => $validated['status'],
            ]);

            // Registrar historial
            foreach ($changes as $change) {
                \App\Models\ActionHistory::create([
                    'request_id'  => $requestModel->id,
                    'user_id'     => \Illuminate\Support\Facades\Auth::id(),
                    'action_type' => $change['action_type'],
                    'old_value'   => $change['old_value'],
                    'new_value'   => $change['new_value'],
                    'comments'    => 'Actualización desde el panel de gestión.',
                ]);
            }
        });

        return redirect()->back()->with('success', 'Solicitud actualizada correctamente.');
    }
}
