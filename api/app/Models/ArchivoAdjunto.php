<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ArchivoAdjunto extends Model
{
    protected $table = 'archivos_adjuntos';

    const CREATED_AT = 'fecha_subida';
    const UPDATED_AT = null;

    protected $fillable = [
        'solicitud_id',
        'usuario_subidor_id',
        'nombre_archivo',
        'url_archivo',
        'tipo_archivo',
        'tamano_bytes',
    ];

    protected function casts(): array
    {
        return [
            'fecha_subida' => 'datetime',
        ];
    }

    public function solicitud(): BelongsTo
    {
        return $this->belongsTo(Solicitud::class);
    }

    public function subidoPor(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'usuario_subidor_id');
    }
}
