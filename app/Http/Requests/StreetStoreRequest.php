<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StreetStoreRequest extends FormRequest
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
            'postal_code' => 'required|digits:5',
            'name' => 'required|max:255',
            'latitude' => 'nullable|numeric|min:-90|max:90',
            'longitude' => 'nullable|numeric|min:-180|max:180',
        ];
    }
}
