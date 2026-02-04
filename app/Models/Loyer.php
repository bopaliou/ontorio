<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Loyer extends Model
{
    protected $fillable = ['contrat_id', 'mois', 'montant', 'commission', 'statut', 'note_annulation', 'penalite', 'taux_penalite'];

    // IMPORTANT: Retirer les appends coûteux pour éviter N+1 queries
    // Utiliser $loyer->append(['date_echeance', 'jours_retard']) uniquement quand nécessaire
    protected $appends = [];

    protected $casts = [
        'penalite' => 'decimal:2',
        'taux_penalite' => 'decimal:2',
        'montant' => 'decimal:2',
    ];

    /**
     * Scope pour eager loading du montant payé
     * Usage: Loyer::withMontantPaye()->get()
     */
    public function scopeWithMontantPaye($query)
    {
        return $query->withSum('paiements', 'montant');
    }

    /**
     * Date d'échéance = 5 du mois suivant le mois du loyer
     */
    public function getDateEcheanceAttribute()
    {
        if (! $this->mois) {
            return null;
        }
        $moisLoyer = Carbon::parse($this->mois);

        return $moisLoyer->copy()->addMonth()->day(5);
    }

    /**
     * Montant payé - utilise eager loading si disponible
     */
    public function getMontantPayeCacheAttribute()
    {
        // Si eager loaded via withSum, utiliser directement
        if (isset($this->attributes['paiements_sum_montant'])) {
            return $this->attributes['paiements_sum_montant'] ?? 0;
        }

        // Fallback: requête séparée (à éviter)
        return $this->paiements()->sum('montant') ?? 0;
    }

    /**
     * Nombre de jours de retard (0 si payé ou pas encore en retard)
     */
    public function getJoursRetardAttribute()
    {
        if (in_array($this->statut, ['payé', 'annulé'])) {
            return 0;
        }

        $echeance = $this->date_echeance;
        if (! $echeance) {
            return 0;
        }

        $now = Carbon::now();

        if ($now->gt($echeance)) {
            return $now->diffInDays($echeance);
        }

        return 0;
    }

    /**
     * Vérifie si le loyer est en retard
     */
    public function getEstEnRetardAttribute()
    {
        return $this->jours_retard > 0;
    }

    /**
     * Calcul du reste à payer (montant + pénalités - paiements)
     */
    public function getResteAPayerAttribute()
    {
        $total = $this->montant + ($this->penalite ?? 0);
        $paye = $this->montant_paye_cache;

        return max(0, $total - $paye);
    }

    /**
     * Calcule et applique les pénalités de retard
     * Appeler via une commande artisan quotidienne
     */
    public function calculerPenalite()
    {
        if (! $this->est_en_retard || in_array($this->statut, ['payé', 'annulé'])) {
            return 0;
        }

        $tauxMensuel = ($this->taux_penalite ?? 10) / 100; // 10% par défaut
        $moisRetard = ceil($this->jours_retard / 30);

        // Pénalité = montant * taux * nombre de mois de retard (plafonné à 3 mois)
        $moisRetard = min($moisRetard, 3);
        $penalite = $this->montant * $tauxMensuel * $moisRetard;

        $this->penalite = round($penalite, 2);
        $this->save();

        return $this->penalite;
    }

    public function contrat()
    {
        return $this->belongsTo(Contrat::class);
    }

    public function paiements()
    {
        return $this->hasMany(Paiement::class);
    }

    /**
     * @deprecated Utiliser montant_paye_cache avec withMontantPaye()
     */
    public function montantPayé()
    {
        return $this->paiements()->sum('montant') ?? 0;
    }
}
