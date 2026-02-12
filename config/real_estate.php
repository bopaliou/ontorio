<?php

return [
    'commission' => [
        // Taux d'honoraires de gestion appliqué sur les encaissements
        'rate' => (float) env('REAL_ESTATE_COMMISSION_RATE', 0.10),
    ],

    'penalties' => [
        // Version de la grille de pénalités pour traçabilité métier
        'version' => env('REAL_ESTATE_PENALTY_RULES_VERSION', 'v1'),

        // Valeurs de repli
        'default_rate_percent' => (float) env('REAL_ESTATE_PENALTY_DEFAULT_RATE_PERCENT', 10),
        'max_months' => (int) env('REAL_ESTATE_PENALTY_MAX_MONTHS', 3),

        // Grille versionnée par type de bail
        'rules' => [
            'v1' => [
                'default' => ['rate_percent' => 10, 'max_months' => 3],
                'habitation' => ['rate_percent' => 10, 'max_months' => 3],
                'commercial' => ['rate_percent' => 12, 'max_months' => 4],
                'professionnel' => ['rate_percent' => 12, 'max_months' => 4],
                'saisonnier' => ['rate_percent' => 8, 'max_months' => 2],
            ],
        ],
    ],
];
