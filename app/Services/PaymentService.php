<?php

namespace App\Services;

use App\Models\Loyer;
use App\Models\Paiement;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Helpers\ActivityLogger;

class PaymentService
{
    /**
     * Enregistrer un paiement avec verrouillage pessimiste
     */
    public function recordPayment(array $data): Paiement
    {
        return DB::transaction(function () use ($data) {
            // 1. Verrouillage du loyer
            $loyer = Loyer::lockForUpdate()->findOrFail($data['loyer_id']);

            // 2. Vérification métier: déjà payé ?
            $totalPaid = $loyer->paiements()->sum('montant');
            $due = $loyer->montant + ($loyer->penalite ?? 0);
            
            if ($totalPaid >= $due) {
                throw new \Exception('Ce loyer est déjà entièrement payé.');
            }

            // 3. Gestion du fichier de preuve
            $preuvePath = null;
            if (isset($data['preuve']) && $data['preuve'] instanceof \Illuminate\Http\UploadedFile) {
                $file = $data['preuve'];
                $filename = 'preuve_'.time().'_'.uniqid().'.'.$file->getClientOriginalExtension();
                $preuvePath = $file->storeAs('paiements', $filename, 'public');
            }

            // 4. Création du paiement
            $paiement = Paiement::create([
                'loyer_id' => $loyer->id,
                'montant' => $data['montant'],
                'date_paiement' => $data['date_paiement'],
                'mode' => $data['mode'],
                'reference' => 'PAY-'.strtoupper(uniqid()),
                'preuve' => $preuvePath,
                'user_id' => auth()->id(),
            ]);

            // 5. Mise à jour du statut du loyer
            $newTotalPaid = $totalPaid + $data['montant'];
            if ($newTotalPaid >= $due - 0.01) {
                $loyer->statut = 'payé';
            } else {
                $loyer->statut = 'partiellement_payé';
            }
            $loyer->save();

            ActivityLogger::log('Encaissement Loyer', "Paiement de {$data['montant']} F pour le loyer #{$loyer->id}", 'success');

            return $paiement;
        });
    }

    /**
     * Supprimer un paiement et restaurer le statut du loyer
     */
    public function deletePayment(Paiement $paiement): void
    {
        DB::transaction(function () use ($paiement) {
            $loyer = $paiement->loyer;
            $montant = $paiement->montant;

            // Supprimer le fichier de preuve si existant
            if ($paiement->preuve && Storage::disk('public')->exists($paiement->preuve)) {
                Storage::disk('public')->delete($paiement->preuve);
            }

            $paiement->delete();

            if ($loyer) {
                $sommePaiements = $loyer->paiements()->sum('montant');
                
                if ($sommePaiements < $loyer->montant) {
                    $moisLoyer = \Carbon\Carbon::parse($loyer->mois);
                    $now = \Carbon\Carbon::now();

                    if ($now->format('Y-m') > $moisLoyer->format('Y-m')) {
                        $loyer->statut = 'en_retard';
                    } else {
                        $loyer->statut = 'émis';
                    }

                    if ($sommePaiements > 0) {
                        $loyer->statut = 'partiellement_payé';
                    }

                    $loyer->save();
                }
            }

            ActivityLogger::log('Annulation Encaissement', "Suppression paiement de {$montant} F pour loyer #".($loyer ? $loyer->id : 'unknown'), 'warning');
        });
    }
}
