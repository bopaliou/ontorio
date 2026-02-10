<?php

namespace App\Services;

use App\Helpers\ActivityLogger;
use App\Models\Contrat;
use Illuminate\Support\Facades\DB;

class ContratService
{
    /**
     * Créer un nouveau contrat
     */
    public function createContract(array $data): Contrat
    {
        return DB::transaction(function () use ($data) {
            $contrat = Contrat::create($data);

            ActivityLogger::log('Création Contrat', "Nouveau contrat #{$contrat->id} pour le bien {$contrat->bien->nom}", 'success', $contrat);

            return $contrat;
        });
    }

    /**
     * Mettre à jour un contrat
     */
    public function updateContract(Contrat $contrat, array $data): Contrat
    {
        return DB::transaction(function () use ($contrat, $data) {
            $contrat->update($data);

            ActivityLogger::log('Modification Contrat', "Mise à jour du contrat #{$contrat->id}", 'info', $contrat);

            return $contrat;
        });
    }

    /**
     * Supprimer un contrat (si possible)
     */
    public function deleteContract(Contrat $contrat): void
    {
        DB::transaction(function () use ($contrat) {
            // Task 3.2: Vérifier si des paiements sont liés ?
            // (On part du principe que SoftDeletes gère l'intégrité,
            // mais on peut ajouter une vérification métier ici si nécessaire)

            $id = $contrat->id;
            $contrat->delete();

            ActivityLogger::log('Suppression Contrat', "Suppression du contrat #{$id}", 'warning');
        });
    }
}
