<?php

namespace App\Http\Controllers;

use App\Helpers\ActivityLogger;
use App\Http\Responses\ApiResponse;
use App\Models\Bien;
use App\Models\BienImage;
use App\Models\Contrat;
use App\Models\Loyer;
use App\Models\Proprietaire;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
class BienController extends Controller
{
    protected $bienService;

    public function __construct(\App\Services\BienService $bienService)
    {
        $this->bienService = $bienService;
    }

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
    public function store(\App\Http\Requests\StoreBienRequest $request)
    {
        try {
            // Ontario Group est le seul propriétaire par défaut si non spécifié
            $proprietaire = \App\Models\Proprietaire::firstOrCreate(
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

            $bien = $this->bienService->createBien($data);

            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $index => $imageFile) {
                    $path = $imageFile->store('biens/'.$bien->id, 'public');
                    \App\Models\BienImage::create([
                        'bien_id' => $bien->id,
                        'chemin' => $path,
                        'nom_original' => $imageFile->getClientOriginalName(),
                        'principale' => ($index === 0),
                        'ordre' => $index + 1,
                    ]);
                }
            }

            return ApiResponse::created($bien, 'Bien ajouté avec succès !');
        } catch (\Exception $e) {
            \Log::error('Erreur création bien', ['error' => $e->getMessage()]);

            return ApiResponse::error('Une erreur technique est survenue lors de la création.', 500);
        }
    }

    /**
     * Update Bien via Iframe target
     */
    public function update(\App\Http\Requests\StoreBienRequest $request, Bien $bien)
    {
        try {
            $oldLoyer = $bien->loyer_mensuel;
            $newLoyer = $request->loyer_mensuel;

            $bien = $this->bienService->updateBien($bien, $request->except(['images', 'image']));

            if ((float) $oldLoyer != (float) $newLoyer) {
                $contrats = \App\Models\Contrat::where('bien_id', $bien->id)
                    ->whereIn('statut', ['actif', 'en_attente'])
                    ->get();

                foreach ($contrats as $c) {
                    $c->update(['loyer_montant' => $newLoyer]);
                    \App\Models\Loyer::where('contrat_id', $c->id)
                        ->whereIn('statut', ['émis', 'en_retard'])
                        ->update(['montant' => $newLoyer]);
                }
                ActivityLogger::log('Synchro Bien-Global', 'Mise à jour automatique Contrats & Loyers : '.$bien->nom, 'info');
            }

            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $index => $imageFile) {
                    $path = $imageFile->store('biens/'.$bien->id, 'public');
                    \App\Models\BienImage::create([
                        'bien_id' => $bien->id,
                        'chemin' => $path,
                        'nom_original' => $imageFile->getClientOriginalName(),
                        'principale' => false,
                        'ordre' => 99,
                    ]);
                }
            }

            return ApiResponse::success($bien, 'Bien mis à jour !');
        } catch (\Exception $e) {
            \Log::error('Erreur modification bien', ['id' => $bien->id, 'error' => $e->getMessage()]);

            return ApiResponse::error('Une erreur technique est survenue lors de la modification.', 500);
        }
    }

    /**
     * Delete Bien via Iframe target
     */
    public function destroy(Bien $bien)
    {
        try {
            // Task 3.2: Vérifier s'il a des contrats actifs
            if ($bien->contrats()->whereIn('statut', ['actif', 'en_attente'])->exists()) {
                return ApiResponse::conflict('Impossible de supprimer ce bien car il est lié à des contrats actifs ou en attente.');
            }

            $this->bienService->deleteBien($bien);

            return ApiResponse::success(null, 'Bien supprimé avec succès !');
        } catch (\Exception $e) {
            \Log::error('Erreur suppression bien', ['id' => $bien->id, 'error' => $e->getMessage()]);

            return ApiResponse::error('Une erreur technique est survenue lors de la suppression.', 500);
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

            return ApiResponse::success(null, 'Image supprimée');
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), 500);
        }
    }
}
