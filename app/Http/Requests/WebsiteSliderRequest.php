<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class WebsiteSliderRequest extends FormRequest
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
                    'images.*' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
                    'collection_id.*' => 'nullable',
                    
                ];
        }
        else{
            return [
                'name' => 'required',
                'images.*' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
                'collection_id.*' => 'nullable',
                
            ];
        }
    }
}