<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VideoRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

     public function rules()
    {
        if($this->method()=='POST'){
        return [
                    'name' => 'required',
                    'images.*' => 'required',
                    'collection_id.*' => 'nullable',
                    
                ];
        }
        else{
            return [
                'name' => 'required',
                'images.*' => 'nullable',
                'collection_id.*' => 'nullable',
                
            ];
        }
    }
}