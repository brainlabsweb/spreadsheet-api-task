<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SpreadsheetRequestForm extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if(auth()->check()) {
            return true;
        }
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'spreadsheet' => 'required|min:3|max:150'
        ];
    }
}
