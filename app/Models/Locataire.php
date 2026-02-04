<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Locataire extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom',
        'email',
        'telephone',
        'adresse',
        'pieces_identite',
        'profession',
        'revenus_mensuels',
    ];

    protected $appends = ['cni'];

    protected $casts = [
        'revenus_mensuels' => 'decimal:2',
    ];

    public function getCniAttribute()
    {
        return $this->pieces_identite;
    }

    public function setCniAttribute($value)
    {
        $this->attributes['pieces_identite'] = $value;
    }

    public function contrats()
    {
        return $this->hasMany(Contrat::class);
    }

    public function documents()
    {
        return $this->morphMany(Document::class, 'documentable', 'entite_type', 'entite_id');
    }

    /**
     * Les garants du locataire
     */
    public function garants()
    {
        return $this->hasMany(Garant::class);
    }

    /**
     * Vérifie si le locataire a au moins un garant
     */
    public function getAGarantAttribute()
    {
        return $this->garants()->exists();
    }

    /**
     * Contrat actif du locataire
     */
    public function contratActif()
    {
        return $this->hasOne(Contrat::class)->where('statut', 'actif');
    }
}
