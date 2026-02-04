<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bien extends Model
{
    use HasFactory;

    protected $fillable = [
        'proprietaire_id',
        'nom',
        'adresse',
        'ville',
        'type',
        'surface',
        'statut',
        'loyer_mensuel',
        'description',
        'nombre_pieces',
        'meuble',
    ];

    protected $casts = [
        'meuble' => 'boolean',
        'surface' => 'decimal:2',
    ];

    public function proprietaire()
    {
        return $this->belongsTo(Proprietaire::class);
    }

    public function contrats()
    {
        return $this->hasMany(Contrat::class);
    }

    public function contratActif()
    {
        return $this->hasOne(Contrat::class)->where('statut', 'actif');
    }

    public function depenses()
    {
        return $this->hasMany(Depense::class);
    }

    /**
     * Toutes les images du bien
     */
    public function images()
    {
        return $this->hasMany(BienImage::class)->orderBy('ordre');
    }

    /**
     * Image principale du bien (pour miniature)
     */
    public function imagePrincipale()
    {
        return $this->hasOne(BienImage::class)->where('principale', true);
    }

    /**
     * Récupérer l'URL de l'image principale ou un placeholder
     */
    public function getImagePrincipaleUrlAttribute()
    {
        $image = $this->imagePrincipale ?? $this->images->first();

        return $image ? asset('storage/'.$image->chemin) : null;
    }
}
