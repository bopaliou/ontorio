<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RevisionLoyer extends Model
{
    protected $table = 'revisions_loyer';
    
    protected $fillable = [
        'contrat_id',
        'ancien_montant',
        'nouveau_montant',
        'date_effet',
        'motif',
        'pourcentage_augmentation',
        'justification',
        'created_by'
    ];

    protected $casts = [
        'ancien_montant' => 'decimal:2',
        'nouveau_montant' => 'decimal:2',
        'pourcentage_augmentation' => 'decimal:2',
        'date_effet' => 'date',
    ];

    /**
     * Le contrat concerné par la révision
     */
    public function contrat()
    {
        return $this->belongsTo(Contrat::class);
    }

    /**
     * L'utilisateur qui a créé la révision
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Label du motif de révision
     */
    public function getMotifLabelAttribute()
    {
        return match($this->motif) {
            'indexation_annuelle' => 'Indexation annuelle',
            'travaux_amelioration' => 'Travaux d\'amélioration',
            'renouvellement_bail' => 'Renouvellement du bail',
            'accord_parties' => 'Accord entre les parties',
            'revision_marche' => 'Révision marché',
            'autre' => 'Autre',
            default => $this->motif
        };
    }

    /**
     * Calcul automatique du pourcentage d'augmentation
     */
    public function calculerPourcentage()
    {
        if ($this->ancien_montant > 0) {
            $diff = $this->nouveau_montant - $this->ancien_montant;
            return round(($diff / $this->ancien_montant) * 100, 2);
        }
        return 0;
    }

    /**
     * Boot du modèle pour calculer auto le pourcentage
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($revision) {
            if (!$revision->pourcentage_augmentation && $revision->ancien_montant > 0) {
                $revision->pourcentage_augmentation = $revision->calculerPourcentage();
            }
        });
    }
}
