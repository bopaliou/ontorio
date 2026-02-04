<?php

namespace App\Http\Controllers;

use App\Helpers\ActivityLogger;
use App\Http\Requests\StoreContratRequest;
use App\Models\Bien;
use App\Models\Contrat;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ContratController extends Controller
{
    /**
     * Store a newly created contrat in storage.
     */
    public function store(StoreContratRequest $request)
    {
        // Validation déjà faite par StoreContratRequest

        // Vérifier si le bien est déjà occupé
        $bien = Bien::find($request->bien_id);
        if ($bien->statut === 'occupé' && $bien->contrats()->where('statut', 'actif')->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Ce bien est déjà occupé par un contrat actif.',
            ], 422);
        }

        try {
            DB::beginTransaction();

            // 1. Créer le contrat
            $contrat = Contrat::create([
                'bien_id' => $request->bien_id,
                'locataire_id' => $request->locataire_id,
                'date_debut' => $request->date_debut,
                'date_fin' => $request->date_fin,
                'loyer_montant' => $request->loyer_montant,
                'caution' => $request->caution,
                'frais_dossier' => $request->frais_dossier,
                'type_bail' => $request->type_bail ?? 'habitation',
                'date_signature' => $request->date_signature,
                'renouvellement_auto' => $request->renouvellement_auto ?? false,
                'preavis_mois' => $request->preavis_mois ?? 3,
                'statut' => 'actif',
            ]);

            // 2. Mettre à jour le statut du bien
            $bien->update(['statut' => 'occupé']);

            DB::commit();

            ActivityLogger::log('Création Bail', "Nouveau bail pour le bien {$bien->nom}", 'success', $contrat);

            return response()->json([
                'success' => true,
                'message' => 'Bail activé avec succès !',
                'data' => $contrat,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Erreur activation bail', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Une erreur technique est survenue.',
            ], 500);
        }
    }

    /**
     * Update the specified contract.
     */
    public function update(Request $request, Contrat $contrat)
    {
        $validator = Validator::make($request->all(), [
            'loyer_montant' => 'required|numeric|min:0',
            'date_debut' => 'required|date',
            'caution' => 'nullable|numeric|min:0',
            'frais_dossier' => 'nullable|numeric|min:0',
            'type_bail' => 'required|string|in:habitation,commercial,professionnel',
            'date_signature' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            DB::beginTransaction();

            $oldMontant = $contrat->loyer_montant;
            $contrat->update([
                'loyer_montant' => $request->loyer_montant,
                'date_debut' => $request->date_debut,
                'caution' => $request->caution,
                'frais_dossier' => $request->frais_dossier,
                'type_bail' => $request->type_bail,
                'date_signature' => $request->date_signature,
            ]);

            // Propagation aux loyers impayés (tous)
            if ($oldMontant != $request->loyer_montant) {
                \App\Models\Loyer::where('contrat_id', $contrat->id)
                    ->whereIn('statut', ['émis', 'en_retard'])
                    ->update(['montant' => $request->loyer_montant]);

                ActivityLogger::log('Modification Bail', "Mise à jour conditions bail #{$contrat->id} et loyers impayés", 'info', $contrat);
            } else {
                ActivityLogger::log('Modification Bail', "Mise à jour conditions bail #{$contrat->id}", 'info', $contrat);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Contrat mis à jour avec succès.',
                'data' => $contrat,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Erreur mise à jour bail', ['id' => $contrat->id, 'error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Une erreur technique est survenue.',
            ], 500);
        }
    }

    /**
     * Terminate/Archive a contract (Soft Delete logic or Status Update)
     * Using destroy for definitive removal or closure depending on business rule.
     * Here allow delete if no payment attached, or close if active.
     * For simplicity, let's allow DELETE to remove errors, and maybe a custom route for ending.
     */
    public function destroy(Contrat $contrat)
    {
        try {
            DB::beginTransaction();

            $bienId = $contrat->bien_id;

            // Supprimer le contrat
            $contrat->delete();

            // Vérifier s'il reste d'autres contrats actifs pour ce bien
            $hasActive = Contrat::where('bien_id', $bienId)->where('statut', 'actif')->exists();
            if (! $hasActive) {
                // Libérer le bien
                Bien::where('id', $bienId)->update(['statut' => 'libre']);
            }

            DB::commit();

            ActivityLogger::log('Suppression Bail', "Suppression du bail #{$contrat->id}", 'warning');

            return response()->json(['success' => true, 'message' => 'Contrat supprimé et bien libéré.']);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Erreur suppression bail', ['id' => $contrat->id, 'error' => $e->getMessage()]);

            return response()->json(['success' => false, 'message' => 'Une erreur est survenue lors de la suppression.'], 500);
        }
    }

    /**
     * Generate PDF for the contract.
     */
    public function print(Contrat $contrat)
    {
        $pdf = Pdf::loadView('pdf.contrat_location', compact('contrat'));

        return $pdf->stream("Contrat_Location_C{$contrat->id}.pdf");
    }
}
