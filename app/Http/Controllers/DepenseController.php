<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Depense;
use App\Models\Bien;
use App\Helpers\ActivityLogger;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class DepenseController extends Controller
{
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
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        try {
            $data = $request->except('justificatif');
            
            if ($request->hasFile('justificatif')) {
                $data['justificatif'] = $request->file('justificatif')->store('depenses', 'public');
            }

            $depense = Depense::create($data);
            
            ActivityLogger::log('Création Dépense', "Ajout d'une dépense de {$depense->montant} F pour {$depense->bien->nom}", 'success', $depense);

            return response()->json([
                'success' => true,
                'message' => 'Dépense enregistrée avec succès !',
                'data' => $depense
            ]);
        } catch (\Exception $e) {
            \Log::error('Erreur création dépense', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue lors de l\'enregistrement.'
            ], 500);
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
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        try {
            $data = $request->except('justificatif');
            
            if ($request->hasFile('justificatif')) {
                // Supprimer l'ancien justificatif
                if ($depense->justificatif && Storage::disk('public')->exists($depense->justificatif)) {
                    Storage::disk('public')->delete($depense->justificatif);
                }
                $data['justificatif'] = $request->file('justificatif')->store('depenses', 'public');
            }

            $depense->update($data);
            
            ActivityLogger::log('Modification Dépense', "Mise à jour de la dépense #{$depense->id}", 'info', $depense);

            return response()->json([
                'success' => true,
                'message' => 'Dépense mise à jour !',
                'data' => $depense
            ]);
        } catch (\Exception $e) {
            \Log::error('Erreur mise à jour dépense', ['id' => $depense->id, 'error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue lors de la mise à jour.'
            ], 500);
        }
    }

    public function destroy(Depense $depense)
    {
        try {
            if ($depense->justificatif && Storage::disk('public')->exists($depense->justificatif)) {
                Storage::disk('public')->delete($depense->justificatif);
            }
            $depense->delete();
            ActivityLogger::log('Suppression Dépense', "Suppression de la dépense #{$depense->id}", 'warning');
            return response()->json(['success' => true, 'message' => 'Dépense supprimée']);
        } catch (\Exception $e) {
            \Log::error('Erreur suppression dépense', ['id' => $depense->id, 'error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Une erreur est survenue lors de la suppression.'], 500);
        }
    }
}
