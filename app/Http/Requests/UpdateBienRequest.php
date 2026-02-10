<?php

namespace App\Http\Requests;

use App\Http\Requests\Traits\BienValidationRules;
use Illuminate\Foundation\Http\FormRequest;

class UpdateBienRequest extends FormRequest
{
    use BienValidationRules;

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
        return array_merge($this->bienRules(), [
            'adresse' => 'nullable|string|max:500',
            'ville' => 'nullable|string|max:100',
        ]);
    }

    /**
     * Get custom messages for validation errors.
     */
    public function messages(): array
    {
        return $this->bienMessages();
    }
}
