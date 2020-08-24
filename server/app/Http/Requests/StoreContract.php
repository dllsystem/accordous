<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreContract extends FormRequest
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
            'tenant_document_type' => 'required',
            'tenant_document' => 'required|cpf_cnpj',
            'tenant_name' => 'required:min:3',
            'tenant_email' => 'required|regex:/(.+)@(.+)\.(.+)/i',
            'property_id' => 'required',
        ];
    }
}