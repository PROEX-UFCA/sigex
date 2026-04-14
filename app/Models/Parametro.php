<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Parametro extends Model
{
    use HasUuids, SoftDeletes, LogsActivity;

    protected $fillable = ['function', 'value', 'status'];
    protected $table = 'parametro';

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['function', 'value', 'status'])
            ->useLogName('parametros')
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
