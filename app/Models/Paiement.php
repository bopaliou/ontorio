<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Paiement extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['loyer_id', 'montant', 'mode', 'date_paiement', 'preuve', 'reference', 'user_id'];

    protected $casts = [
        'date_paiement' => 'date',
        'montant' => 'decimal:2',
    ];

    public function loyer()
    {
        return $this->belongsTo(Loyer::class);
    }

    // Status update is handled by Loyer::updateStatus() via PaymentService
    // (includes penalty-aware comparison with tolerance)
}
