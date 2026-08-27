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

    protected $fillable = ['id_projeto', 'ano', 'titulo', 'modalidade_edital', 'bolsas_solicitadas', 'bolsas_concedidas', 'financiamento_interno', 'financiamento_externo', 'situacao', 'data_cadastro', 'data_inicio', 'data_fim', 'data_atualizacao', 'centro_departamento_sigla', 'tipo_acao', 'area_tematica', 'resumo', 'palavras_chave', 'ods', 'contexto', 'status', 'img'];

    public function coordenador() : BelongsTo {
        return $this->belongsTo(User::class, 'id_proponente', 'uuid');
    }

    public function equipe() : HasMany {
        return $this->hasMany(Equipe_Acao::class, 'id_acao', 'id');
    }

    public function agenda() : HasMany {
        return $this->hasMany(Agenda_Acao::class, 'id_acao', 'id');
    }

    public function agendaInterna()
    {
        return $this->hasMany(AgendaInternaAcao::class, 'id_acao', 'id');
    }

    public function submissoes() : HasMany {
        return $this->hasMany(Submissao::class, 'id_acao', 'id');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['id_projeto', 'ano', 'titulo', 'modalidade_edital', 'bolsas_solicitadas', 'financiamento_interno', 'financiamento_externo', 'situacao', 'data_cadastro', 'data_inicio', 'data_fim', 'data_atualizacao', 'centro_departamento_sigla', 'tipo_acao', 'area_tematica', 'resumo', 'palavras_chave', 'ods', 'contexto', 'status', 'img'])
            ->useLogName('acao')
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}