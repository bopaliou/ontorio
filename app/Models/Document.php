<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Document extends Model
{
    use SoftDeletes;

    protected $fillable = ['type', 'nom_original', 'chemin_fichier', 'entite_type', 'entite_id'];

    /**
     * Génère une référence externe unique (non-séquentielle) pour le document.
     */
    public function getReferenceExterneAttribute()
    {
        return 'DOC-' . strtoupper(substr(md5($this->id . config('app.key')), 0, 8)) . '-' . $this->id;
    }

    public function documentable()
    {
        return $this->morphTo('documentable', 'entite_type', 'entite_id');
    }
}
