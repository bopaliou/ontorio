<?php

namespace App\Http\Controllers;

use App\Models\Bien;
use App\Models\BienImage;
use App\Models\Proprietaire;
use App\Models\Contrat;
use App\Models\Loyer;
use App\Helpers\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class BienController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $biens = Bien::with(['contrats.locataire', 'proprietaire', 'imagePrincipale'])
            ->latest()
            ->paginate(15);
            
        return view('biens.index', compact('biens'));
    }

    /**
     * Store Bien via Iframe target
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nom' => 'required|string|max:255',
            'adresse' => 'nullable|string',
            'loyer_mensuel' => 'required|numeric',
            'type' => 'required|in:appartement,villa,studio,bureau,magasin,entrepot,autre',
            'nombre_pieces' => 'nullable|integer',
            'meuble' => 'nullable|boolean',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            // Ontario Group est le seul propriétaire par défaut si non spécifié
            $proprietaire = Proprietaire::firstOrCreate(
                ['nom' => 'Ontario Group'],
                [
                    'prenom' => 'S.A.',
                    'email' => 'contact@ontariogroup.net',
                    'telephone' => '33 822 32 67',
                    'adresse' => '5 Félix Faure x Colbert, Dakar Plateau',
                ]
            );

            $data = $request->except(['images', 'image']);
            $data['proprietaire_id'] = $request->proprietaire_id ?? $proprietaire->id;

            $bien = Bien::create($data);

            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $index => $imageFile) {
                    $path = $imageFile->store('biens/'.$bien->id, 'public');
                    BienImage::create([
                        'bien_id' => $bien->id,
                        'chemin' => $path,
                        'nom_original' => $imageFile->getClientOriginalName(),
                        'principale' => ($index === 0),
                        'ordre' => $index + 1,
                    ]);
                }
            }

            ActivityLogger::log('Création Bien', 'Ajout du bien : '.$bien->nom, 'success', $bien);

            return response()->json([
                'success' => true,
                'message' => 'Bien ajouté avec succès !',
                'data' => $bien,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update Bien via Iframe target
     */
    public function update(Request $request, Bien $bien)
    {
        $validator = Validator::make($request->all(), [
            'nom' => 'required|string|max:255',
            'adresse' => 'nullable|string',
            'loyer_mensuel' => 'required|numeric',
            'type' => 'required|in:appartement,villa,studio,bureau,magasin,entrepot,autre',
            'nombre_pieces' => 'nullable|integer',
            'meuble' => 'nullable|boolean',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $oldLoyer = $bien->loyer_mensuel;
            $newLoyer = $request->loyer_mensuel;

            $bien->update($request->except(['images', 'image']));

            if ((float) $oldLoyer != (float) $newLoyer) {
                $contrats = Contrat::where('bien_id', $bien->id)
                    ->whereIn('statut', ['actif', 'en_attente'])
                    ->get();

                foreach ($contrats as $c) {
                    $c->update(['loyer_montant' => $newLoyer]);
                    Loyer::where('contrat_id', $c->id)
                        ->whereIn('statut', ['émis', 'en_retard'])
                        ->update(['montant' => $newLoyer]);
                }
                ActivityLogger::log('Synchro Bien-Global', 'Mise à jour automatique Contrats & Loyers : '.$bien->nom, 'info');
            }

            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $index => $imageFile) {
                    $path = $imageFile->store('biens/'.$bien->id, 'public');
                    BienImage::create([
                        'bien_id' => $bien->id,
                        'chemin' => $path,
                        'nom_original' => $imageFile->getClientOriginalName(),
                        'principale' => false,
                        'ordre' => 99,
                    ]);
                }
            }

            ActivityLogger::log('Modification Bien', 'Mise à jour du bien : '.$bien->nom, 'info', $bien);

            return response()->json([
                'success' => true,
                'message' => 'Bien mis à jour !',
                'data' => $bien,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete Bien via Iframe target
     */
    public function destroy(Bien $bien)
    {
        try {
            $nom = $bien->nom;
            $bien->delete();
            ActivityLogger::log('Suppression Bien', 'Suppression du bien : '.$nom, 'warning');

            return response()->json([
                'success' => true,
                'message' => 'Bien supprimé avec succès !',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Impossible de supprimer ce bien car il est lié à des contrats actifs.',
            ], 422);
        }
    }

    /**
     * Delete Bien Image via AJAX
     */
    public function deleteImage(BienImage $bienImage)
    {
        try {
            if (Storage::disk('public')->exists($bienImage->chemin)) {
                Storage::disk('public')->delete($bienImage->chemin);
            }

            if ($bienImage->principale) {
                $nextImage = BienImage::where('bien_id', $bienImage->bien_id)
                    ->where('id', '!=', $bienImage->id)
                    ->orderBy('ordre')
                    ->first();
                if ($nextImage) {
                    $nextImage->update(['principale' => true]);
                }
            }

            $bienImage->delete();

            return response()->json(['success' => true, 'message' => 'Image supprimée']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
