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
            font-size: 10px;
            color: #1e293b;
            line-height: 1.5;
            background-color: #fff;
            margin: 0;
            padding: 0;
        }

        .page {
            position: relative;
            padding: 30px 40px;
            box-sizing: border-box;
            height: 100%;
        }

        /* Branding Colors */
        .text-primary { color: #cb2d2d; }
        .text-secondary { color: #1a2e3d; }

        /* KPI Tiles */
        .kpi-container {
            width: 100%;
            margin-bottom: 25px;
            overflow: hidden;
        }
        .kpi-tile {
            width: 23%;
            float: left;
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            padding: 12px;
            border-radius: 8px;
            text-align: center;
        }
        .kpi-gap { width: 2.6%; float: left; height: 10px; }
        .kpi-label {
            font-size: 7.5px;
            font-weight: 900;
            color: #94a3b8;
            text-transform: uppercase;
            margin-bottom: 5px;
        }
        .kpi-value {
            font-size: 14px;
            font-weight: 900;
            color: #1a2e3d;
        }

        /* Sections */
        .section-title {
            font-size: 10px;
            font-weight: 900;
            color: #1a2e3d;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin: 30px 0 15px;
            border-bottom: 2px solid #cb2d2d;
            padding-bottom: 5px;
        }

        /* Tables */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            page-break-inside: avoid;
        }
        .data-table th {
            background-color: #1a2e3d;
            color: #fff;
            padding: 10px 12px;
            text-align: left;
            font-size: 8px;
            font-weight: 900;
            text-transform: uppercase;
        }
        .data-table td {
            padding: 10px 12px;
            border-bottom: 1px solid #f1f5f9;
            font-size: 9.5px;
        }
        .data-table .val-bold { font-weight: 800; }
        .data-table .badge {
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 7.5px;
            font-weight: 900;
            text-transform: uppercase;
        }
        .badge-success { background-color: #dcfce7; color: #166534; }
        .badge-danger { background-color: #fee2e2; color: #991b1b; }
        .badge-info { background-color: #e0f2fe; color: #0369a1; }

        /* Insight Box */
        .insight-box {
            background-color: #fefce8;
            border-left: 4px solid #eab308;
            padding: 15px;
            margin-top: 20px;
            font-size: 9.5px;
            page-break-inside: avoid;
        }

        .clearfix { clear: both; }
        .page-break { page-break-after: always; }
    </style>
</head>
<body>
    <div class="page">
        <!-- HEADER -->
        @include('pdf.partials.header', [
            'title' => 'Rapport de Gestion Mensuel',
            'ref' => 'REP-' . date('Ym'),
            'date' => \Carbon\Carbon::parse($mois)->translatedFormat('F Y')
        ])

        <!-- KPI GRID -->
        <div class="kpi-container">
            <div class="kpi-tile">
                <div class="kpi-label">Revenus Bruts</div>
                <div class="kpi-value">{{ format_money($data['kpis']['revenu_mensuel']) }}</div>
            </div>
            <div class="kpi-gap"></div>
            <div class="kpi-tile">
                <div class="kpi-label">Occupation</div>
                <div class="kpi-value">{{ $data['kpis']['taux_occupation'] }}%</div>
            </div>
            <div class="kpi-gap"></div>
            <div class="kpi-tile">
                <div class="kpi-label">Collecte</div>
                <div class="kpi-value">{{ $data['kpis']['taux_collecte'] }}%</div>
            </div>
            <div class="kpi-gap"></div>
            <div class="kpi-tile">
                <div class="kpi-label">Impayés</div>
                <div class="kpi-value" style="color: #ef4444;">{{ format_money($data['kpis']['impayes']) }}</div>
            </div>
            <div class="clearfix"></div>
        </div>

        <div class="section-title">Analyse Financière</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Indicateur</th>
                    <th style="text-align: right;">Valeur (FCFA)</th>
                    <th style="text-align: center;">Statut</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Émissions des Loyers</td>
                    <td class="val-bold" style="text-align: right;">{{ number_format($data['kpis']['loyers_emis'], 0, ',', ' ') }}</td>
                    <td style="text-align: center;"><span class="badge badge-info">Facturation</span></td>
                </tr>
                <tr>
                    <td>Encaissements Effectifs</td>
                    <td class="val-bold" style="text-align: right; color: #10b981;">{{ number_format($data['kpis']['loyers_payes'], 0, ',', ' ') }}</td>
                    <td style="text-align: center;"><span class="badge badge-success">Recouvrement</span></td>
                </tr>
                <tr>
                    <td>Variance (Reliquat)</td>
                    <td class="val-bold" style="text-align: right; color: #ef4444;">{{ number_format($data['kpis']['total_impaye'], 0, ',', ' ') }}</td>
                    <td style="text-align: center;">
                        @if($data['kpis']['total_impaye'] > 0)
                            <span class="badge badge-danger">Déficitaire</span>
                        @else
                            <span class="badge badge-success">Soldé</span>
                        @endif
                    </td>
                </tr>
            </tbody>
        </table>

        <div class="insight-box">
            <strong class="text-secondary" style="text-transform: uppercase; font-size: 9px; display: block; margin-bottom: 4px;">Recommandation de Gestion</strong>
            Le taux de performance est de <strong>{{ $data['kpis']['taux_collecte'] }}%</strong>.
            @if($data['kpis']['taux_collecte'] < 90)
                Une accélération des relances est nécessaire pour les dossiers en variance négative.
            @else
                La gestion des flux est optimale pour cette période.
            @endif
        </div>

        @include('pdf.partials.footer')
    </div>

    <div class="page-break"></div>

    <div class="page">
        <div class="section-title" style="margin-top: 0;">Portefeuille Immobilier & Occupation</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Bien & Localisation</th>
                    <th>Typologie</th>
                    <th style="text-align: center;">Statut</th>
                    <th style="text-align: right;">Loyer Nominal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data['biens_list']->take(25) as $bien)
                <tr>
                    <td>
                        <div class="val-bold">{{ $bien->nom }}</div>
                        <div style="font-size: 8px; color: #94a3b8;">{{ $bien->ville }}</div>
                    </td>
                    <td>{{ ucfirst($bien->type) }}</td>
                    <td style="text-align: center;">
                        @if($bien->statut === 'libre' || $bien->statut === 'disponible')
                            <span class="badge badge-success">Libre</span>
                        @else
                            <span class="badge badge-info">Loué</span>
                        @endif
                    </td>
                    <td style="text-align: right;" class="val-bold">{{ format_money($bien->loyer_mensuel) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        @include('pdf.partials.footer')
    </div>
</body>
</html>
