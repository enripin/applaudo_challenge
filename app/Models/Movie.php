<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Movie extends Model
{
    protected $table = 'movies';
    protected $primaryKey = 'id_movie';

    public function scopeAvailable($query)
    {
        return $query->where('availability', '>', 0);
    }

    public function likes(){
        return $this->belongsToMany('App\Models\Users', 'user_likes', 'id_movie', 'id_user');
    }
}
