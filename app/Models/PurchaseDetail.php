<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseDetail extends Model
{
    protected $table = 'purchases_details';
    protected $primaryKey = 'id_detail';
    public $timestamps = false;

    protected $fillable = [
        'id_purchase', 'id_movie', 'number', 'unity_price'
    ];

    public function movie(){
        return $this->belongsTo('App\Models\Movie','id_movie','id_movie');
    }

    public function purchase(){
        return $this->belongsTo('App\Models\MoviePurchase','id_purchase','id_purchase');
    }
}
