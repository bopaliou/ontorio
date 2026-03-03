<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Rapport Hebdomadaire / Mensuel - Ontario Group</title>
    <style>
        @page {
            margin: 0;
            size: A4;
        }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 9px;
            color: #334155;
            line-height: 1.4;
            background-color: #fff;
            margin: 0;
            padding: 0;
        }

        .page {
            position: relative;
            padding: 30px 40px;
            height: 100%;
            box-sizing: border-box;
        }

        /* Branding Colors */
        .text-brand-red { color: #cb2d2d; }
        .text-brand-blue { color: #0f172a; }

        /* Document Title Header */
        .doc-title {
            text-align: center;
            font-size: 14px;
            font-weight: 900;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: 25px;
            border-bottom: 2px solid #e2e8f0;
            padding-bottom: 10px;
        }
        .doc-subtitle {
            font-size: 9px;
            color: #64748b;
            letter-spacing: 1px;
            margin-top: 4px;
            display: block;
        }

        /* KPI Tiles Grid */
        .kpi-container {
            width: 100%;
            margin-bottom: 25px;
        }
        .kpi-tile {
            width: 23.5%;
            float: left;
            background-color: #f8fafc;
            border: 1px solid #f1f5f9;
            border-top: 3px solid #cb2d2d;
            padding: 12px 0;
            border-radius: 6px;
            text-align: center;
        }
        .kpi-gap { width: 2%; float: left; height: 10px; }
        .kpi-label {
            font-size: 7px;
            font-weight: 800;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
        }
        .kpi-value {
            font-size: 13px;
            font-weight: 900;
            color: #0f172a;
        }
        .kpi-value-red { color: #cb2d2d; }
        
        /* Section Headers */
        .section-header {
            font-size: 10px;
            font-weight: 900;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 12px;
            border-left: 3px solid #cb2d2d;
            padding-left: 10px;
            margin-top: 25px;
        }

        /* Tables */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            page-break-inside: avoid;
        }
        .data-table th {
            background-color: #f8fafc;
            color: #475569;
            padding: 8px 12px;
            text-align: left;
            font-size: 7.5px;
            font-weight: 900;
            text-transform: uppercase;
            border-bottom: 2px solid #e2e8f0;
            letter-spacing: 1px;
        }
        .data-table td {
            padding: 8px 12px;
            border-bottom: 1px solid #f1f5f9;
            font-size: 8.5px;
            color: #334155;
            vertical-align: middle;
        }
        .val-bold { font-weight: 800; color: #0f172a; }
        
        .badge {
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 7px;
            font-weight: 900;
            text-transform: uppercase;
            display: inline-block;
        }
        .badge-success { background-color: #dcfce7; color: #166534; }
        .badge-danger { background-color: #fee2e2; color: #991b1b; }
        .badge-info { background-color: #e0f2fe; color: #0369a1; }
        .badge-neutral { background-color: #f1f5f9; color: #475569; }

        /* Insight Box */
        .insight-box {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-left: 3px solid #1a2e3d;
            padding: 12px 15px;
            border-radius: 6px;
            font-size: 9px;
            color: #475569;
            page-break-inside: avoid;
        }
        .insight-title {
            text-transform: uppercase;
            font-size: 7px;
            font-weight: 900;
            color: #94a3b8;
            margin-bottom: 4px;
            display: block;
            letter-spacing: 1px;
        }

        .clearfix { clear: both; }
        .page-break { page-break-after: always; }
    </style>
</head>
<body>
    <div class="page">
        <!-- HEADER -->
        @include('pdf.partials.header', [
            'title' => 'RAPPORT DE GESTION',
            'ref' => 'REP-' . date('Ym'),
            'date' => \Carbon\Carbon::parse($mois)->translatedFormat('F Y')
        ])

        <div class="doc-title">
            Bilan Mensuel Global
            <span class="doc-subtitle">Période d'analyse : {{ \Carbon\Carbon::parse($mois)->translatedFormat('F Y') }}</span>
        </div>

        <!-- KPI GRID -->
        <div class="kpi-container">
            <div class="kpi-tile">
                <div class="kpi-label">Revenus Bruts Facturés</div>
                <div class="kpi-value">{{ format_money($data['kpis']['revenu_mensuel'], '') }}</div>
            </div>
            <div class="kpi-gap"></div>
            <div class="kpi-tile">
                <div class="kpi-label">Taux d'Occupation</div>
                <div class="kpi-value">{{ $data['kpis']['taux_occupation'] }}%</div>
            </div>
            <div class="kpi-gap"></div>
            <div class="kpi-tile">
                <div class="kpi-label">Taux de Recouvrement</div>
                <div class="kpi-value">{{ $data['kpis']['taux_collecte'] }}%</div>
            </div>
            <div class="kpi-gap"></div>
            <div class="kpi-tile" style="border-top-color: #1a2e3d;">
                <div class="kpi-label">Arriérés / Déficit</div>
                <div class="kpi-value kpi-value-red">{{ format_money($data['kpis']['impayes'], '') }}</div>
            </div>
            <div class="clearfix"></div>
        </div>

        <div class="section-header">Synthèse Opérationnelle & Financière</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 45%;">Indicateur de Performance</th>
                    <th style="width: 25%; text-align: right;">Valeur Quantitative</th>
                    <th style="width: 30%; text-align: center;">État d'Alerte</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Émissions des Loyers (Facturation)</td>
                    <td class="val-bold" style="text-align: right;">{{ format_money($data['kpis']['loyers_emis'], '') }}</td>
                    <td style="text-align: center;"><span class="badge badge-info">ÉTABLI</span></td>
                </tr>
                <tr>
                    <td>Encaissements Effectifs (Recouvrements)</td>
                    <td class="val-bold" style="text-align: right; color: #10b981;">{{ format_money($data['kpis']['loyers_payes'], '') }}</td>
                    <td style="text-align: center;"><span class="badge badge-success">SÉCURISÉ</span></td>
                </tr>
                <tr>
                    <td>Variance Négative (Reliquats & Impayés)</td>
                    <td class="val-bold" style="text-align: right; color: #cb2d2d;">{{ format_money($data['kpis']['total_impaye'], '') }}</td>
                    <td style="text-align: center;">
                        @if($data['kpis']['total_impaye'] > 0)
                            <span class="badge badge-danger">DÉFICITAIRE</span>
                        @else
                            <span class="badge badge-success">SOLDÉ</span>
                        @endif
                    </td>
                </tr>
            </tbody>
        </table>

        <!-- INSIGHT BOX -->
        <div class="insight-box">
            <span class="insight-title">Note de Synthèse de Direction</span>
            L'indice de performance pour la période s'établit à <strong style="color: #0f172a;">{{ $data['kpis']['taux_collecte'] }}%</strong> d'efficacité de recouvrement global.
            @if($data['kpis']['taux_collecte'] < 90)
                <br>Une accélération des procédures de relances contentieuses est recommandée pour assainir les portefeuilles déficitaires.
            @else
                <br>La captation des flux de trésorerie est jugée optimale. Aucune action coercitive lourde n'est requise.
            @endif
        </div>
        
        <div class="section-header" style="margin-top: 35px;">Portefeuille Actif et Occupation</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 45%;">Désignation & Situation Géographique</th>
                    <th style="width: 20%;">Typologie</th>
                    <th style="width: 15%; text-align: center;">Statut</th>
                    <th style="width: 20%; text-align: right;">Loyer Nominal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data['biens_list']->take(25) as $bien)
                <tr>
                    <td>
                        <div class="val-bold">{{ $bien->nom }}</div>
                        <div style="font-size: 7.5px; color: #94a3b8;">{{ $bien->ville ?? $bien->adresse ?? 'Sénégal' }}</div>
                    </td>
                    <td>{{ ucfirst($bien->type) }}</td>
                    <td style="text-align: center;">
                        @if($bien->statut === 'libre' || $bien->statut === 'disponible')
                            <span class="badge badge-success">DISPONIBLE</span>
                        @elseif($bien->statut === 'en_travaux')
                            <span class="badge badge-danger">TRAVAUX</span>
                        @else
                            <span class="badge badge-neutral">OCCUPÉ</span>
                        @endif
                    </td>
                    <td style="text-align: right;" class="val-bold">{{ format_money($bien->loyer_mensuel, '') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        @include('pdf.partials.footer')
    </div>
</body>
</html>
