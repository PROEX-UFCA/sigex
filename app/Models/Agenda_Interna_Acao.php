<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Agenda_Interna_Acao extends Model
{
    use SoftDeletes, HasUuids;

    protected $table = 'agenda_interna_acao';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id_acao',
        'titulo_evento',
        'data_hora_inicio',
        'data_hora_fim',
        'local_formato',
        'descricao',
        'pauta_interna'
    ];

    public function acao()
    {
        return $this->belongsTo(Acao::class, 'id_acao', 'id');
    }
}