<?php

namespace App\Services;

use App\Helpers\ActivityLogger;
use App\Models\Proprietaire;
use Illuminate\Support\Facades\DB;

class ProprietaireService
{
    public function createProprietaire(array $data): Proprietaire
    {
        return DB::transaction(function () use ($data) {
            $prop = Proprietaire::create($data);
            ActivityLogger::log('Création Propriétaire', "Ajout du propriétaire {$prop->nom}", 'success', $prop);

            return $prop;
        });
    }

    public function updateProprietaire(Proprietaire $proprietaire, array $data): Proprietaire
    {
        return DB::transaction(function () use ($proprietaire, $data) {
            $proprietaire->update($data);
            ActivityLogger::log('Modification Propriétaire', "Mise à jour du propriétaire {$proprietaire->nom}", 'info', $proprietaire);

            return $proprietaire;
        });
    }

    public function deleteProprietaire(Proprietaire $proprietaire): void
    {
        if ($proprietaire->biens()->exists()) {
            throw new \Exception('Impossible de supprimer un propriétaire lié à des biens.');
        }

        DB::transaction(function () use ($proprietaire) {
            $nom = $proprietaire->nom;
            $proprietaire->delete();
            ActivityLogger::log('Suppression Propriétaire', "Suppression du propriétaire {$nom}", 'warning');
        });
    }
}
