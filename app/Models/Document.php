<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    protected $fillable = ['type', 'nom_original', 'chemin_fichier', 'entite_type', 'entite_id'];

    public function documentable()
    {
        return $this->morphTo('documentable', 'entite_type', 'entite_id');
    }
}
