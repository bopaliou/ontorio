<?php

namespace App\Helpers;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

class ActivityLogger
{
    /**
     * Enregistrer une activité
     *
     * @param string $action (ex: "Création Contrat")
     * @param string $description (ex: "Contrat #123 créé pour le locataire X")
     * @param string $type (info, success, warning, danger)
     * @param Model|null $target (Optionnel : l'objet concerné)
     */
    public static function log($action, $description, $type = 'info', $target = null)
    {
        if (Auth::check()) {
            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => $action,
                'description' => $description,
                'type' => $type,
                'target_type' => $target ? get_class($target) : null,
                'target_id' => $target ? $target->id : null,
            ]);
        }
    }
}
