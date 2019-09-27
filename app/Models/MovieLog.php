<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MovieLog extends Model
{
    protected $table = 'movies_log';
    protected $primaryKey = 'id_movie_log';

    public function movie(){
        return $this->belongsTo('App\Models\Movie','id_movie','id_movie');
    }

    public function user(){
        return $this->belongsTo('App\Models\User','id_user','id_user');
    }
}
