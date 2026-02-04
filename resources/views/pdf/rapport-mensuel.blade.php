<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Rapport Mensuel - Ontario Group</title>
    <style>
        @page { margin: 40px; }
        body { font-family: 'Helvetica', sans-serif; color: #333; line-height: 1.6; font-size: 12px; margin: 0; padding: 0; }

        /* Color Palette */
        .text-brand { color: #274256; }
        .bg-brand { background-color: #274256; color: white; }

        .header {
            border-bottom: 2px solid #274256;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .logo-text {
            font-size: 20px;
            font-weight: 900;
            color: #274256;
            text-transform: uppercase;
        }
        .logo-sub {
            font-size: 9px;
            color: #64748b;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .kpi-grid { display: table; width: 100%; margin: 30px 0; }
        .kpi-item { display: table-cell; width: 25%; padding: 10px; text-align: center; border-right: 1px solid #f0f0f0; }
        .kpi-item:last-child { border-right: none; }
        .kpi-label { font-size: 9px; text-transform: uppercase; color: #94a3b8; font-weight: bold; margin-bottom: 5px; }
        .kpi-value { font-size: 20px; font-weight: bold; color: #274256; }
        .kpi-value.green { color: #10b981; }
        .kpi-value.red { color: #ef4444; }

        .section-title { font-weight: bold; color: #274256; border-bottom: 2px solid #e2e8f0; margin: 30px 0 15px; padding-bottom: 8px; font-size: 14px; text-transform: uppercase; }

        .data-table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        .data-table th { background: #f8fafc; padding: 10px; text-align: left; font-size: 10px; text-transform: uppercase; border-bottom: 1px solid #cbd5e1; color: #64748b; }
        .data-table td { padding: 8px 10px; border-bottom: 1px solid #e2e8f0; font-size: 11px; }
        .data-table tr:hover { background: #f1f5f9; }

        .footer { position: fixed; bottom: -20px; left: 0; right: 0; text-align: center; font-size: 8px; color: #94a3b8; border-top: 1px solid #e2e8f0; padding-top: 10px; }

        .badge { display: inline-block; padding: 3px 8px; border-radius: 999px; font-size: 8px; font-weight: bold; text-transform: uppercase; }
        .badge-success { background: #dcfce7; color: #166534; }
        .badge-warning { background: #fef9c3; color: #854d0e; }
        .badge-danger { background: #fee2e2; color: #991b1b; }

        .highlight-box { background: #eff6ff; border-left: 3px solid #274256; padding: 15px; margin: 20px 0; font-size: 11px; }
        .page-break { page-break-after: always; }
    </style>
</head>
<body>
    <!-- HEADER -->
    <div class="header">
        <table style="width: 100%;">
            <tr>
                <td style="width: 50%; vertical-align: top;">
                    <img src="{{ public_path('images/ontorio-logo.png') }}" alt="Ontario Group Logo" style="max-height: 60px; margin-bottom: 5px;">
                    <div class="logo-sub">Bien loger dans un bon logement</div>
                    <div style="margin-top: 10px; font-size: 10px; color: #64748b;">
                        5 Félix Faure x Colbert<br>
                        Dakar Plateau, Sénégal<br>
                        BP 06813
                    </div>
                </td>
                <td style="width: 50%; text-align: right; vertical-align: top; font-size: 10px;">
                    <strong>Rapport Mensuel</strong><br>
                    Tél : +221 33 822 32 67 / 33 842 05 80<br>
                    Email : commercial@ontariogroup.net
                </td>
            </tr>
        </table>
    </div>

    <div style="text-align: center; margin-bottom: 30px;">
        <h1 style="margin: 0; font-size: 24px; color: #274256; text-transform: uppercase; font-weight: 900;">RAPPORT DE GESTION</h1>
        <p style="margin: 5px 0; color: #64748b; font-size: 12px; font-weight: bold;">Période : {{ \Carbon\Carbon::parse($mois)->translatedFormat('F Y') }}</p>
    </div>

    <!-- KPIs Principaux -->
    <div class="kpi-grid">
        <div class="kpi-item">
            <div class="kpi-label">Revenus Mensuels</div>
            <div class="kpi-value">{{ number_format($data['kpis']['revenu_mensuel'], 0, ',', ' ') }} F</div>
        </div>
        <div class="kpi-item">
            <div class="kpi-label">Taux d'Occupation</div>
            <div class="kpi-value green">{{ $data['kpis']['taux_occupation'] }}%</div>
        </div>
        <div class="kpi-item">
            <div class="kpi-label">Impayés</div>
            <div class="kpi-value red">{{ number_format($data['kpis']['impayes'], 0, ',', ' ') }} F</div>
        </div>
        <div class="kpi-item">
            <div class="kpi-label">Taux de Collecte</div>
            <div class="kpi-value">{{ $data['kpis']['taux_collecte'] }}%</div>
        </div>
    </div>

    <div class="section-title">📊 Synthèse Financière</div>

    <table class="data-table">
        <thead>
            <tr>
                <th>Indicateur</th>
                <th style="text-align: right;">Montant (F CFA)</th>
                <th style="text-align: center;">Note</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Loyers Facturés (Émis)</td>
                <td style="text-align: right; font-weight: bold;">{{ number_format($data['kpis']['loyers_emis'], 0, ',', ' ') }}</td>
                <td style="text-align: center;">-</td>
            </tr>
            <tr>
                <td>Loyers Encaissés (Perçus)</td>
                <td style="text-align: right; font-weight: bold; color: #10b981;">{{ number_format($data['kpis']['loyers_payes'], 0, ',', ' ') }}</td>
                <td style="text-align: center;"><span class="badge badge-success">OK</span></td>
            </tr>
            <tr>
                <td>Commissions Agence (Estim. 10%)</td>
                <td style="text-align: right; font-weight: bold; color: #274256;">{{ number_format($data['kpis']['commission_mensuelle'], 0, ',', ' ') }}</td>
                <td style="text-align: center;"><span class="badge badge-success">Revenu</span></td>
            </tr>
            <tr>
                <td>Reste à Recouvrer (Impayés)</td>
                <td style="text-align: right; font-weight: bold; color: #ef4444;">{{ number_format($data['kpis']['total_impaye'], 0, ',', ' ') }}</td>
                <td style="text-align: center;">
                    @if($data['kpis']['total_impaye'] > 0)
                    <span class="badge badge-danger">Action Requise</span>
                    @else
                    <span class="badge badge-success">Aucun</span>
                    @endif
                </td>
            </tr>
        </tbody>
    </table>

    <div class="highlight-box">
        <strong>💡 ANALYSE :</strong> Le taux de collecte de ce mois s'élève à <strong>{{ $data['kpis']['taux_collecte'] }}%</strong>.
        @if($data['kpis']['taux_collecte'] >= 90)
            Excellente performance de recouvrement.
        @elseif($data['kpis']['taux_collecte'] >= 75)
            Bonne performance, maintenir les efforts de relance.
        @else
            Performance en dessous des objectifs. Il est impératif d'intensifier les relances.
        @endif
    </div>

    <div class="page-break"></div>

    <div class="section-title">🏢 État du Parc Immobilier</div>

    <table class="data-table">
        <thead>
            <tr>
                <th>Bien Immobilier</th>
                <th>Type</th>
                <th style="text-align: center;">Statut</th>
                <th style="text-align: right;">Valeur Locative</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data['biens_list']->take(20) as $bien)
            <tr>
                <td>
                    <strong>{{ $bien->nom }}</strong><br>
                    <span style="font-size: 9px; color: #64748b;">{{ $bien->ville }}</span>
                </td>
                <td>{{ ucfirst($bien->type) }}</td>
                <td style="text-align: center;">
                    @if($bien->statut === 'libre' || $bien->statut === 'disponible')
                        <span class="badge badge-success">Libre</span>
                    @elseif($bien->statut === 'occupé')
                        <span class="badge badge-warning">Occupé</span>
                    @else
                        <span class="badge badge-danger">{{ $bien->statut }}</span>
                    @endif
                </td>
                <td style="text-align: right; font-weight: bold;">{{ number_format($bien->loyer_mensuel, 0, ',', ' ') }} F</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="section-title">📈 Évolution des Revenus (6 mois)</div>

    <table class="data-table">
        <thead>
            <tr>
                <th>Mois</th>
                <th style="text-align: right;">Revenus Encaissés</th>
                <th style="text-align: right;">Commissions (10%)</th>
                <th style="text-align: center;">Tendance</th>
            </tr>
        </thead>
        <tbody>
            @foreach(array_reverse($data['revenus_par_mois']) as $rev)
            <tr>
                <td><strong>{{ \Carbon\Carbon::parse($rev['mois'])->translatedFormat('F Y') }}</strong></td>
                <td style="text-align: right;">{{ number_format($rev['montant'], 0, ',', ' ') }} F</td>
                <td style="text-align: right; color: #10b981;">{{ number_format($rev['montant'] * 0.10, 0, ',', ' ') }} F</td>
                <td style="text-align: center;">
                    @if($rev['montant'] > 0) <span class="badge badge-success">Active</span> @else <span class="badge badge-danger">-</span> @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Ontario Group S.A. - Bien loger dans un bon logement - Rapport Confidentiel - Généré le {{ date('d/m/Y H:i') }}
    </div>
</body>
</html>
