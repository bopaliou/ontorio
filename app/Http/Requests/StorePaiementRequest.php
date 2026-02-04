<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePaiementRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'loyer_id' => 'required|exists:loyers,id',
            'montant' => 'required|numeric|min:0',
            'date_paiement' => 'required|date',
            'mode' => 'required|in:espèces,virement,chèque,mobile_money,carte',
            'reference' => 'nullable|string|max:100',
            'preuve' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'notes' => 'nullable|string|max:500',
        ];
    }

    /**
     * Get custom messages for validation errors.
     */
    public function messages(): array
    {
        return [
            'loyer_id.required' => 'Veuillez sélectionner un loyer.',
            'loyer_id.exists' => 'Le loyer sélectionné n\'existe pas.',
            'montant.required' => 'Le montant du paiement est obligatoire.',
            'montant.numeric' => 'Le montant doit être un nombre.',
            'montant.min' => 'Le montant ne peut pas être négatif.',
            'date_paiement.required' => 'La date de paiement est obligatoire.',
            'mode.required' => 'Le mode de paiement est obligatoire.',
            'mode.in' => 'Mode de paiement invalide.',
            'preuve.mimes' => 'Le justificatif doit être un PDF ou une image.',
            'preuve.max' => 'Le justificatif ne doit pas dépasser 5 Mo.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'loyer_id' => 'loyer',
            'date_paiement' => 'date de paiement',
            'preuve' => 'justificatif de paiement',
        ];
    }
}
