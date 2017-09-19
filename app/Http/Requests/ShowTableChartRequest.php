<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ShowTableChartRequest extends FormRequest
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
            "startDate"      =>  "required",
            "endDate"   =>  "required",
            "tableId"   => "required|numeric|exists:tables,id"
        ];
    }
}
