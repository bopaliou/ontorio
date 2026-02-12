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
        }

        /* Branding Colors */
        .text-primary { color: #cb2d2d; }
        .text-secondary { color: #1a2e3d; }

        /* Profile Card */
        .profile-section {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 25px;
        }
        .profile-label {
            font-size: 8px;
            font-weight: 900;
            color: #cb2d2d;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 5px;
        }
        .profile-name {
            font-size: 14px;
            font-weight: 900;
            color: #1a2e3d;
        }

        /* Section Headings */
        .section-header {
            font-size: 9px;
            font-weight: 900;
            color: #1a2e3d;
            text-transform: uppercase;
            margin-bottom: 12px;
            border-left: 4px solid #cb2d2d;
            padding-left: 12px;
        }

        /* Tables */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
            page-break-inside: avoid;
        }
        .data-table th {
            background-color: #1a2e3d;
            color: #fff;
            text-align: left;
            padding: 10px 12px;
            font-size: 8px;
            font-weight: 900;
            text-transform: uppercase;
        }
        .data-table td {
            padding: 10px 12px;
            border-bottom: 1px solid #f1f5f9;
            font-size: 9.5px;
        }
        .data-table .amount {
            text-align: right;
            font-weight: 800;
        }
        .data-table tr.total-row td {
            background-color: #f8fafc;
            font-weight: 900;
            border-top: 2px solid #e2e8f0;
        }

        /* Financial Synthesis Box */
        .synthesis-box {
            background-color: #1a2e3d;
            color: #fff;
            border-radius: 12px;
            padding: 20px;
            margin-top: 10px;
            page-break-inside: avoid;
        }
        .synth-item {
            margin-bottom: 10px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            padding-bottom: 8px;
        }
        .synth-label {
            font-size: 10px;
            font-weight: 700;
            color: #94a3b8;
            text-transform: uppercase;
        }
        .synth-val {
            font-size: 14px;
            font-weight: 800;
            float: right;
            margin-top: -3px;
        }
        .net-result {
            margin-top: 15px;
            padding-top: 15px;
            border-top: 2px dashed rgba(255,255,255,0.2);
        }
        .net-label {
            font-size: 12px;
            font-weight: 900;
            text-transform: uppercase;
        }
        .net-val {
            font-size: 24px;
            font-weight: 900;
            float: right;
            margin-top: -10px;
        }

        .clearfix { clear: both; }
    </style>
</head>
<body>
    <div class="page">
        <!-- HEADER -->
        @include('pdf.partials.header', [
            'title' => 'Bilan Financier Propriétaire',
            'ref' => 'BIL-' . date('Ym') . '-' . $proprietaire->id,
            'date' => date('d/m/Y')
        ])

        <!-- PROFILE -->
        <div class="profile-section">
            <div class="profile-label">Identité du Propriétaire</div>
            <div class="profile-name">{{ strtoupper($proprietaire->nom) }} {{ strtoupper($proprietaire->prenom ?? '') }}</div>
            <div style="font-size: 9px; color: #64748b; margin-top: 4px;">Nombre de biens en gestion : <strong>{{ $biens->count() }}</strong></div>
        </div>

        <!-- REVENUES Table -->
        <div class="section-header">Recettes Locatives Encaissées</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 45%">Bien</th>
                    <th style="width: 30%">Locataire</th>
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
                        <td style="font-weight: 700;">{{ $bien->nom }}</td>
                        <td>{{ $bien->contrats->where('statut', 'actif')->first()->locataire->nom ?? '---' }}</td>
                        <td class="amount text-primary">{{ format_money($encaisséBien) }}</td>
                    </tr>
                @endforeach
                <tr class="total-row">
                    <td colspan="2">TOTAL DES REVENUS LOCATIFS</td>
                    <td class="amount text-primary">{{ format_money($totalRecettes) }}</td>
                </tr>
            </tbody>
        </table>

        <!-- EXPENSES Table -->
        <div class="section-header">Charges & Maintenance Déductibles</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 15%">Date</th>
                    <th style="width: 60%">Libellé / Catégorie</th>
                    <th style="width: 25%; text-align: right;">Montant</th>
                </tr>
            </thead>
            <tbody>
                @php $totalDepenses = 0; $hasDepenses = false; @endphp
                @foreach($biens as $bien)
                    @foreach($bien->depenses as $dep)
                        @php $totalDepenses += $dep->montant; $hasDepenses = true; @endphp
                        <tr>
                            <td>{{ $dep->date_depense->format('d/m/y') }}</td>
                            <td>{{ $dep->titre }} <span style="color: #94a3b8; font-size: 8px;">({{ $dep->categorie }})</span></td>
                            <td class="amount" style="color: #64748b;">{{ format_money($dep->montant) }}</td>
                        </tr>
                    @endforeach
                @endforeach
                @if(!$hasDepenses)
                    <tr>
                        <td colspan="3" style="text-align: center; padding: 15px; color: #94a3b8; font-style: italic;">Aucune charge enregistrée sur la période.</td>
                    </tr>
                @endif
                <tr class="total-row">
                    <td colspan="2">TOTAL DES DÉPENSES OPÉRATIONNELLES</td>
                    <td class="amount" style="color: #64748b;">{{ format_money($totalDepenses) }}</td>
                </tr>
            </tbody>
        </table>

        <!-- SYNTHESIS -->
        <div class="synthesis-box">
            <div class="synth-item">
                <span class="synth-label">Recettes Brutes</span>
                <span class="synth-val">{{ format_money($totalRecettes) }}</span>
                <div class="clearfix"></div>
            </div>
            <div class="synth-item">
                <span class="synth-label">Charges & Dépenses</span>
                <span class="synth-val">- {{ format_money($totalDepenses) }}</span>
                <div class="clearfix"></div>
            </div>
            <div class="net-result">
                <span class="net-label">Net de Gestion à Reverser</span>
                <span class="net-val">{{ format_money($totalRecettes - $totalDepenses) }}</span>
                <div class="clearfix"></div>
            </div>
        </div>

        @include('pdf.partials.footer')
    </div>
</body>
</html>
