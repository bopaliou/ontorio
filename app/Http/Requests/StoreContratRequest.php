<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreContratRequest extends FormRequest
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
            'bien_id' => 'required|exists:biens,id',
            'locataire_id' => 'required|exists:locataires,id',
            'date_debut' => 'required|date',
            'date_fin' => 'nullable|date|after:date_debut',
            'loyer_montant' => 'required|numeric|min:0',
            'caution' => 'nullable|numeric|min:0',
            'frais_dossier' => 'nullable|numeric|min:0',
            'type_bail' => 'nullable|in:habitation,commercial,professionnel,mixte',
            'statut' => 'nullable|in:actif,en_attente,résilié,expiré',
            'date_signature' => 'nullable|date',
            'renouvellement_auto' => 'nullable|boolean',
            'preavis_mois' => 'nullable|integer|min:1|max:12',
        ];
    }

    /**
     * Get custom messages for validation errors.
     */
    public function messages(): array
    {
        return [
            'bien_id.required' => 'Veuillez sélectionner un bien.',
            'bien_id.exists' => 'Le bien sélectionné n\'existe pas.',
            'locataire_id.required' => 'Veuillez sélectionner un locataire.',
            'locataire_id.exists' => 'Le locataire sélectionné n\'existe pas.',
            'date_debut.required' => 'La date de début est obligatoire.',
            'date_fin.after' => 'La date de fin doit être postérieure à la date de début.',
            'loyer_montant.required' => 'Le montant du loyer est obligatoire.',
            'loyer_montant.numeric' => 'Le loyer doit être un nombre.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'bien_id' => 'bien',
            'locataire_id' => 'locataire',
            'date_debut' => 'date de début',
            'date_fin' => 'date de fin',
            'loyer_montant' => 'loyer mensuel',
            'type_bail' => 'type de bail',
            'preavis_mois' => 'délai de préavis',
        ];
    }
}
