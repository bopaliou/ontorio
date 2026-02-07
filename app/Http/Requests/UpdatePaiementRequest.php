<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePaiementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'montant'       => 'sometimes|numeric|min:0.01|max:999999.99',
            'mode'          => 'sometimes|in:virement,espèces,chèque,carte,mobile_money',
            'date_paiement' => 'sometimes|date|before_or_equal:today',
            'preuve'        => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'reference'     => 'nullable|string|max:100',
        ];
    }

    public function messages(): array
    {
        return [
            'montant.numeric' => 'Le montant doit être un nombre',
            'montant.min' => 'Le montant doit être supérieur à 0',
            'mode.in' => 'Mode de paiement invalide',
            'preuve.mimes' => 'La preuve doit être PDF ou image',
            'preuve.max' => 'La preuve ne peut pas dépasser 5 MB',
        ];
    }
}
