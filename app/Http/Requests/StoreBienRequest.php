<?php

namespace App\Http\Requests;

use App\Http\Requests\Traits\BienValidationRules;

class StoreBienRequest extends BaseApiFormRequest
{
    use BienValidationRules;

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return array_merge($this->bienRules(), [
            'proprietaire_id' => 'required|exists:proprietaires,id',
            'adresse' => 'required|string|max:255',
            'ville' => 'required|string|max:100',
            'surface' => 'required|numeric|min:1',
        ]);
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return $this->bienMessages();
    }
}
