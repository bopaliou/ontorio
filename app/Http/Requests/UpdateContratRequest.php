<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateContratRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'bien_id'               => 'sometimes|exists:biens,id',
            'locataire_id'          => 'sometimes|exists:locataires,id',
            'date_debut'            => 'sometimes|date',
            'date_fin'              => 'sometimes|date',
            'loyer_montant'         => 'sometimes|numeric|min:0.01|max:999999.99',
            'statut'                => 'sometimes|in:actif,résilié,suspendu,expiré',
            'caution'               => 'nullable|numeric|min:0|max:999999.99',
            'frais_dossier'         => 'nullable|numeric|min:0|max:999999.99',
            'type_bail'             => 'sometimes|in:location,colocation,meublé,commercial',
            'date_signature'        => 'nullable|date|before_or_equal:today',
            'renouvellement_auto'   => 'nullable|boolean',
            'preavis_mois'          => 'nullable|integer|min:1|max:12',
        ];
    }
}
