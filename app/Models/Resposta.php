<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Resposta extends Model
{
    use HasUuids, LogsActivity;
    
    protected $table = 'resposta'; 
    protected $fillable = ['id_submissao', 'id_pergunta', 'valor', 'indice_grupo'];

    public function validacao() :HasOne{
        return $this->hasOne(ValidacaoResposta::class, 'id_resposta', 'id');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['id_submissao', 'id_pergunta', 'valor', 'indice_grupo'])
            ->useLogName('resposta')
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
