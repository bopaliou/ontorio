<?php

namespace App\Http\Controllers;

use App\Models\Proprietaire;
use Illuminate\Http\Request;

class ProprietaireController extends Controller
{
    /**
     * Store a newly created proprietaire via Dashboard
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'telephone' => 'nullable|string|max:20',
            'adresse' => 'nullable|string',
            'type_mandat' => 'nullable|in:gestion,location,vente',
            'taux_commission' => 'nullable|numeric|min:0|max:100',
        ]);

        $proprietaire = Proprietaire::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Propriétaire ajouté avec succès',
            'data' => $proprietaire,
        ]);
    }

    /**
     * Update proprietaire via Dashboard
     */
    public function update(Request $request, Proprietaire $proprietaire)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'telephone' => 'nullable|string|max:20',
            'adresse' => 'nullable|string',
            'type_mandat' => 'nullable|in:gestion,location,vente',
            'taux_commission' => 'nullable|numeric|min:0|max:100',
        ]);

        $proprietaire->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Propriétaire mis à jour avec succès',
            'data' => $proprietaire,
        ]);
    }

    /**
     * Delete proprietaire via Dashboard
     */
    public function destroy(Proprietaire $proprietaire)
    {
        $proprietaire->delete();

        return response()->json([
            'success' => true,
            'message' => 'Propriétaire supprimé avec succès',
        ]);
    }

    public function bilanPDF(Proprietaire $proprietaire)
    {
        $moisActuel = \Carbon\Carbon::now()->format('Y-m');

        // Charger les données financières consolidées
        $biens = $proprietaire->biens()->with(['depenses', 'contrats.paiements'])->get();

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.bilan_proprietaire', [
            'proprietaire' => $proprietaire,
            'biens' => $biens,
            'mois' => $moisActuel,
        ]);

        return $pdf->stream('Bilan_'.str_replace(' ', '_', $proprietaire->nom).'.pdf');
    }

    public function index()
    {
        return abort(404);
    }

    public function create()
    {
        return abort(404);
    }

    public function show(Proprietaire $proprietaire)
    {
        return abort(404);
    }

    public function edit(Proprietaire $proprietaire)
    {
        return abort(404);
    }
}
