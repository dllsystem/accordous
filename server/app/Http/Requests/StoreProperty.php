<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProperty extends FormRequest
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
            'owner_name' => 'required|min:3|max:255',
            'owner_email' => 'required|regex:/(.+)@(.+)\.(.+)/i',
            'address_street' => 'required|min:3|max:255',
            'address_neighborhood' => 'required|max:255',
            'address_city' => 'required|min:3|max:255',
            'address_state' => 'required|min:2|max:255',
        ];
    }
}
