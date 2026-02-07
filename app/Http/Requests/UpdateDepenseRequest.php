<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDepenseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'titre' => 'sometimes|string|max:255',
            'description' => 'nullable|string|max:2000',
            'montant' => 'sometimes|numeric|min:0.01|max:999999.99',
            'date_depense' => 'sometimes|date|before_or_equal:today',
            'categorie' => 'sometimes|in:maintenance,travaux,taxe,assurance,autre',
            'justificatif' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'statut' => 'sometimes|in:draft,payé,en_attente,rejeté',
        ];
    }
}
