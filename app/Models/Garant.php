<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Garant extends Model
{
    protected $fillable = [
        'locataire_id',
        'nom',
        'telephone',
        'email',
        'adresse',
        'profession',
        'revenus_mensuels',
        'piece_identite',
        'justificatif_revenus',
        'lien_locataire',
        'notes',
    ];

    protected $casts = [
        'revenus_mensuels' => 'decimal:2',
    ];

    /**
     * Le locataire associé à ce garant
     */
    public function locataire()
    {
        return $this->belongsTo(Locataire::class);
    }

    /**
     * URL de la pièce d'identité
     */
    public function getPieceIdentiteUrlAttribute()
    {
        return $this->piece_identite ? asset('storage/'.$this->piece_identite) : null;
    }

    /**
     * URL du justificatif de revenus
     */
    public function getJustificatifRevenusUrlAttribute()
    {
        return $this->justificatif_revenus ? asset('storage/'.$this->justificatif_revenus) : null;
    }

    /**
     * Label du lien avec le locataire
     */
    public function getLienLabelAttribute()
    {
        return match ($this->lien_locataire) {
            'parent' => 'Parent/Famille',
            'employeur' => 'Employeur',
            'autre' => 'Autre',
            default => $this->lien_locataire
        };
    }
}
