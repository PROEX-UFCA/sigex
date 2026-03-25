<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Instituicao_Externa_Agenda extends Model
{
    use HasUuids, SoftDeletes;

    protected $fillable = ['id_instituicao', 'data_disponivel', 'hora_inicio', 'hora_fim', 'observacao'];
}
