<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MoviePurchase extends Model
{
    protected $table = 'movies_purchases';
    protected $primaryKey = 'id_purchase';
    public $timestamps = false;

    protected $fillable = [
        'purchase_date', 'total_payment', 'id_user'
    ];

    public function purchase_details(){
        return $this->hasMany('App\Models\PurchaseDetail','id_purchase','id_purchase');
    }

    public function client(){
        return $this->belongsTo('App\Models\User','id_user','id_user');
    }
}
