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
            'proprietaire_id' => 'nullable|exists:proprietaires,id',
            'adresse' => 'required|string|max:255',
            'ville' => 'nullable|string|max:100',
            'surface' => 'nullable|numeric|min:0',
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
