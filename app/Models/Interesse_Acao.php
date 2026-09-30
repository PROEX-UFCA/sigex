<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Interesse_Acao extends Model
{
    use HasUuids, SoftDeletes, LogsActivity;

    protected $fillable = ['id_instituicao', 'id_acao'];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['id_instituicao', 'id_acao'])
            ->useLogName('interesse_acao')
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
