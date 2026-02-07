<?php

namespace App\Http\Controllers;

use App\Http\Responses\ApiResponse;
use App\Models\Proprietaire;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProprietaireController extends Controller
{
    protected $proprietaireService;

    public function __construct(\App\Services\ProprietaireService $proprietaireService)
    {
        $this->proprietaireService = $proprietaireService;
    }

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
            return ApiResponse::error($validator->errors()->first(), 422, $validator->errors());
        }

        try {
            $prop = $this->proprietaireService->createProprietaire($request->only(['nom', 'prenom', 'email', 'telephone', 'adresse']));

            return ApiResponse::created($prop, 'Propriétaire créé avec succès !');
        } catch (\Exception $e) {
            return ApiResponse::error('Une erreur est survenue. Veuillez réessayer.', 500);
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
            return ApiResponse::error($validator->errors()->first(), 422, $validator->errors());
        }

        try {
            $prop = $this->proprietaireService->updateProprietaire($proprietaire, $request->only(['nom', 'prenom', 'email', 'telephone', 'adresse']));

            return ApiResponse::success($prop, 'Propriétaire mis à jour !');
        } catch (\Exception $e) {
            return ApiResponse::error('Une erreur est survenue. Veuillez réessayer.', 500);
        }
    }

    /**
     * Delete proprietaire via Dashboard
     */
    public function destroy(Proprietaire $proprietaire)
    {
        try {
            $this->proprietaireService->deleteProprietaire($proprietaire);

            return ApiResponse::success(null, 'Propriétaire supprimé avec succès');
        } catch (\Exception $e) {
            return ApiResponse::error('Impossible de supprimer ce propriétaire car il est lié à des biens.', 422);
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
