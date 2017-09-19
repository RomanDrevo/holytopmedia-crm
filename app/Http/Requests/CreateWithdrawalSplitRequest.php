<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateWithdrawalSplitRequest extends FormRequest
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
            "withdrawal_id"     => "required|exists:withdrawals,id",
            "split_employee_id" => "required|exists:employees,id",
            "split_amount"      => "required|numeric"
        ];
    }
}
