<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Acao extends Model
{
    use HasUuids, SoftDeletes, LogsActivity;

    protected $table = 'acao';

    protected $fillable = ['id_proponente', 'id_projeto', 'titulo', 'palavras_chave', 'resumo', 'centro_departamento', 'com_bolsa', 'ods', 'data_inicio', 'data_fim', 'data_atualizacao', 'ano', 'tipo_acao', 'area_tematica', 'modalidade', 'situacao', 'status', 'img'];

    public function coordenador() : BelongsTo {
        return $this->belongsTo(User::class, 'id_proponente', 'uuid');
    }

    public function equipe() : HasMany {
        return $this->hasMany(Equipe_Acao::class, 'id_acao', 'id');
    }

    public function agenda() : HasMany {
        return $this->hasMany(Agenda_Acao::class, 'id_acao', 'id');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['id_proponente', 'id_projeto', 'titulo', 'palavras_chave', 'centro_departamento', 'com_bolsa', 'ods', 'data_inicio', 'data_fim', 'data_atualizacao', 'ano', 'tipo_acao', 'area_tematica', 'modalidade', 'situacao', 'status', 'img'])
            ->useLogName('acao')
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}