<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Localidade_Acao extends Model
{
    use HasUuids, SoftDeletes, LogsActivity;

    protected $table = 'localidade_acao';
    protected $fillable = ['id_projeto', 'cidade', 'estado', 'sim', 'area_tematica'];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['id_projeto', 'cidade', 'estado', 'sim', 'area_tematica'])
            ->useLogName('localidade_acao')
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
