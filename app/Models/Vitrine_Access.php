<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vitrine_Access extends Model
{
    protected $table = 'vitrine_access';

    protected $fillable = ['ip_address', 'user_agent', 'user_id'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
