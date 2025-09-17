<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FacetAttributesValueRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
    'facet_attribute_id' => 'required|numeric',
    'values__json__name[]\'' => 'nullable',
    'values' => 'nullable'
];
    }
}