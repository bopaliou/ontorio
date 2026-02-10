<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Rapport Mensuel - Ontario Group</title>
    <style>
        @page { 
            margin: 0; 
            size: A4;
        }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 11px;
            color: #1e293b;
            line-height: 1.6;
            margin: 0;
            padding: 0;
            background-color: #fff;
        }

        .page {
            position: relative;
            padding: 40px 50px;
            box-sizing: border-box;
            height: 100%;
        }

        /* Branding */
        .header {
            border-bottom: 4px solid #cb2d2d;
            padding-bottom: 25px;
            margin-bottom: 35px;
        }
        .agency-name {
            font-size: 22px;
            font-weight: 900;
            color: #1a2e3d;
            letter-spacing: -1px;
        }
        .header-sub {
            font-size: 9.5px;
            color: #64748b;
            margin-top: 5px;
        }

        /* Title Area */
        .report-header {
            text-align: center;
            margin-bottom: 40px;
        }
        .report-label {
            font-size: 10px;
            font-weight: 900;
            color: #cb2d2d;
            text-transform: uppercase;
            letter-spacing: 4px;
            margin-bottom: 8px;
        }
        .report-title {
            font-size: 26px;
            font-weight: 900;
            color: #1a2e3d;
            text-transform: uppercase;
            letter-spacing: -1px;
        }
        .report-period {
            font-size: 12px;
            color: #64748b;
            font-weight: 800;
            margin-top: 10px;
        }

        /* KPI Tiles */
        .kpi-container {
            width: 100%;
            margin-bottom: 35px;
        }
        .kpi-tile {
            width: 23%;
            float: left;
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            padding: 15px;
            border-radius: 12px;
            text-align: center;
        }
        .kpi-gap { width: 2.66%; float: left; height: 10px; }
        .kpi-label {
            font-size: 8px;
            font-weight: 900;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 8px;
        }
        .kpi-value {
            font-size: 18px;
            font-weight: 900;
            color: #1a2e3d;
        }
        .kpi-trend {
            font-size: 8px;
            font-weight: 800;
            margin-top: 5px;
        }
        .trend-up { color: #10b981; }
        .trend-down { color: #ef4444; }

        /* Sections */
        .section-title {
            font-size: 11px;
            font-weight: 900;
            color: #1a2e3d;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            margin: 40px 0 20px;
            border-bottom: 2px solid #f1f5f9;
            padding-bottom: 10px;
        }

        /* Tables */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .data-table th {
            background-color: #1a2e3d;
            color: #fff;
            padding: 12px 15px;
            text-align: left;
            font-size: 9px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .data-table td {
            padding: 12px 15px;
            border-bottom: 1px solid #f1f5f9;
            font-size: 11px;
            color: #334155;
        }
        .data-table .val-bold { font-weight: 800; color: #1a2e3d; }
        .data-table .badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 4px;
            font-size: 8px;
            font-weight: 900;
            text-transform: uppercase;
        }
        .badge-success { background-color: #dcfce7; color: #166534; }
        .badge-danger { background-color: #fee2e2; color: #991b1b; }
        .badge-info { background-color: #e0f2fe; color: #0369a1; }

        /* Insight Box */
        .insight-box {
            background-color: #fff7ed;
            border-left: 5px solid #f97316;
            padding: 20px;
            margin-top: 30px;
            font-size: 11px;
        }

        /* Footer */
        .footer {
            position: absolute;
            bottom: 30px;
            left: 50px;
            right: 50px;
            text-align: center;
            font-size: 8.5px;
            color: #94a3b8;
            border-top: 1px solid #f1f5f9;
            padding-top: 15px;
        }

        .clearfix { clear: both; }
        .page-break { page-break-after: always; }
    </style>
</head>
<body>
    <div class="page">
        <!-- HEADER -->
        <div class="header">
            <table width="100%">
                <thead>
                    <tr>
                        <th width="60%" style="text-align: left; font-weight: normal;">
                            <h1 class="agency-name">ONTARIO GROUP S.A.</h1>
                            <div class="header-sub">Rapport Stratégique de Gestion Locative</div>
                        </th>
                        <th width="40%" align="right" style="font-weight: normal;">
                            <img src="{{ public_path('images/ontorio-logo.png') }}" style="max-height: 70px;">
                        </th>
                    </tr>
                </thead>
            </table>
        </div>

        <!-- REPORT HEADER -->
        <div class="report-header">
            <div class="report-label">Analyse Mensuelle</div>
            <h1 class="report-title">Tableau de Bord Exécutif</h1>
            <div class="report-period">{{ \Carbon\Carbon::parse($mois)->translatedFormat('F Y') }}</div>
        </div>

        <!-- KPI GRID -->
        <div class="kpi-container">
            <div class="kpi-tile">
                <div class="kpi-label">Revenus Bruts</div>
                <div class="kpi-value text-secondary">{{ number_format($data['kpis']['revenu_mensuel'], 0, ',', ' ') }} F</div>
                <div class="kpi-trend trend-up">↑ +4.2% vs M-1</div>
            </div>
            <div class="kpi-gap"></div>
            <div class="kpi-tile">
                <div class="kpi-label">Occupation</div>
                <div class="kpi-value">{{ $data['kpis']['taux_occupation'] }}%</div>
                <div class="kpi-trend trend-up">Stable</div>
            </div>
            <div class="kpi-gap"></div>
            <div class="kpi-tile">
                <div class="kpi-label">Taux de Collecte</div>
                <div class="kpi-value text-secondary">{{ $data['kpis']['taux_collecte'] }}%</div>
                <div class="kpi-trend {{ $data['kpis']['taux_collecte'] >= 90 ? 'trend-up' : 'trend-down' }}">
                    {{ $data['kpis']['taux_collecte'] >= 90 ? 'Performant' : 'À surveiller' }}
                </div>
            </div>
            <div class="kpi-gap"></div>
            <div class="kpi-tile">
                <div class="kpi-label">Impayés</div>
                <div class="kpi-value" style="color: #ef4444;">{{ number_format($data['kpis']['impayes'], 0, ',', ' ') }} F</div>
                <div class="kpi-trend trend-down">Dette cumulée</div>
            </div>
            <div class="clearfix"></div>
        </div>

        <!-- FINANCIAL SUMMARY -->
        <div class="section-title">Synthèse Financière de la Période</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Indicateur de Performance</th>
                    <th style="text-align: right;">Valeur (FCFA)</th>
                    <th style="text-align: center;">Statut Opérationnel</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Émissions des Loyers de la Période</td>
                    <td class="val-bold" align="right">{{ number_format($data['kpis']['loyers_emis'], 0, ',', ' ') }}</td>
                    <td align="center"><span class="badge badge-info text-secondary">Facturation</span></td>
                </tr>
                <tr>
                    <td>Encaissements Effectifs (Mois en cours)</td>
                    <td class="val-bold" align="right" style="color: #10b981;">{{ number_format($data['kpis']['loyers_payes'], 0, ',', ' ') }}</td>
                    <td align="center"><span class="badge badge-success">Recouvrement</span></td>
                </tr>
                <tr>
                    <td>Chiffre d'Affaire Estimé (Honoraires 10%)</td>
                    <td class="val-bold" align="right">{{ number_format($data['kpis']['commission_mensuelle'], 0, ',', ' ') }}</td>
                    <td align="center"><span class="badge badge-info">Revenu Agence</span></td>
                </tr>
                <tr>
                    <td>Variance Mensuelle (Reliquat à percevoir)</td>
                    <td class="val-bold" align="right" style="color: #ef4444;">{{ number_format($data['kpis']['total_impaye'], 0, ',', ' ') }}</td>
                    <td align="center">
                        @if($data['kpis']['total_impaye'] > 0)
                            <span class="badge badge-danger">Déficitaire</span>
                        @else
                            <span class="badge badge-success">Soldé</span>
                        @endif
                    </td>
                </tr>
            </tbody>
        </table>

        <!-- INSIGHT BOX -->
        <div class="insight-box">
            <strong class="text-secondary" style="font-weight: 900; text-transform: uppercase; font-size: 10px; display: block; margin-bottom: 5px;">Observation Stratégique</strong>
            Le taux de performance financière pour la période est de <strong>{{ $data['kpis']['taux_collecte'] }}%</strong>. 
            @if($data['kpis']['taux_collecte'] >= 90)
                L'efficacité des encaissements est optimale. Maintenir les processus actuels.
            @else
                Des mesures de relance active sont préconisées pour les dossiers en variance négative afin de stabiliser les flux de trésorerie.
            @endif
        </div>

        <div class="footer">
            Ontario Group S.A. | Rapport de Gestion Généré Numériquement | Page 1/2
        </div>
    </div>

    <div class="page-break"></div>

    <div class="page">
        <!-- HEADER (Simplified for secondary pages) -->
        <div class="header">
            <table width="100%">
                <thead>
                    <tr>
                        <th style="text-align: left; font-weight: normal;"><div class="agency-name">Détail Opérationnel</div></th>
                        <th align="right" style="font-weight: normal;"><div class="report-period">{{ \Carbon\Carbon::parse($mois)->translatedFormat('F Y') }}</div></th>
                    </tr>
                </thead>
            </table>
        </div>

        <div class="section-title">Portefeuille Immobilier & Occupation</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Bien & Localisation</th>
                    <th>Typologie</th>
                    <th style="text-align: center;">Statut Actuel</th>
                    <th style="text-align: right;">Loyer Nominal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data['biens_list']->take(20) as $bien)
                <tr>
                    <td>
                        <div class="val-bold">{{ $bien->nom }}</div>
                        <div style="font-size: 9px; color: #94a3b8;">{{ $bien->ville }}</div>
                    </td>
                    <td>{{ ucfirst($bien->type) }}</td>
                    <td align="center">
                        @if($bien->statut === 'libre' || $bien->statut === 'disponible')
                            <span class="badge badge-success">Disponible</span>
                        @elseif($bien->statut === 'occupé')
                            <span class="badge badge-info">Loué</span>
                        @else
                            <span class="badge badge-danger">{{ $bien->statut }}</span>
                        @endif
                    </td>
                    <td align="right" class="val-bold">{{ number_format($bien->loyer_mensuel, 0, ',', ' ') }} F</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="footer">
            Document Interne de Pilotage | Ontario Group S.A. | Page 2/2
        </div>
    </div>
</body>
</html>
