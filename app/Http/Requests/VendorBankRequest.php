<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VendorBankRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
    'vendor_id' => 'required|numeric',
    'bank_name' => 'required',
    'account_number' => 'required|numeric',
    'account_holder' => 'required',
    'ifsc_code' => 'required',
    'branch_name' => 'nullable'
];
    }
}