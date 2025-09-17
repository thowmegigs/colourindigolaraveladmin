<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
    'name' => 'required',
    'description' => 'nullable|string',
    'price' => 'required|numeric',
    'sale_price' => 'required_with:price|lt:price|numeric',
    'image' => 'required|image',
    //  'image' => 'required|image|dimensions:min_width=500,min_height=300',
    // 'images.*' => 'nullable|image|dimensions:min_width=500,min_height=300',
    'quantity' => 'required|numeric',
    'max_quantity_allowed'=>'required|numeric',
    'status' => 'string|sometimes',
    'attrributes__json__id[]\'' => 'nullable',
    'attrributes' => 'nullable',
    'attrributes__json__value[]\'' => 'nullable'
];
    }
}