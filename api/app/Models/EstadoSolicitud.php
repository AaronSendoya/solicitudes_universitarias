<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EstadoSolicitud extends Model
{
    public const ABIERTA = 'Abierta';
    public const EN_PROCESO = 'En Proceso';
    public const PENDIENTE_REVISION = 'Pendiente de Revisión';
    public const CERRADA = 'Cerrada';

    protected $table = 'estados_solicitud';

    protected $fillable = ['nombre'];
}
