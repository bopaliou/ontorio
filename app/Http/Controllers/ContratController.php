<?php

namespace App\Http\Controllers;

use App\Helpers\ActivityLogger;
use App\Http\Requests\StoreContratRequest;
use App\Http\Responses\ApiResponse;
use App\Models\Bien;
use App\Models\Contrat;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
class ContratController extends Controller
{
    protected $contratService;

    public function __construct(\App\Services\ContratService $contratService)
    {
        $this->contratService = $contratService;
    }

    /**
     * Store a newly created contrat in storage.
     */
    public function store(StoreContratRequest $request)
    {
        // Vérifier si le bien est déjà occupé
        $bien = \App\Models\Bien::find($request->bien_id);
        if ($bien->statut === 'occupé' && $bien->contrats()->where('statut', 'actif')->exists()) {
            return ApiResponse::error('Ce bien est déjà occupé par un contrat actif.', 422);
        }

        try {
            $data = $request->validated();
            $data['statut'] = 'actif';
            $contrat = $this->contratService->createContract($data);

            // Mettre à jour le statut du bien
            $bien->update(['statut' => 'occupé']);

            return ApiResponse::created($contrat, 'Bail activé avec succès !');
        } catch (\Exception $e) {
            \Log::error('Erreur activation bail', ['error' => $e->getMessage()]);

            return ApiResponse::error('Une erreur technique est survenue.', 500);
        }
    }

    /**
     * Update the specified contract.
     */
    public function update(\App\Http\Requests\StoreContratRequest $request, Contrat $contrat)
    {
        try {
            DB::beginTransaction();

            $oldMontant = $contrat->loyer_montant;
            $contrat = $this->contratService->updateContract($contrat, $request->validated());

            // Propagation aux loyers impayés (tous)
            if ($oldMontant != $request->loyer_montant) {
                \App\Models\Loyer::where('contrat_id', $contrat->id)
                    ->whereIn('statut', ['émis', 'en_retard'])
                    ->update(['montant' => $request->loyer_montant]);

                ActivityLogger::log('Modification Bail', "Mise à jour des loyers impayés pour bail #{$contrat->id}", 'info', $contrat);
            }

            DB::commit();

            return ApiResponse::success($contrat, 'Contrat mis à jour avec succès.');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Erreur mise à jour bail', ['id' => $contrat->id, 'error' => $e->getMessage()]);

            return ApiResponse::error('Une erreur technique est survenue.', 500);
        }
    }

    /**
     * Terminate/Archive a contract (Soft Delete logic or Status Update)
     */
    public function destroy(Contrat $contrat)
    {
        try {
            // Task 3.2: Vérifier si des loyers impayés existent
            $hasUnpaid = $contrat->loyers()->whereIn('statut', ['émis', 'en_retard', 'partiellement_payé'])->exists();
            if ($hasUnpaid) {
                return ApiResponse::conflict('Impossible de supprimer ce contrat car il possède des loyers impayés ou partiellement payés.');
            }

            DB::beginTransaction();

            $bienId = $contrat->bien_id;
            $this->contratService->deleteContract($contrat);

            // Vérifier s'il reste d'autres contrats actifs pour ce bien
            $hasActive = Contrat::where('bien_id', $bienId)->where('statut', 'actif')->exists();
            if (! $hasActive) {
                \App\Models\Bien::where('id', $bienId)->update(['statut' => 'libre']);
            }

            DB::commit();

            return ApiResponse::success(null, 'Contrat supprimé et bien libéré.');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Erreur suppression bail', ['id' => $contrat->id, 'error' => $e->getMessage()]);

            return ApiResponse::error('Une erreur est survenue lors de la suppression.', 500);
        }
    }

    /**
     * Generate PDF for the contract.
     */
    public function print(Contrat $contrat)
    {
        $pdf = Pdf::loadView('pdf.contrat_location', compact('contrat'));

        return $pdf->stream("Contrat_Location_C{$contrat->id}.pdf");
    }
}
