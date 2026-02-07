<?php

namespace App\Http\Controllers;

use App\Models\Proprietaire;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProprietaireController extends Controller
{
    /**
     * Store Propriétaire via Iframe target (Single Page Pattern)
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nom' => 'required|string|max:255',
            'email' => 'nullable|email|unique:proprietaires,email',
            'telephone' => 'nullable|string|max:50',
            'adresse' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $prop = Proprietaire::create($request->only(['nom', 'prenom', 'email', 'telephone', 'adresse']));

            return response()->json([
                'success' => true,
                'message' => 'Propriétaire créé avec succès !',
                'data' => $prop,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue. Veuillez réessayer.',
            ], 500);
        }
    }

    /**
     * Update Propriétaire via Iframe target (Single Page Pattern)
     */
    public function update(Request $request, Proprietaire $proprietaire)
    {
        $validator = Validator::make($request->all(), [
            'nom' => 'required|string|max:255',
            'email' => 'nullable|email|unique:proprietaires,email,'.$proprietaire->id,
            'telephone' => 'nullable|string|max:50',
            'adresse' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $proprietaire->update($request->only(['nom', 'prenom', 'email', 'telephone', 'adresse']));

            return response()->json([
                'success' => true,
                'message' => 'Propriétaire mis à jour !',
                'data' => $proprietaire,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue. Veuillez réessayer.',
            ], 500);
        }
    }

    /**
     * Delete proprietaire via Dashboard
     */
    public function destroy(Proprietaire $proprietaire)
    {
        try {
            $proprietaire->delete();

            return response()->json([
                'success' => true,
                'message' => 'Propriétaire supprimé avec succès',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Impossible de supprimer ce propriétaire car il est lié à des biens.',
            ], 422);
        }
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
