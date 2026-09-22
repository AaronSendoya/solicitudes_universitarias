<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ComentarioSolicitud extends Model
{
    protected $table = 'comentarios_solicitud';

    const CREATED_AT = 'fecha_comentario';
    const UPDATED_AT = null;

    protected $fillable = [
        'solicitud_id',
        'usuario_autor_id',
        'texto_comentario',
    ];

    protected function casts(): array
    {
        return [
            'fecha_comentario' => 'datetime',
        ];
    }

    public function solicitud(): BelongsTo
    {
        return $this->belongsTo(Solicitud::class);
    }

    public function autor(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'usuario_autor_id');
    }
}
