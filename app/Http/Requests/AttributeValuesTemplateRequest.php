<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AttributeValuesTemplateRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
    'name' => 'required',
    'values_json__json__name[]\'' => 'nullable',
    'values_json' => 'nullable'
];
    }
}