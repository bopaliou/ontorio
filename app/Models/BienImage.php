<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BienImage extends Model
{
    protected $fillable = [
        'bien_id',
        'chemin',
        'nom_original',
        'principale',
        'ordre'
    ];

    protected $casts = [
        'principale' => 'boolean',
        'ordre' => 'integer',
    ];

    /**
     * L'image appartient à un bien
     */
    public function bien()
    {
        return $this->belongsTo(Bien::class);
    }

    /**
     * Obtenir l'URL complète de l'image
     */
    public function getUrlAttribute()
    {
        return asset('storage/' . $this->chemin);
    }
}
