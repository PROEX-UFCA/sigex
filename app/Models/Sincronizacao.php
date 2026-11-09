<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sincronizacao extends Model
{
    protected $table = 'sincronizacao';

    protected $fillable = [
        'status',
        'etapa_atual',
        'iniciado_em',
        'finalizado_em',
        'resultados',
        'erro',
        'user_id',
    ];

    protected function casts(): array
    {
        return [
            'iniciado_em' => 'datetime',
            'finalizado_em' => 'datetime',
            'resultados' => 'array',
        ];
    }
}
