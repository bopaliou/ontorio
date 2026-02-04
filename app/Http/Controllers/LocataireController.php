<?php

namespace App\Http\Controllers;

use App\Helpers\ActivityLogger;
use App\Models\Locataire;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LocataireController extends Controller
{
    /**
     * Store a newly created locataire in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nom' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:locataires',
            'telephone' => 'required|string|max:50',
            'adresse' => 'nullable|string',
            'cni' => 'nullable|string|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $data = $request->only(['nom', 'email', 'telephone', 'adresse']);
            $data['pieces_identite'] = $request->cni;
            $locataire = Locataire::create($data);
            ActivityLogger::log('Création Locataire', "Ajout du locataire {$locataire->nom}", 'success', $locataire);

            return response()->json([
                'success' => true,
                'message' => 'Locataire ajouté avec succès !',
                'data' => $locataire,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue. Veuillez réessayer.',
            ], 500);
        }
    }

    /**
     * Update the specified locataire in storage.
     */
    public function update(Request $request, Locataire $locataire)
    {
        $validator = Validator::make($request->all(), [
            'nom' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:locataires,email,'.$locataire->id,
            'telephone' => 'required|string|max:50',
            'adresse' => 'nullable|string',
            'cni' => 'nullable|string|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $data = $request->only(['nom', 'email', 'telephone', 'adresse']);
            $data['pieces_identite'] = $request->cni;
            $locataire->update($data);
            ActivityLogger::log('Modification Locataire', "Mise à jour du locataire {$locataire->nom}", 'info', $locataire);

            return response()->json([
                'success' => true,
                'message' => 'Locataire mis à jour !',
                'data' => $locataire,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue. Veuillez réessayer.',
            ], 500);
        }
    }

    /**
     * Remove the specified locataire from storage.
     */
    public function destroy(Locataire $locataire)
    {
        try {
            // Vérifier s'il a des contrats actifs
            if ($locataire->contrats()->where('statut', 'actif')->exists()) {
                return response()->json(['success' => false, 'message' => 'Impossible de supprimer ce locataire car il a des contrats actifs.'], 403);
            }

            $nom = $locataire->nom;
            $locataire->delete();

            ActivityLogger::log('Suppression Locataire', "Suppression du locataire {$nom}", 'warning');

            return response()->json(['success' => true, 'message' => 'Locataire supprimé !']);
        } catch (\Exception $e) {
            \Log::error('Erreur suppression locataire', ['id' => $locataire->id, 'error' => $e->getMessage()]);

            return response()->json(['success' => false, 'message' => 'Une erreur est survenue lors de la suppression.'], 500);
        }
    }
}
