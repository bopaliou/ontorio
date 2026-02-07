<?php

namespace App\Http\Controllers;

use App\Helpers\ActivityLogger;
use App\Models\Loyer;
use App\Models\Paiement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PaiementController extends Controller
{
    /**
     * Display a listing of payments.
     */
    public function index(Request $request)
    {
        $paiements = Paiement::with('loyer')->get();

        return response()->json(['data' => $paiements], 200);
    }

    /**
     * Display the specified payment.
     */
    public function show(Paiement $paiement)
    {
        return response()->json(['data' => $paiement], 200);
    }

    /**
     * Store a newly created payment in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'loyer_id' => 'required|exists:loyers,id',
            'montant' => 'required|numeric|min:0',
            'date_paiement' => 'required|date',
            // Accepted payment modes
            'mode' => 'nullable|in:espèces,virement,chèque,mobile_money',
            'preuve' => 'nullable|file|mimes:pdf,jpg,jpeg,png,webp,gif|max:5120',
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

            $preuvePath = null;
            if ($request->hasFile('preuve')) {
                $file = $request->file('preuve');
                $filename = 'preuve_'.time().'_'.uniqid().'.'.$file->getClientOriginalExtension();
                $preuvePath = $file->storeAs('paiements', $filename, 'public');
            }

            // Créer le paiement
            $paiement = Paiement::create([
                'loyer_id' => $request->loyer_id,
                'montant' => $request->montant,
                'date_paiement' => $request->date_paiement,
                'mode' => $request->mode ?? 'espèces',
                'reference' => 'PAY-'.strtoupper(uniqid()),
                'preuve' => $preuvePath,
            ]);

            // Mettre à jour le statut du loyer
            $loyer = Loyer::find($request->loyer_id);
            if ($loyer) {
                // Si le montant payé couvre tout le montant dû (ou plus)
                // Ici on assume un paiement complet pour simplifier, sinon 'partiel'
                if ($request->montant >= $loyer->montant) {
                    $loyer->statut = 'payé';
                } else {
                    // Logique partielle non gérée pour l'instant
                    $loyer->statut = 'partiellement_payé';
                }
                $loyer->save();
            }

            DB::commit();

            ActivityLogger::log('Encaissement Loyer', "Paiement de {$request->montant} F pour le loyer #{$loyer->id}", 'success');

            return response()->json([
                'success' => true,
                'message' => 'Paiement enregistré avec succès !',
                'data' => $paiement,
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Erreur enregistrement paiement', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue lors de l\'enregistrement.',
            ], 500);
        }
    }

    /**
     * Remove the specified payment from storage.
     */
    public function destroy(Paiement $paiement)
    {
        try {
            DB::beginTransaction();

            $loyer = $paiement->loyer;
            $montant = $paiement->montant;

            // Supprimer le paiement
            $paiement->delete();

            // Recalculer le reste à payer pour ce loyer
            if ($loyer) {
                // Somme des paiements restants
                $sommePaiements = $loyer->paiements()->sum('montant');

                // Si la somme est inférieure au montant du loyer, on repasse en impayé
                if ($sommePaiements < $loyer->montant) {
                    // Déterminer si 'en_retard' ou 'émis' basé sur la date du jour et le mois du loyer
                    $moisLoyer = \Carbon\Carbon::parse($loyer->mois);
                    $now = \Carbon\Carbon::now();

                    // Si on est après le mois du loyer, c'est un retard
                    if ($now->format('Y-m') > $moisLoyer->format('Y-m')) {
                        $loyer->statut = 'en_retard';
                    } else {
                        // Si c'est le mois en cours ou futur
                        $loyer->statut = 'émis';
                    }

                    // Si partiel (amélioration future)
                    if ($sommePaiements > 0) {
                        $loyer->statut = 'partiellement_payé'; // Assurez-vous que l'enum supporte ça ou restez sur 'émis'/'en_retard'
                    }

                    $loyer->save();
                }
            }

            DB::commit();

            ActivityLogger::log('Annulation Encaissement', "Suppression paiement de {$montant} F pour loyer #{$loyer->id}", 'warning');

            return response()->json([
                'success' => true,
                'message' => 'Paiement supprimé et statut du loyer restauré.',
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Erreur suppression paiement', ['id' => $paiement->id, 'error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue lors de la suppression.',
            ], 500);
        }
    }
}
