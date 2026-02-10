<?php

namespace App\Enums;

enum LoyerStatus: string
{
    case PAYE = 'payé';
    case PARTIELLEMENT_PAYE = 'partiellement_payé';
    case EMIS = 'émis';
    case EN_RETARD = 'en_retard';
    case ANNULE = 'annulé';
}
