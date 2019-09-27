<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseDetail extends Model
{
    protected $table = 'purchases_details';
    protected $primaryKey = 'id_detail';

    public function movie(){
        return $this->belongsTo('App\Models\Movie','id_movie','id_movie');
    }
}
