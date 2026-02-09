<?php

namespace App\Services;

use App\Helpers\ActivityLogger;
use App\Models\Bien;
use Illuminate\Support\Facades\DB;

class BienService
{
    /**
     * Créer un nouveau bien
     */
    public function createBien(array $data): Bien
    {
        return DB::transaction(function () use ($data) {
            $bien = Bien::create($data);

            ActivityLogger::log('Création Bien', "Ajout du bien {$bien->nom}", 'success', $bien);

            return $bien;
        });
    }

    /**
     * Mettre à jour un bien
     */
    public function updateBien(Bien $bien, array $data): Bien
    {
        return DB::transaction(function () use ($bien, $data) {
            $bien->update($data);

            ActivityLogger::log('Modification Bien', "Mise à jour du bien {$bien->nom}", 'info', $bien);

            return $bien;
        });
    }

    /**
     * Supprimer un bien
     */
    public function deleteBien(Bien $bien): void
    {
        DB::transaction(function () use ($bien) {
            $id = $bien->id;
            $nom = $bien->nom;
            $bien->delete();

            ActivityLogger::log('Suppression Bien', "Suppression du bien {$nom} (#{$id})", 'warning');
        });
    }
}
