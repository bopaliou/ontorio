<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contrat extends Model
{
    use HasFactory;

    protected $fillable = [
        'bien_id',
        'locataire_id',
        'date_debut',
        'date_fin',
        'loyer_montant',
        'statut',
        'caution',
        'frais_dossier',
        'type_bail',
        'date_signature',
        'renouvellement_auto',
        'preavis_mois',
    ];

    protected $casts = [
        'date_debut' => 'date',
        'date_fin' => 'date',
        'date_signature' => 'date',
        'caution' => 'decimal:2',
        'frais_dossier' => 'decimal:2',
        'loyer_montant' => 'decimal:2',
        'renouvellement_auto' => 'boolean',
    ];

    public function bien()
    {
        return $this->belongsTo(Bien::class);
    }

    public function locataire()
    {
        return $this->belongsTo(Locataire::class);
    }

    public function loyers()
    {
        return $this->hasMany(Loyer::class);
    }

    public function paiements()
    {
        return $this->hasManyThrough(Paiement::class, Loyer::class);
    }

    /**
     * Historique des révisions de loyer
     */
    public function revisionsLoyer()
    {
        return $this->hasMany(RevisionLoyer::class)->orderBy('date_effet', 'desc');
    }

    /**
     * Réviser le loyer avec traçabilité
     */
    public function reviserLoyer(float $nouveauMontant, string $motif = 'indexation_annuelle', ?string $justification = null)
    {
        $ancienMontant = $this->loyer_montant;

        // Créer l'historique de révision
        $revision = RevisionLoyer::create([
            'contrat_id' => $this->id,
            'ancien_montant' => $ancienMontant,
            'nouveau_montant' => $nouveauMontant,
            'date_effet' => now(),
            'motif' => $motif,
            'justification' => $justification,
            'created_by' => auth()->id(),
        ]);

        // Mettre à jour le montant du contrat
        $this->update(['loyer_montant' => $nouveauMontant]);

        return $revision;
    }

    /**
     * Vérifie si le contrat expire bientôt (dans les 60 jours)
     */
    public function getExpireBientotAttribute()
    {
        if (! $this->date_fin) {
            return false;
        }

        return $this->date_fin->diffInDays(now()) <= 60 && $this->date_fin->isFuture();
    }

    /**
     * Nombre de jours restants avant expiration
     */
    public function getJoursRestantsAttribute()
    {
        if (! $this->date_fin) {
            return null;
        }

        return $this->date_fin->isFuture() ? $this->date_fin->diffInDays(now()) : 0;
    }
}
