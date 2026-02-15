<?php

namespace App\Http\Controllers;

use App\Http\Responses\ApiResponse;
use App\Models\Locataire;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LocataireController extends Controller
{
    protected $locataireService;

    public function __construct(\App\Services\LocataireService $locataireService)
    {
        $this->locataireService = $locataireService;
    }

    /**
     * Store a newly created locataire in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('locataires.create');

        $validator = Validator::make($request->all(), [
            'nom' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:locataires',
            'telephone' => 'required|string|max:50',
            'adresse' => 'nullable|string',
            'cni' => 'nullable|string|max:100',
        ]);

        if ($validator->fails()) {
            return ApiResponse::error($validator->errors()->first(), 422, $validator->errors());
        }

        try {
            $data = $request->only(['nom', 'email', 'telephone', 'adresse']);
            $data['pieces_identite'] = $request->cni;
            $locataire = $this->locataireService->createLocataire($data);

            return ApiResponse::created($locataire, 'Locataire ajouté avec succès !');
        } catch (\Exception $e) {
            return ApiResponse::error('Une erreur est survenue. Veuillez réessayer.', 500);
        }
    }

    /**
     * Update the specified locataire in storage.
     */
    public function update(Request $request, Locataire $locataire)
    {
        $this->authorize('locataires.edit');

        $validator = Validator::make($request->all(), [
            'nom' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:locataires,email,'.$locataire->id,
            'telephone' => 'required|string|max:50',
            'adresse' => 'nullable|string',
            'cni' => 'nullable|string|max:100',
        ]);

        if ($validator->fails()) {
            return ApiResponse::error($validator->errors()->first(), 422, $validator->errors());
        }

        try {
            $data = $request->only(['nom', 'email', 'telephone', 'adresse']);
            $data['pieces_identite'] = $request->cni;
            $locataire = $this->locataireService->updateLocataire($locataire, $data);

            return ApiResponse::success($locataire, 'Locataire mis à jour !');
        } catch (\Exception $e) {
            return ApiResponse::error('Une erreur est survenue. Veuillez réessayer.', 500);
        }
    }

    /**
     * Remove the specified locataire from storage.
     */
    public function destroy(Locataire $locataire)
    {
        $this->authorize('locataires.delete');

        try {
            // Task 3.2: Vérifier s'il a des contrats actifs
            if ($locataire->contrats()->whereIn('statut', ['actif', 'en_attente'])->exists()) {
                return ApiResponse::conflict('Impossible de supprimer ce locataire car il possède des contrats actifs ou en attente.');
            }

            $this->locataireService->deleteLocataire($locataire);

            return ApiResponse::success(null, 'Locataire supprimé !');
        } catch (\Exception $e) {
            \Log::error('Erreur suppression locataire', ['id' => $locataire->id, 'error' => $e->getMessage()]);

            return ApiResponse::error('Une erreur est survenue lors de la suppression.', 500);
        }
    }
}
