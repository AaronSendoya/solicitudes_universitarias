<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HistorialSolicitud extends Model
{
    protected $table = 'historial_solicitud';

    const CREATED_AT = 'fecha_cambio';
    const UPDATED_AT = null;

    protected $fillable = [
        'solicitud_id',
        'usuario_cambio_id',
        'campo_cambiado',
        'valor_anterior',
        'valor_nuevo',
    ];

    protected function casts(): array
    {
        return [
            'fecha_cambio' => 'datetime',
        ];
    }

    public function solicitud(): BelongsTo
    {
        return $this->belongsTo(Solicitud::class);
    }

    public function usuarioCambio(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'usuario_cambio_id');
    }
}
