<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MovieRental extends Model
{
    protected $table = 'movies_rentals';
    protected $primaryKey = 'id_rental';

    public function movie(){
        return $this->belongsTo('App\Models\Movie','id_movie','id_movie');
    }

    public function client(){
        return $this->belongsTo('App\Models\User','id_user','id_user');
    }
}
