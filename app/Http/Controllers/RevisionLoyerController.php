<?php

namespace App\Http\Controllers;

use App\Models\RevisionLoyer;
use App\Models\Contrat;
use App\Models\Loyer;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RevisionLoyerController extends Controller
{
    /**
     * Store a new revision
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'contrat_id' => 'required|exists:contrats,id',
            'ancien_montant' => 'required|numeric',
            'nouveau_montant' => 'required|numeric',
            'date_effet' => 'required|date',
            'motif' => 'nullable|string|max:255',
            'justification' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $contrat = Contrat::findOrFail($request->contrat_id);
            
            // Calcul du pourcentage
            $pourcentage = (($request->nouveau_montant - $request->ancien_montant) / $request->ancien_montant) * 100;

            $revision = RevisionLoyer::create([
                'contrat_id' => $contrat->id,
                'ancien_montant' => $request->ancien_montant,
                'nouveau_montant' => $request->nouveau_montant,
                'date_effet' => $request->date_effet,
                'motif' => $request->motif,
                'pourcentage_augmentation' => round($pourcentage, 2),
                'justification' => $request->justification,
                'created_by' => auth()->id(),
            ]);

            // Mise à jour du contrat
            $contrat->update(['loyer_montant' => $request->nouveau_montant]);

            // Mise à jour des loyers futurs (non payés et dont le mois est >= date_effet)
            // Note: Simplification ici, on met à jour tous les loyers 'émis' ou 'en_retard' pour ce contrat
            Loyer::where('contrat_id', $contrat->id)
                ->whereIn('statut', ['émis', 'en_retard'])
                ->update(['montant' => $request->nouveau_montant]);

            ActivityLogger::log('Révision Loyer', "Nouvelle révision pour contrat #{$contrat->id} : {$revision->pourcentage_augmentation}%", 'info', $revision);

            return response()->json([
                'success' => true,
                'message' => 'Révision de loyer appliquée avec succès !',
                'data' => $revision,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * History of revisions for a contract
     */
    public function index(Request $request)
    {
        $query = RevisionLoyer::with('creator');
        
        if ($request->has('contrat_id')) {
            $query->where('contrat_id', $request->contrat_id);
        }

        $revisions = $query->latest()->get();

        return response()->json([
            'success' => true,
            'data' => $revisions,
        ]);
    }
}
