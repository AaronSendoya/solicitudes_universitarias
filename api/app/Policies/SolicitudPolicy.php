<?php

namespace App\Policies;

use App\Models\EstadoSolicitud;
use App\Models\Solicitud;
use App\Models\Usuario;

class SolicitudPolicy
{
    public function view(Usuario $usuario, Solicitud $solicitud): bool
    {
        return match ($usuario->rol?->nombre) {
            'Administrativo' => true,
            'Tecnico' => $solicitud->usuario_responsable_actual_id === $usuario->id
                || $solicitud->asignaciones()->where('usuario_asignado_id', $usuario->id)->exists(),
            default => $solicitud->usuario_solicitante_id === $usuario->id,
        };
    }

    public function create(Usuario $usuario): bool
    {
        return $usuario->esEstudiante();
    }

    public function update(Usuario $usuario, Solicitud $solicitud): bool
    {
        if ($usuario->esAdministrativo()) {
            return true;
        }

        return $solicitud->usuario_solicitante_id === $usuario->id
            && $solicitud->estado?->nombre === EstadoSolicitud::ABIERTA;
    }

    public function delete(Usuario $usuario, Solicitud $solicitud): bool
    {
        if ($usuario->esAdministrativo()) {
            return true;
        }

        return $solicitud->usuario_solicitante_id === $usuario->id
            && $solicitud->estado?->nombre === EstadoSolicitud::ABIERTA;
    }

    public function clasificar(Usuario $usuario): bool
    {
        return $usuario->esAdministrativo();
    }

    public function viewReportes(Usuario $usuario): bool
    {
        return $usuario->esAdministrativo();
    }

    public function asignar(Usuario $usuario): bool
    {
        return $usuario->esAdministrativo();
    }

    public function cambiarEstado(Usuario $usuario, Solicitud $solicitud): bool
    {
        if ($usuario->esAdministrativo()) {
            return true;
        }

        return $usuario->esTecnico() && $solicitud->usuario_responsable_actual_id === $usuario->id;
    }

    public function comentar(Usuario $usuario, Solicitud $solicitud): bool
    {
        return $this->view($usuario, $solicitud);
    }

    public function adjuntar(Usuario $usuario, Solicitud $solicitud): bool
    {
        return $this->view($usuario, $solicitud);
    }
}
