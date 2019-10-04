<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PurchaseResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id_purchase'=>$this->id_purchase,
            'purchase_date'=>$this->purchase_date,
            'total_payment'=>round($this->total_payment,2),
            'id_user'=>$this->id_user,
            'details'=>$this->purchase_details
        ];
    }
}
