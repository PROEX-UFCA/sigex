<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Agenda_Acao extends Model
{
    use HasUuids, SoftDeletes;

    protected $table = 'agenda_acao';
    protected $fillable = ['id_acao', 'titulo_evento', 'data_hora_inicio', 'data_hora_fim', 'local_formato', 'descricao'];
}
