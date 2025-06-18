<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class NewCouponRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
    'code' => 'required',
    'short_description' => 'required',
    'description' => 'nullable',
    'cart_amount' => 'required|numeric',
    'discount' => 'required|numeric',
    'start_date' => 'required',
    'end_date' => 'required'
];
    }
}