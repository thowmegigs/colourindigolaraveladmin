<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SliderRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
    'name' => 'required',
    'images_meta' => 'nullable',
    'images_meta__json__file[]\'' => 'nullable',
    'images_meta__json__show_image_only[]\'' => 'nullable',
    'images_meta__json__top_text[]\'' => 'nullable',
    'images_meta__json__top_text_color[]\'' => 'nullable',
    'images_meta__json__middle_text[]\'' => 'nullable',
    'images_meta__json__middle_text_color[]\'' => 'nullable',
    'images_meta__json__bottom_text[]\'' => 'nullable',
    'images_meta__json__bottom_text_color[]\'' => 'nullable',
    'images_meta__json__category[]\'' => 'nullable',
    'images_meta__json__collection[]\'' => 'nullable'
];
    }
}