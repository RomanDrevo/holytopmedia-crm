<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CustomerGetDodRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'customer_id'   =>  'required|exists:customers,id',
            'form_id'       =>  'required|numeric|exists:forms,id',
            'access_code'   =>  'required',
        ];
    }

    // OPTIONAL OVERRIDE
    public function response(array $errors)
    {
        dd("Unauthorized request!!");
        // abort(403, 'Unauthorized request!!');
    }
}
