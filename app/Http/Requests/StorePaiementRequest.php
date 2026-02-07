<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePaiementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'loyer_id' => 'required|exists:loyers,id',
            'montant' => 'required|numeric|min:0.01|max:999999.99',
            'mode' => 'required|in:virement,espèces,chèque,carte,mobile_money',
            'date_paiement' => 'required|date|before_or_equal:today',
            'preuve' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'reference' => 'nullable|string|max:100',
            'user_id' => 'nullable|exists:users,id',
        ];
    }

    public function messages(): array
    {
        return [
            'loyer_id.required' => 'Le loyer est obligatoire',
            'loyer_id.exists' => 'Ce loyer n\'existe pas',
            'montant.required' => 'Le montant est obligatoire',
            'montant.numeric' => 'Le montant doit être un nombre',
            'montant.min' => 'Le montant doit être supérieur à 0',
            'montant.max' => 'Le montant dépasse la limite maximale',
            'mode.required' => 'Le mode de paiement est obligatoire',
            'mode.in' => 'Mode de paiement invalide',
            'date_paiement.required' => 'La date de paiement est obligatoire',
            'date_paiement.date' => 'La date doit être valide',
            'date_paiement.before_or_equal' => 'La date ne peut pas être dans le futur',
            'preuve.mimes' => 'La preuve doit être PDF ou image (jpg, png)',
            'preuve.max' => 'La preuve ne peut pas dépasser 5 MB',
        ];
    }
}
