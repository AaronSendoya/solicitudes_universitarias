<?php

namespace App\Http\Controllers;

use App\Models\Request as RequestModel;
use App\Models\ActionHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TechnicianRequestController extends Controller
{
    /**
     * Bandeja de entrada del Técnico.
     */
    public function index()
    {
        // Solo obtener las solicitudes asignadas al técnico autenticado
        $requests = RequestModel::where('assigned_to', Auth::id())
            ->with(['user', 'category'])
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

        return view('technician.requests.index', compact('requests'));
    }

    /**
     * Muestra el detalle de una solicitud asignada al técnico.
     */
    public function show($id)
    {
        // Aseguramos que la solicitud pertenece al técnico antes de mostrarla
        $requestModel = RequestModel::where('assigned_to', Auth::id())
            ->with(['user', 'category', 'evidences', 'actionHistories.user' => function($query) {
                $query->orderBy('created_at', 'desc');
            }])
            ->findOrFail($id);

        return view('technician.requests.show', compact('requestModel'));
    }

    /**
     * Permite al técnico actualizar el estado y agregar comentarios de resolución.
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'status'   => 'required|in:En Proceso,Resuelto',
            'comments' => 'required|string|min:5|max:2000',
        ]);

        DB::transaction(function () use ($validated, $id) {
            // Re-validar propiedad de la solicitud
            $requestModel = RequestModel::where('assigned_to', Auth::id())->findOrFail($id);

            $oldStatus = $requestModel->status;
            $newStatus = $validated['status'];

            // Actualizar el estado
            $requestModel->update([
                'status' => $newStatus,
            ]);

            // Registrar el cambio de estado si hubo cambio
            if ($oldStatus !== $newStatus) {
                ActionHistory::create([
                    'request_id'  => $requestModel->id,
                    'user_id'     => Auth::id(),
                    'action_type' => 'status_change',
                    'old_value'   => $oldStatus,
                    'new_value'   => $newStatus,
                    'comments'    => null, // Lo guardaremos separado como un registro de comentario técnico o junto, pero aquí como cambio de estado.
                ]);
            }

            // Registrar el comentario y acciones tomadas (siempre se registra ya que es obligatorio)
            ActionHistory::create([
                'request_id'  => $requestModel->id,
                'user_id'     => Auth::id(),
                'action_type' => 'comment',
                'old_value'   => null,
                'new_value'   => null,
                'comments'    => $validated['comments'],
            ]);
        });

        return redirect()->back()->with('success', 'Actualización registrada correctamente.');
    }
}
