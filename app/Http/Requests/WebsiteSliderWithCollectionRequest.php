<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class WebsiteSliderWithCollectionRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
    'name' => 'required',
    'repeatable__json__image[]\'' => 'nullable',
    'repeatable' => 'nullable',
    'repeatable__json__collection[]\'' => 'nullable'
];
    }
}