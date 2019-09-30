<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VerifyEmail extends Model
{
    protected $table = 'verify_emails';
    protected $primaryKey = 'id_verification';

    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'id_user', 'id_user');
    }

}
