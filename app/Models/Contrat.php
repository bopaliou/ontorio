<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contrat extends Model
{
    protected $fillable = ['bien_id', 'locataire_id', 'date_debut', 'date_fin', 'loyer_montant', 'statut', 'caution', 'frais_dossier', 'type_bail', 'date_signature'];
    protected $casts = [
        'date_debut' => 'date', 
        'date_fin' => 'date',
        'date_signature' => 'date',
        'caution' => 'decimal:2',
        'frais_dossier' => 'decimal:2'
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
}
