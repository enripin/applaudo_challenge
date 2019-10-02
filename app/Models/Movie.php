<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Movie extends Model
{
    protected $table = 'movies';
    protected $primaryKey = 'id_movie';

    protected $fillable = [
        'title', 'description', 'sale_price', 'rental_price', 'available', 'stock'
    ];

    public function scopeAvailable($query,$value)
    {
        if($value){
            return $query->where('available', '=', 1);
        }else{
            return $query->where('available', '=', 0);
        }
    }

    public function likes(){
        return $this->belongsToMany('App\Models\User', 'users_likes', 'id_movie', 'id_user');
    }
}
