<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Locataire extends Model
{
    protected $fillable = ['nom', 'email', 'telephone', 'adresse', 'pieces_identite'];

    protected $appends = ['cni'];

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
}
