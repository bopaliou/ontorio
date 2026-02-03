<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Depense extends Model
{
    protected $fillable = [
        'bien_id',
        'titre',
        'description',
        'montant',
        'date_depense',
        'categorie',
        'justificatif',
        'statut'
    ];

    protected $casts = [
        'date_depense' => 'date',
        'montant' => 'decimal:2',
    ];

    public function bien()
    {
        return $this->belongsTo(Bien::class);
    }
}
