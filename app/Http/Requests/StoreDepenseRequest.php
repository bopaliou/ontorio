<?php

namespace App\Http\Requests;

class StoreDepenseRequest extends AuthenticatedApiFormRequest
{
    public function rules(): array
    {
        return [
            'bien_id' => 'required|exists:biens,id',
            'titre' => 'required|string|max:255',
            'description' => 'nullable|string|max:2000',
            'montant' => 'required|numeric|min:0.01|max:999999.99',
            'date_depense' => 'required|date|before_or_equal:today',
            'categorie' => 'required|in:maintenance,travaux,taxe,assurance,autre',
            'justificatif' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'statut' => 'required|in:draft,payé,en_attente,rejeté',
        ];
    }

    public function messages(): array
    {
        return [
            'bien_id.required' => 'Le bien est obligatoire',
            'titre.required' => 'Le titre de la dépense est obligatoire',
            'montant.required' => 'Le montant est obligatoire',
            'montant.numeric' => 'Le montant doit être un nombre',
            'montant.min' => 'Le montant doit être supérieur à 0',
            'date_depense.required' => 'La date de la dépense est obligatoire',
            'categorie.required' => 'La catégorie est obligatoire',
            'justificatif.mimes' => 'Le justificatif doit être PDF ou image',
            'justificatif.max' => 'Le justificatif ne peut pas dépasser 10 MB',
        ];
    }
}
