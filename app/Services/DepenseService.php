<?php

namespace App\Services;

use App\Helpers\ActivityLogger;
use App\Models\Depense;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class DepenseService
{
    public function createDepense(array $data, $justificatif = null): Depense
    {
        return DB::transaction(function () use ($data, $justificatif) {
            if ($justificatif) {
                $data['justificatif'] = $justificatif->store('depenses', 'public');
            }

            $depense = Depense::create($data);
            ActivityLogger::log('Création Dépense', "Ajout d'une dépense de {$depense->montant} F pour {$depense->bien->nom}", 'success', $depense);

            return $depense;
        });
    }

    public function updateDepense(Depense $depense, array $data, $justificatif = null): Depense
    {
        return DB::transaction(function () use ($depense, $data, $justificatif) {
            if ($justificatif) {
                if ($depense->justificatif && Storage::disk('public')->exists($depense->justificatif)) {
                    Storage::disk('public')->delete($depense->justificatif);
                }
                $data['justificatif'] = $justificatif->store('depenses', 'public');
            }

            $depense->update($data);
            ActivityLogger::log('Modification Dépense', "Mise à jour de la dépense #{$depense->id}", 'info', $depense);

            return $depense;
        });
    }

    public function deleteDepense(Depense $depense): void
    {
        DB::transaction(function () use ($depense) {
            if ($depense->justificatif && Storage::disk('public')->exists($depense->justificatif)) {
                Storage::disk('public')->delete($depense->justificatif);
            }
            $id = $depense->id;
            $depense->delete();
            ActivityLogger::log('Suppression Dépense', "Suppression de la dépense #{$id}", 'warning');
        });
    }
}
