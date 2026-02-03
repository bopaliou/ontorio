<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Proprietaire extends Model
{
    protected $fillable = ['nom', 'email', 'telephone', 'adresse'];

    public function biens()
    {
        return $this->hasMany(Bien::class);
    }

    public function depenses()
    {
        return $this->hasManyThrough(Depense::class, Bien::class);
    }

    public function contrats()
    {
        return $this->hasManyThrough(Contrat::class, Bien::class);
    }
}
