<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Document extends Model
{
    use SoftDeletes;

    protected $fillable = ['type', 'nom_original', 'chemin_fichier', 'entite_type', 'entite_id'];

    protected $appends = ['type_label', 'url', 'extension'];

    /**
     * Libellé humain du type de document.
     */
    public function getTypeLabelAttribute()
    {
        $labels = [
            'cni' => 'Carte Nationale d\'Identité',
            'contrat_signe' => 'Contrat Signé',
            'attestation' => 'Attestation',
            'justificatif' => 'Justificatif',
            'autre' => 'Autre Document',
        ];

        return $labels[$this->type] ?? ucfirst($this->type);
    }

    /**
     * URL sécurisée pour accéder au document.
     */
    public function getUrlAttribute()
    {
        return get_secure_url($this->chemin_fichier);
    }

    /**
     * Extension du fichier.
     */
    public function getExtensionAttribute()
    {
        return strtolower(pathinfo($this->chemin_fichier, PATHINFO_EXTENSION));
    }

    /**
     * Génère une référence externe unique (non-séquentielle) pour le document.
     */
    public function getReferenceExterneAttribute()
    {
        return 'DOC-'.strtoupper(substr(md5($this->id.config('app.key')), 0, 8)).'-'.$this->id;
    }

    public function documentable()
    {
        return $this->morphTo('documentable', 'entite_type', 'entite_id');
    }
}
