<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MovieReturn extends Model
{
    protected $table = 'movies_returns';
    protected $primaryKey = 'id_return';

    public function rental(){
        return $this->belongsTo('App\Models\MovieRent','id_rental','id_rental');
    }
}
