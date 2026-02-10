<?php

namespace App\Http\Requests\Traits;

trait BienValidationRules
{
    /**
     * Shared validation rules for Bien (create & update).
     */
    protected function bienRules(): array
    {
        return [
            'nom' => 'required|string|max:255',
            'type' => 'required|in:studio,appartement,maison,villa,immeuble,commercial,bureau,magasin,entrepot,autre',
            'loyer_mensuel' => 'required|numeric|min:0',
            'surface' => 'nullable|numeric|min:0',
            'nombre_pieces' => 'nullable|integer|min:0',
            'meuble' => 'nullable|boolean',
            'description' => 'nullable|string|max:2000',
            'images.*' => 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:5120',
        ];
    }

    /**
     * Shared validation messages for Bien.
     */
    protected function bienMessages(): array
    {
        return [
            'nom.required' => 'Le nom du bien est obligatoire.',
            'type.required' => 'Le type de bien est obligatoire.',
            'type.in' => 'Le type de bien sélectionné est invalide.',
            'loyer_mensuel.required' => 'Le montant du loyer est obligatoire.',
            'loyer_mensuel.numeric' => 'Le loyer doit être un nombre.',
            'loyer_mensuel.min' => 'Le loyer ne peut pas être négatif.',
            'surface.min' => 'La surface doit être au moins de 1 m².',
            'images.*.image' => 'Les fichiers doivent être des images.',
            'images.*.max' => 'Chaque image ne doit pas dépasser 5 Mo.',
        ];
    }
}
