<?php

namespace App\Http\Requests;

class StoreContratRequest extends BaseApiFormRequest
{
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
            'statut' => 'required|in:actif,résilié,expiré,en_attente',
            'caution' => 'nullable|numeric|min:0',
            'frais_dossier' => 'nullable|numeric|min:0',
            'type_bail' => 'required|string|max:100',
            'date_signature' => 'nullable|date|before_or_equal:today',
            'renouvellement_auto' => 'nullable|boolean',
            'preavis_mois' => 'nullable|integer|min:0',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'date_fin.after' => 'La date de fin doit être postérieure à la date de début.',
            'loyer_montant.min' => 'Le montant du loyer ne peut pas être négatif.',
            'date_signature.before_or_equal' => 'La date de signature ne peut pas être dans le futur.',
            'statut.in' => 'Le statut sélectionné est invalide.',
        ];
    }

    /**
     * Additional business logic after standard validation.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            try {
                $bienId = $this->input('bien_id');
                if ($bienId) {
                    $isOccupied = \App\Models\Bien::where('id', $bienId)
                        ->where('statut', 'occupé')
                        ->whereHas('contrats', function ($q) {
                            $q->whereIn('statut', ['actif', 'en_attente']);
                        })->exists();

                    if ($isOccupied) {
                        $validator->errors()->add('bien_id', 'Ce bien est déjà occupé par un contrat actif ou réservé.');
                    }
                }
            } catch (\Throwable $e) {
                file_put_contents('debug_request.txt', $e->getMessage()."\n".$e->getTraceAsString());
            }
        });
    }
}
