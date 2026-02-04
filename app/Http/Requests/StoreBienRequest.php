<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBienRequest extends FormRequest
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
            'nom' => 'required|string|max:255',
            'adresse' => 'nullable|string|max:500',
            'ville' => 'nullable|string|max:100',
            'loyer_mensuel' => 'required|numeric|min:0',
            'type' => 'required|in:appartement,villa,studio,bureau,magasin,entrepot,autre',
            'surface' => 'nullable|numeric|min:0',
            'nombre_pieces' => 'nullable|integer|min:0',
            'meuble' => 'nullable|boolean',
            'description' => 'nullable|string|max:2000',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
        ];
    }

    /**
     * Get custom messages for validation errors.
     */
    public function messages(): array
    {
        return [
            'nom.required' => 'Le nom du bien est obligatoire.',
            'loyer_mensuel.required' => 'Le montant du loyer est obligatoire.',
            'loyer_mensuel.numeric' => 'Le loyer doit être un nombre.',
            'type.required' => 'Le type de bien est obligatoire.',
            'type.in' => 'Type de bien invalide.',
            'images.*.image' => 'Le fichier doit être une image.',
            'images.*.max' => 'L\'image ne doit pas dépasser 5 Mo.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'nom' => 'nom du bien',
            'loyer_mensuel' => 'loyer mensuel',
            'nombre_pieces' => 'nombre de pièces',
        ];
    }
}
