<?php

namespace App\Http\Requests;

class StoreLocataireRequest extends BaseApiFormRequest
{
    protected bool $requiresAuthentication = true;

class StoreLocataireRequest extends AuthenticatedApiFormRequest
{
    public function rules(): array
    {
        return [
            'nom' => 'required|string|max:255',
            'email' => 'required|email|unique:locataires,email',
            'telephone' => 'required|string|max:20',
            'adresse' => 'nullable|string|max:500',
            'pieces_identite' => 'required|string|max:50',
            'profession' => 'nullable|string|max:100',
            'revenus_mensuels' => 'nullable|numeric|min:0|max:999999.99',
        ];
    }

    public function messages(): array
    {
        return [
            'nom.required' => 'Le nom du locataire est obligatoire',
            'email.required' => 'L\'email est obligatoire',
            'email.email' => 'L\'email doit être valide',
            'email.unique' => 'Ce locataire existe déjà',
            'telephone.required' => 'Le téléphone est obligatoire',
            'pieces_identite.required' => 'La pièce d\'identité est obligatoire',
            'revenus_mensuels.numeric' => 'Les revenus doivent être un nombre',
        ];
    }
}
