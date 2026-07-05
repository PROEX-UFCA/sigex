<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Equipe_Acao extends Model
{
    use HasUuids, SoftDeletes, LogsActivity;

    protected $table = "equipe_acao";
    protected $fillable = ['id_acao', 'id_usuario', 'id_projeto', 'id_pessoa', 'tipo_membro', 'categoria_membro', 'status', 'data_inicio', 'data_fim', 'tipo_vinculo'];

    public function action() :HasOne{
        return $this->hasOne(Acao::class, 'id', 'id_acao');
    }

    public function user() :HasOne{
        return $this->hasOne(User::class, 'uuid', 'id_usuario');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['id_acao', 'id_usuario', 'id_projeto', 'id_pessoa', 'tipo_membro', 'categoria_membro', 'status', 'data_inicio', 'data_fim', 'tipo_vinculo'])
            ->useLogName('equipe_acao')
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}