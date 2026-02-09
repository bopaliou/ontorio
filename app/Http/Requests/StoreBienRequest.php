<?php

namespace App\Http\Requests;

use App\Traits\HandlesApiValidation;
use Illuminate\Foundation\Http\FormRequest;

class StoreBienRequest extends FormRequest
{
    use HandlesApiValidation;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'proprietaire_id' => 'required|exists:proprietaires,id',
            'nom' => 'required|string|max:255',
            'adresse' => 'required|string|max:255',
            'ville' => 'required|string|max:100',
            'type' => 'required|in:studio,appartement,maison,villa,immeuble,commercial,autre',
            'surface' => 'required|numeric|min:1',
            'loyer_mensuel' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'nombre_pieces' => 'nullable|integer|min:0',
            'meuble' => 'nullable|boolean',
            'images.*' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'type.in' => 'Le type de bien sélectionné est invalide.',
            'surface.min' => 'La surface doit être au moins de 1 m².',
            'loyer_mensuel.min' => 'Le loyer ne peut pas être négatif.',
            'images.*.image' => 'Les fichiers doivent être des images.',
            'images.*.max' => 'Chaque image ne doit pas dépasser 5 Mo.',
        ];
    }
}
