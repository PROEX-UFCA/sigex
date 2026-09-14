<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Opcao_Pergunta extends Model
{
    use HasUuids, SoftDeletes, LogsActivity;

    protected $table = 'opcao_pergunta';
    protected $fillable = ['id_pergunta', 'rotulo', 'valor'];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['id_pergunta', 'rotulo', 'valor'])
            ->useLogName('opcao_pergunta')
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
