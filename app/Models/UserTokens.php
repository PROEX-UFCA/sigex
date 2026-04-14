<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class UserTokens extends Model
{
    use HasUuids, SoftDeletes, LogsActivity;
    
    protected $fillable = [
        'user_uuid',
        'type'
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['user_uuid', 'type'])
            ->useLogName('user_tokens')
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
