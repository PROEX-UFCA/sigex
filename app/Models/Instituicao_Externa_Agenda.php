<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Instituicao_Externa_Agenda extends Model
{
    use HasUuids, SoftDeletes, LogsActivity;

    protected $fillable = ['id_instituicao', 'data_disponivel', 'hora_inicio', 'hora_fim', 'observacao'];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['id_instituicao', 'data_disponivel', 'hora_inicio', 'hora_fim', 'observacao'])
            ->useLogName('instituicao_externa_agenda')
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
