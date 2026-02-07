<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Proprietaire extends Model
{
    use HasFactory, SoftDeletes;

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
