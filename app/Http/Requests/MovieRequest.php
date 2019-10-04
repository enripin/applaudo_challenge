<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Spatie\Permission\Models\Permission;

class MovieRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->user()->hasPermissionTo(Permission::findByName('movies.cud','api'));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'title' =>  'required|string',
            'description' =>  'string',
            'rental_price' =>  'required|numeric',
            'sale_price' =>  'required|numeric',
            'available' =>  'integer',
            'image' =>  'active_url'
        ];
    }
}
