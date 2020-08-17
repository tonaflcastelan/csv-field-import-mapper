<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class CsvRequestPost extends FormRequest
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
            'csv_file' => 'required'    
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $json = [
            'success' => false,
            'errors' => ['message' => $validator->errors()]
        ];
        throw new HttpResponseException(response()->json($json, 400));
    }

}
