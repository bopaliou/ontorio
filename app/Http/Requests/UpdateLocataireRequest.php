<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateLocataireRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        $locataireId = $this->route('locataire')?->id;

        return [
            'nom' => 'sometimes|string|max:255',
            'email' => "sometimes|email|unique:locataires,email,{$locataireId}",
            'telephone' => 'sometimes|string|max:20',
            'adresse' => 'nullable|string|max:500',
            'pieces_identite' => 'sometimes|string|max:50',
            'profession' => 'nullable|string|max:100',
            'revenus_mensuels' => 'nullable|numeric|min:0|max:999999.99',
        ];
    }
}
