<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Bilan Financier - {{ $proprietaire->nom }}</title>
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
        }

        /* Branding Colors */
        .text-primary { color: #cb2d2d; }
        .text-secondary { color: #1a2e3d; }
        .bg-secondary { background-color: #1a2e3d; }

        /* Header */
        .header {
            border-bottom: 4px solid #cb2d2d;
            padding-bottom: 25px;
            margin-bottom: 40px;
        }
        .agency-name {
            font-size: 24px;
            font-weight: 900;
            color: #1a2e3d;
            letter-spacing: -1px;
            margin: 0;
        }
        .header-sub {
            font-size: 9.5px;
            color: #64748b;
            margin-top: 5px;
        }

        /* Title Area */
        .doc-header {
            text-align: center;
            margin-bottom: 40px;
        }
        .doc-type {
            font-size: 10px;
            font-weight: 900;
            color: #cb2d2d;
            text-transform: uppercase;
            letter-spacing: 3px;
            margin-bottom: 5px;
        }
        .doc-title {
            font-size: 22px;
            font-weight: 900;
            color: #1a2e3d;
            text-transform: uppercase;
            letter-spacing: -0.5px;
        }
        .doc-date {
            font-size: 9px;
            color: #94a3b8;
            font-weight: 700;
            margin-top: 5px;
        }

        /* Profile Card */
        .profile-section {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 35px;
        }
        .profile-label {
            font-size: 8px;
            font-weight: 900;
            color: #cb2d2d;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            margin-bottom: 8px;
        }
        .profile-name {
            font-size: 16px;
            font-weight: 900;
            color: #1a2e3d;
        }
        .profile-stat {
            font-size: 11px;
            color: #64748b;
            margin-top: 5px;
        }

        /* Section Headings */
        .section-header {
            font-size: 10px;
            font-weight: 900;
            color: #1a2e3d;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            margin-bottom: 15px;
            border-left: 5px solid #cb2d2d;
            padding-left: 15px;
        }

        /* Tables */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 35px;
        }
        .data-table th {
            background-color: #1a2e3d;
            color: #fff;
            text-align: left;
            padding: 12px 15px;
            font-size: 9px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .data-table td {
            padding: 12px 15px;
            border-bottom: 1px solid #f1f5f9;
            font-size: 11px;
            color: #334155;
        }
        .data-table .amount {
            text-align: right;
            font-weight: 800;
            color: #1a2e3d;
        }
        .data-table tr.total-row td {
            background-color: #f8fafc;
            font-weight: 900;
            color: #1a2e3d;
            border-top: 2px solid #e2e8f0;
        }

        /* Financial Synthesis Box */
        .synthesis-grid {
            width: 100%;
            margin-top: 20px;
        }
        .synthesis-box {
            background-color: #1a2e3d;
            color: #fff;
            border-radius: 16px;
            padding: 30px;
            box-shadow: 0 10px 25px rgba(26, 46, 61, 0.2);
        }
        .synth-item {
            margin-bottom: 15px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            padding-bottom: 10px;
        }
        .synth-label {
            font-size: 11px;
            font-weight: 700;
            color: #94a3b8;
            text-transform: uppercase;
        }
        .synth-val {
            font-size: 18px;
            font-weight: 800;
            float: right;
            margin-top: -5px;
        }
        .net-result {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 2px dashed rgba(255,255,255,0.2);
        }
        .net-label {
            font-size: 14px;
            font-weight: 900;
            color: #fff;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .net-val {
            font-size: 32px;
            font-weight: 900;
            color: #fff;
            float: right;
            margin-top: -15px;
        }

        /* Footer */
        .footer {
            position: absolute;
            bottom: 40px;
            left: 50px;
            right: 50px;
            text-align: center;
            font-size: 8.5px;
            color: #94a3b8;
            border-top: 1px solid #f1f5f9;
            padding-top: 20px;
        }

        .clearfix { clear: both; }
    </style>
</head>
<body>
    <div class="page">
        <!-- HEADER -->
        <div class="header">
            <table style="width: 100%">
                <thead>
                    <tr>
                        <th style="width: 60%; text-align: left; font-weight: normal;">
                            <h1 class="agency-name">ONTARIO GROUP S.A.</h1>
                            <div class="header-sub">
                                Expertise Immobilière & Gestion de Patrimoine<br>
                                Dakar Plateau, Sénégal | +221 33 822 32 67
                            </div>
                        </th>
                        <th style="width: 40%; text-align: right; font-weight: normal;">
                            <img src="{{ public_path('images/ontorio-logo.png') }}" style="max-height: 70px;" alt="Logo Ontario Group">
                        </th>
                    </tr>
                </thead>
            </table>
        </div>

        <!-- DOC HEADER -->
        <div class="doc-header">
            <div class="doc-type">Rapport Comptable</div>
            <h2 class="doc-title">Relevé de Compte Propriétaire</h2>
            <div class="doc-date">GÉNÉRÉ LE {{ date('d/m/Y') }} | PÉRIODE CONSOLIDÉE</div>
        </div>

        <!-- PROFILE -->
        <div class="profile-section">
            <div class="profile-label">Propriétaire du Patrimoine</div>
            <div class="profile-name">{{ strtoupper($proprietaire->nom) }} {{ strtoupper($proprietaire->prenom) }}</div>
            <div class="profile-stat">Portefeuille de Gestion : <strong>{{ $biens->count() }} Unités Immobilières</strong></div>
        </div>

        <!-- REVENUES Table -->
        <div class="section-header">Recettes Locatives Encaissées</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 40%">Désignation du Bien</th>
                    <th style="width: 35%">Locataire</th>
                    <th style="width: 25%; text-align: right;">Montant Perçu</th>
                </tr>
            </thead>
            <tbody>
                @php $totalRecettes = 0; @endphp
                @foreach($biens as $bien)
                    @php
                        $encaisséBien = $bien->contrats->flatMap->paiements->sum('montant');
                        $totalRecettes += $encaisséBien;
                    @endphp
                    <tr>
                        <td style="font-weight: 800; color: #1a2e3d;">{{ $bien->nom }}</td>
                        <td>{{ $bien->contrats->where('statut', 'actif')->first()->locataire->nom ?? '---' }}</td>
                        <td class="amount text-primary">{{ format_money($encaisséBien) }}</td>
                    </tr>
                @endforeach
                <tr class="total-row">
                    <td colspan="2">TOTAL DES REVENUS LOCATIFS</td>
                    <td class="amount text-primary" style="font-size: 14px;">{{ format_money($totalRecettes) }}</td>
                </tr>
            </tbody>
        </table>

        <!-- EXPENSES Table -->
        <div class="section-header">Charges & Maintenance Déductibles</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 15%">Date</th>
                    <th style="width: 25%">Bien</th>
                    <th style="width: 40%">Libellé / Description</th>
                    <th style="width: 20%; text-align: right;">Montant</th>
                </tr>
            </thead>
            <tbody>
                @php $totalDepenses = 0; $hasDepenses = false; @endphp
                @foreach($biens as $bien)
                    @foreach($bien->depenses as $dep)
                        @php $totalDepenses += $dep->montant; $hasDepenses = true; @endphp
                        <tr>
                            <td>{{ $dep->date_depense->format('d/m/y') }}</td>
                            <td>{{ $bien->nom }}</td>
                            <td>{{ $dep->titre }} <span style="color: #94a3b8; font-size: 9px;">({{ $dep->categorie }})</span></td>
                            <td class="amount" style="color: #64748b;">{{ format_money($dep->montant) }}</td>
                        </tr>
                    @endforeach
                @endforeach
                @if(!$hasDepenses)
                    <tr>
                        <td colspan="4" style="text-align: center; padding: 25px; color: #94a3b8; font-style: italic;">Aucune charge enregistrée sur la période.</td>
                    </tr>
                @endif
                <tr class="total-row">
                    <td colspan="3">TOTAL DES DÉPENSES OPÉRATIONNELLES</td>
                    <td class="amount" style="font-size: 14px; color: #64748b;">{{ format_money($totalDepenses) }}</td>
                </tr>
            </tbody>
        </table>

        <!-- SYNTHESIS -->
        <div class="synthesis-box">
            <div class="synth-item">
                <span class="synth-label">Total Recettes Brutes</span>
                <span class="synth-val">{{ format_money($totalRecettes) }}</span>
                <div class="clearfix"></div>
            </div>
            <div class="synth-item">
                <span class="synth-label">Total Charges & Dépenses</span>
                <span class="synth-val">- {{ format_money($totalDepenses) }}</span>
                <div class="clearfix"></div>
            </div>
            <div class="net-result">
                <span class="net-label">Net de Gestion à Reverser</span>
                <span class="net-val">{{ format_money($totalRecettes - $totalDepenses) }}</span>
                <div class="clearfix"></div>
            </div>
        </div>

        <div class="footer">
            Ontario Group S.A. | Relevé Certifié par le département comptable<br>
            Document confidentiel destiné exclusivement au propriétaire mentionné ci-dessus.
        </div>
    </div>
</body>
</html>
