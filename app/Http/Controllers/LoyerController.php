<?php

namespace App\Http\Controllers;

use App\Models\Loyer;
use App\Models\Contrat;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class LoyerController extends Controller
{
    /**
     * Exporter la quittance en PDF
     */
    public function exporterPDF(Loyer $loyer)
    {
        // On charge les relations nécessaires
        $loyer->load(['contrat.locataire', 'contrat.bien']);

        $pdf = Pdf::loadView('pdf.quittance', compact('loyer'));
        
        $filename = 'Quittance_' . str_replace(' ', '_', $loyer->contrat->locataire->nom) . '_' . $loyer->mois . '.pdf';
        
        return $pdf->stream($filename);
    }

    /**
     * Générer les loyers pour le mois en cours pour tous les contrats actifs
     */
    /**
     * Générer les loyers pour le mois en cours pour tous les contrats actifs
     */
    public function genererMois(Request $request)
    {
        Carbon::setLocale('fr');
        $moisActuel = Carbon::now()->format('Y-m');
        $contrats = Contrat::where('statut', 'actif')->get();
        $countCreated = 0;
        $countUpdated = 0;

        foreach ($contrats as $contrat) {
            // Vérifier si le loyer existe déjà pour ce mois
            $loyer = Loyer::where('contrat_id', $contrat->id)
                           ->where('mois', $moisActuel)
                           ->first();

            if (!$loyer) {
                Loyer::create([
                    'contrat_id' => $contrat->id,
                    'mois' => $moisActuel,
                    'montant' => $contrat->loyer_montant,
                    'statut' => 'émis'
                ]);
                $countCreated++;
            } else {
                // Si le loyer n'est pas encore payé et que le montant du contrat a changé, on met à jour
                if ($loyer->statut !== 'payé' && $loyer->montant != $contrat->loyer_montant) {
                    $loyer->update([
                        'montant' => $contrat->loyer_montant
                    ]);
                    $countUpdated++;
                }
            }
        }

        $msg = "$countCreated quittances générées et $countUpdated mises à jour pour " . Carbon::now()->translatedFormat('F Y');
        
        return response("<script>window.parent.loySection.onStoreSuccess('$msg');</script>")
                ->header('Content-Type', 'text/html');
    }

    public function index() { return abort(404); }
    public function create() { return abort(404); }
    public function store(Request $request) { return abort(404); }
    public function show(string $id) { return abort(404); }
    public function edit(string $id) { return abort(404); }
    
    public function update(Request $request, string $id) 
    {
        $loyer = Loyer::findOrFail($id);
        
        $validated = $request->validate([
            'montant' => 'required|numeric|min:0',
            'statut' => 'required|in:émis,payé,en_retard,annulé,partiellement_payé',
            'mois' => 'required|date_format:Y-m',
            'note_annulation' => 'nullable|string'
        ]);

        $oldStatus = $loyer->getOriginal('statut');
        $loyer->update($validated);

        // Synchronisation du paiement
        if ($loyer->statut === 'payé' || $loyer->statut === 'partiellement_payé') {
            // Si le statut est passé à payé ou partiellement payé
            $paiement = \App\Models\Paiement::where('loyer_id', $loyer->id)->first();
            
            // Pour le statut 'payé', on utilise le montant total du loyer
            // Pour 'partiellement_payé', on peut recevoir un montant spécifique via request
            $montantEncaisse = ($loyer->statut === 'payé') ? $loyer->montant : ($request->montant_paye ?? $loyer->montantPayé());

            if (!$paiement) {
                // Création automatique si inexistant
                \App\Models\Paiement::create([
                    'loyer_id' => $loyer->id,
                    'montant' => $montantEncaisse,
                    'date_paiement' => now(),
                    'mode' => 'espèces',
                    'reference' => 'PAY-' . strtoupper(uniqid())
                ]);
            } else {
                // Mise à jour du montant si déjà existant
                $paiement->update(['montant' => $montantEncaisse]);
            }
        } elseif ($oldStatus === 'payé' && $loyer->statut !== 'payé' && $loyer->statut !== 'partiellement_payé') {
            // Si on repasse de 'payé' ou 'partiel' à autre chose, on supprime le paiement associé
            \App\Models\Paiement::where('loyer_id', $loyer->id)->delete();
        }

        return response()->json(['message' => 'Loyer mis à jour avec succès', 'loyer' => $loyer]);
    }

    public function destroy(string $id) { return abort(404); }
}
