<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MovieResource extends JsonResource
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
            'id_movie'=>$this->id_movie,
            'title'=>$this->title,
            'description'=>$this->description,
            'rental_price'=>$this->rental_price,
            'sale_price'=>$this->sale_price,
            'availability'=>$this->availability,
            'likes'=>$this->likes()->count()
        ];
    }
}
