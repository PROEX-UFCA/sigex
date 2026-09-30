<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Agenda_Acao extends Model
{
    use HasUuids, SoftDeletes, LogsActivity;

    protected $table = 'agenda_acao';
    protected $fillable = ['id_acao', 'titulo_evento', 'data_hora_inicio', 'data_hora_fim', 'local_formato', 'descricao'];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['id_acao', 'titulo_evento', 'data_hora_inicio', 'data_hora_fim', 'local_formato', 'descricao'])
            ->useLogName('agenda_acao')
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
    public function acao()
    {
        return $this->belongsTo(Acao::class, 'id_acao');
    }
}
