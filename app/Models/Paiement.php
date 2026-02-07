<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Paiement extends Model
{
    use HasFactory;

    protected $fillable = ['loyer_id', 'montant', 'mode', 'date_paiement', 'preuve', 'reference', 'user_id'];

    protected $casts = ['date_paiement' => 'date'];

    public function loyer()
    {
        return $this->belongsTo(Loyer::class);
    }
}
