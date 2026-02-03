<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Loyer extends Model
{
    protected $fillable = ['contrat_id', 'mois', 'montant', 'commission', 'statut', 'note_annulation'];
    protected $appends = ['montant_paye_cache'];

    public function getMontantPayeCacheAttribute()
    {
        return $this->montantPayé();
    }

    public function contrat()
    {
        return $this->belongsTo(Contrat::class);
    }

    public function paiements()
    {
        return $this->hasMany(Paiement::class);
    }

    public function montantPayé()
    {
        return $this->paiements()->sum('montant') ?? 0;
    }
}
