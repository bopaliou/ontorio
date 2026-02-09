<?php

namespace App\Services;

use App\Helpers\ActivityLogger;
use App\Models\Locataire;
use Illuminate\Support\Facades\DB;

class LocataireService
{
    public function createLocataire(array $data): Locataire
    {
        return DB::transaction(function () use ($data) {
            $locataire = Locataire::create($data);
            ActivityLogger::log('Création Locataire', "Ajout du locataire {$locataire->nom}", 'success', $locataire);

            return $locataire;
        });
    }

    public function updateLocataire(Locataire $locataire, array $data): Locataire
    {
        return DB::transaction(function () use ($locataire, $data) {
            $locataire->update($data);
            ActivityLogger::log('Modification Locataire', "Mise à jour du locataire {$locataire->nom}", 'info', $locataire);

            return $locataire;
        });
    }

    public function deleteLocataire(Locataire $locataire): void
    {
        DB::transaction(function () use ($locataire) {
            $nom = $locataire->nom;
            $locataire->delete();
            ActivityLogger::log('Suppression Locataire', "Suppression du locataire {$nom}", 'warning');
        });
    }
}
