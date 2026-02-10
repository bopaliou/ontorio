<?php

namespace App\Http\Requests;

use App\Models\Loyer;
use App\Traits\HandlesApiValidation;
use Illuminate\Foundation\Http\FormRequest;

class StorePaiementRequest extends FormRequest
{
    use HandlesApiValidation;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Permission check is already handled by role middleware in routes,
        // but we can add more granular check here if needed.
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'loyer_id' => 'required|exists:loyers,id',
            'montant' => [
                'required',
                'numeric',
                'min:1',
                function ($value, $fail) {
                    $loyer = Loyer::withSum('paiements', 'montant')->find($this->loyer_id);
                    if ($loyer) {
                        $due = $loyer->montant + ($loyer->penalite ?? 0) - ($loyer->paiements_sum_montant ?? 0);
                        if ($value > $due + 0.01) { // Allowing tiny rounding difference
                            $fail("Le montant ($value) excède le reste à payer (".number_format($due, 0, ',', ' ').' F).');
                        }
                    }
                },
            ],
            'date_paiement' => 'required|date|before_or_equal:today',
            'mode' => 'required|in:espèces,virement,chèque,mobile_money,autre',
            'preuve' => 'nullable|file|mimes:pdf,jpg,jpeg,png,webp|max:5120',
            'reference' => 'nullable|string|max:100',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'montant.min' => 'Le montant doit être supérieur à 0.',
            'date_paiement.before_or_equal' => 'La date de paiement ne peut pas être dans le futur.',
            'preuve.max' => 'La preuve ne doit pas dépasser 5 Mo.',
            'mode.in' => 'Le mode de paiement sélectionné est invalide.',
        ];
    }
}
