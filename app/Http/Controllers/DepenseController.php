<?php

namespace App\Http\Controllers;

use App\Helpers\ActivityLogger;
use App\Http\Responses\ApiResponse;
use App\Models\Depense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class DepenseController extends Controller
{
    protected $depenseService;

    public function __construct(\App\Services\DepenseService $depenseService)
    {
        $this->depenseService = $depenseService;
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'bien_id' => 'required|exists:biens,id',
            'titre' => 'required|string|max:255',
            'montant' => 'required|numeric|min:0',
            'date_depense' => 'required|date',
            'categorie' => 'required|in:maintenance,travaux,taxe,assurance,autre',
            'description' => 'nullable|string',
            'justificatif' => 'nullable|file|mimes:pdf,jpeg,png,jpg|max:5120',
        ]);

        if ($validator->fails()) {
            return ApiResponse::error($validator->errors()->first(), 422, $validator->errors());
        }

        try {
            $depense = $this->depenseService->createDepense(
                $request->except('justificatif'),
                $request->file('justificatif')
            );

            return ApiResponse::created($depense, 'Dépense enregistrée avec succès !');
        } catch (\Exception $e) {
            \Log::error('Erreur création dépense', ['error' => $e->getMessage()]);

            return ApiResponse::error('Une erreur est survenue lors de l\'enregistrement.', 500);
        }
    }

    public function update(Request $request, Depense $depense)
    {
        $validator = Validator::make($request->all(), [
            'titre' => 'required|string|max:255',
            'montant' => 'required|numeric|min:0',
            'date_depense' => 'required|date',
            'categorie' => 'required|in:maintenance,travaux,taxe,assurance,autre',
            'description' => 'nullable|string',
            'justificatif' => 'nullable|file|mimes:pdf,jpeg,png,jpg|max:5120',
        ]);

        if ($validator->fails()) {
            return ApiResponse::error($validator->errors()->first(), 422, $validator->errors());
        }

        try {
            $depense = $this->depenseService->updateDepense(
                $depense,
                $request->except('justificatif'),
                $request->file('justificatif')
            );

            return ApiResponse::success($depense, 'Dépense mise à jour !');
        } catch (\Exception $e) {
            \Log::error('Erreur mise à jour dépense', ['id' => $depense->id, 'error' => $e->getMessage()]);

            return ApiResponse::error('Une erreur est survenue lors de la mise à jour.', 500);
        }
    }

    public function destroy(Depense $depense)
    {
        try {
            $this->depenseService->deleteDepense($depense);

            return ApiResponse::success(null, 'Dépense supprimée');
        } catch (\Exception $e) {
            \Log::error('Erreur suppression dépense', ['id' => $depense->id, 'error' => $e->getMessage()]);

            return ApiResponse::error('Une erreur est survenue lors de la suppression.', 500);
        }
    }
}
