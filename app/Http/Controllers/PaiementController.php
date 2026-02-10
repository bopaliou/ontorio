<?php

namespace App\Http\Controllers;

use App\Http\Responses\ApiResponse;
use App\Models\Paiement;
use Illuminate\Http\Request;

class PaiementController extends Controller
{
    protected $paymentService;

    public function __construct(\App\Services\PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    /**
     * Display a listing of payments.
     */
    public function index()
    {
        $paiements = Paiement::with('loyer')->get();

        return ApiResponse::success($paiements);
    }

    /**
     * Display the specified payment.
     */
    public function show(Paiement $paiement)
    {
        return ApiResponse::success($paiement);
    }

    /**
     * Store a newly created payment in storage.
     */
    public function store(\App\Http\Requests\StorePaiementRequest $request)
    {
        try {
            $paiement = $this->paymentService->recordPayment($request->validated());

            return ApiResponse::created($paiement, 'Paiement enregistré avec succès !');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return ApiResponse::notFound('Loyer non trouvé.');
        } catch (\Exception $e) {
            if ($e->getMessage() === 'Ce loyer est déjà entièrement payé.') {
                return ApiResponse::conflict($e->getMessage());
            }

            \Log::error('Erreur enregistrement paiement', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return ApiResponse::error('Une erreur est survenue lors de l\'enregistrement.', 500);
        }
    }

    /**
     * Remove the specified payment from storage.
     */
    public function destroy(Paiement $paiement)
    {
        try {
            $this->paymentService->deletePayment($paiement);

            return ApiResponse::success(null, 'Paiement supprimé et statut du loyer restauré.');
        } catch (\Exception $e) {
            \Log::error('Erreur suppression paiement', [
                'id' => $paiement->id,
                'error' => $e->getMessage(),
            ]);

            return ApiResponse::error('Une erreur est survenue lors de la suppression.', 500);
        }
    }
}
