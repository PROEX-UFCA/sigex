<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Acao extends Model
{
    use HasUuids, SoftDeletes;

    protected $table = 'acao';
    protected $fillable = ['id_coordenador', 'id_atividade', 'id_projeto', 'titulo', 'centro_departamento', 'data_inicio', 'data_fim', 'ano', 'tipo_acao', 'area_tematica', 'modalidade', 'status'];
}
