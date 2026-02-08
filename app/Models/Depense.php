<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Depense extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'bien_id',
        'titre',
        'description',
        'montant',
        'date_depense',
        'categorie',
        'justificatif',
        'statut',
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
