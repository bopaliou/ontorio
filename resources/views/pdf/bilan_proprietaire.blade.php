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

        /* Profile Block */
        .profile-section {
            background-color: #f8fafc;
            border: 1px solid #f1f5f9;
            border-left: 3px solid #cb2d2d;
            border-radius: 6px;
            padding: 12px 15px;
            margin-bottom: 25px;
        }
        .profile-label {
            font-size: 7px;
            font-weight: 800;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 4px;
        }
        .profile-name {
            font-size: 14px;
            font-weight: 900;
            color: #0f172a;
            text-transform: uppercase;
        }
        .profile-meta {
            font-size: 8.5px;
            color: #64748b;
            margin-top: 4px;
        }

        /* Tables */
        .section-header {
            font-size: 10px;
            font-weight: 900;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 10px;
            border-bottom: 2px solid #e2e8f0;
            padding-bottom: 5px;
            margin-top: 25px;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            page-break-inside: avoid;
        }
        .data-table th {
            background-color: #f8fafc;
            color: #475569;
            text-align: left;
            padding: 8px 12px;
            font-size: 7.5px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 1px;
            border-bottom: 2px solid #e2e8f0;
        }
        .data-table td {
            padding: 8px 12px;
            border-bottom: 1px solid #f1f5f9;
            font-size: 9px;
            vertical-align: middle;
        }
        .data-table .amount {
            text-align: right;
            font-weight: 800;
            color: #0f172a;
        }
        
        .row-sub { color: #64748b; font-size: 8px; }

        /* Empty State */
        .empty-row td {
            text-align: center;
            padding: 15px;
            color: #94a3b8;
            font-style: italic;
            font-size: 8.5px;
        }

        /* Synthesis / Totals */
        .totals-container {
            width: 100%;
            margin-top: 20px;
            page-break-inside: avoid;
        }
        .totals-table {
            width: 280px;
            float: right;
            border-collapse: collapse;
        }
        .totals-table td {
            padding: 6px 0;
            text-align: right;
        }
        .total-label {
            font-size: 9px;
            color: #64748b;
            font-weight: 700;
            padding-right: 15px;
            text-transform: uppercase;
        }
        .total-value {
            font-size: 11px;
            font-weight: 800;
            color: #334155;
        }
        
        .grand-total-row td {
            padding-top: 12px;
            padding-bottom: 12px;
            border-top: 2px solid #cb2d2d; 
            background-color: #fffafa;
        }
        .grand-total-label {
            font-size: 10px;
            font-weight: 900;
            color: #cb2d2d;
            text-transform: uppercase;
            letter-spacing: 1px;
            padding-right: 15px;
            padding-left: 10px;
        }
        .grand-total-value {
            font-size: 14px;
            font-weight: 900;
            color: #cb2d2d;
            padding-right: 10px;
        }

        .clearfix { clear: both; }
    </style>
</head>
<body>
    <div class="page">
        <!-- HEADER -->
        @include('pdf.partials.header', [
            'title' => 'Bilan Financier Propriétaire',
            'ref' => 'BIL-' . date('Ym') . '-' . str_pad($proprietaire->id, 3, '0', STR_PAD_LEFT),
            'date' => date('d/m/Y')
        ])

        <!-- PROFILE -->
        <div class="profile-section">
            <div class="profile-label">Identité du Propriétaire / Mandant</div>
            <div class="profile-name">{{ $proprietaire->nom }} {{ $proprietaire->prenom ?? '' }}</div>
            <div class="profile-meta">
                Contact : {{ $proprietaire->telephone ?? '--' }} &nbsp;|&nbsp; 
                Parc immobilier : <strong>{{ $biens->count() }}</strong> bien(s) en gestion
            </div>
        </div>

        <!-- REVENUES Table -->
        <div class="section-header">Recettes Locatives Encaissées</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 45%">Désignation du Bien</th>
                    <th style="width: 30%">Locataire Principal</th>
                    <th style="width: 25%; text-align: right;">Montant Perçu</th>
                </tr>
            </thead>
            <tbody>
                @php $totalRecettes = 0; $baseCommission = 0; $hasRecettes = false; @endphp
                @foreach($biens as $bien)
                    @php
                        // Calcul des encaissements sur ce bien
                        $encaisséBien = $bien->contrats->flatMap->paiements->sum('montant');
                        // Base commissionnaire = min(loyer facturé, paiements reçus) pour exclure les pénalités
                        $loyersMontant = $bien->contrats->flatMap->loyers->sum('montant');
                        $baseCommission += min($loyersMontant, $encaisséBien);
                        if($encaisséBien > 0) $hasRecettes = true;
                        $totalRecettes += $encaisséBien;
                    @endphp
                    @if($encaisséBien > 0)
                    <tr>
                        <td>
                            <strong style="color: #0f172a;">{{ $bien->nom }}</strong>
                            <div class="row-sub">{{ ucfirst($bien->type) }}</div>
                        </td>
                        <td>{{ $bien->contrats->where('statut', 'actif')->first()->locataire->nom ?? '---' }}</td>
                        <td class="amount">{{ format_money($encaisséBien, '') }} <span style="font-size: 8px; font-weight: 400; color: #94a3b8;">FCFA</span></td>
                    </tr>
                    @endif
                @endforeach
                
                @if(!$hasRecettes)
                    <tr class="empty-row">
                        <td colspan="3">Aucun encaissement sur la période.</td>
                    </tr>
                @endif
            </tbody>
        </table>

        <!-- EXPENSES Table -->
        <div class="section-header">Charges & Frais Déductibles</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 15%">Date</th>
                    <th style="width: 60%">Libellé / Catégorie Intervention</th>
                    <th style="width: 25%; text-align: right;">Montant</th>
                </tr>
            </thead>
            <tbody>
                @php $totalDepenses = 0; $hasDepenses = false; @endphp
                @foreach($biens as $bien)
                    @foreach($bien->depenses as $dep)
                        @php $totalDepenses += $dep->montant; $hasDepenses = true; @endphp
                        <tr>
                            <td style="color: #64748b;">{{ $dep->date_depense->format('d/m/Y') }}</td>
                            <td>
                                <strong style="color: #0f172a;">{{ $dep->titre }}</strong>
                                <div class="row-sub">{{ $bien->nom }} — {{ ucfirst($dep->categorie) }}</div>
                            </td>
                            <td class="amount">{{ format_money($dep->montant, '') }} <span style="font-size: 8px; font-weight: 400; color: #94a3b8;">FCFA</span></td>
                        </tr>
                    @endforeach
                @endforeach
                
                @if(!$hasDepenses)
                    <tr class="empty-row">
                        <td colspan="3">Aucune charge ou dépense enregistrée sur la période.</td>
                    </tr>
                @endif
            </tbody>
        </table>

        <!-- SYNTHESIS / TOTALS -->
        <div class="totals-container clearfix">
            <table class="totals-table">
                <tr>
                    <td class="total-label">Total des Recettes</td>
                    <td class="total-value">{{ format_money($totalRecettes, '') }} <span style="font-size: 9px; color: #94a3b8;">FCFA</span></td>
                </tr>
                <tr>
                    <td class="total-label">Total des Charges</td>
                    <td class="total-value" style="color: #cb2d2d;">- {{ format_money($totalDepenses, '') }} <span style="font-size: 9px; color: #94a3b8;">FCFA</span></td>
                </tr>
                
                <!-- Commission Agence calculation (10% par défaut) -->
                @php
                    $commissionRate = config('real_estate.commission.rate', 0.10);
                    $commission = round($baseCommission * $commissionRate, 2);
                    $netAReverser = $totalRecettes - $totalDepenses - $commission;
                @endphp
                
                <tr>
                    <td class="total-label">Frais de Gestion ({{ $commissionRate * 100 }}%)</td>
                    <td class="total-value" style="color: #cb2d2d;">- {{ format_money($commission, '') }} <span style="font-size: 9px; color: #94a3b8;">FCFA</span></td>
                </tr>
                
                <tr class="grand-total-row">
                    <td class="grand-total-label">NET À REVERSER</td>
                    <td class="grand-total-value">
                        {{ format_money($netAReverser, '') }} 
                        <span style="font-size: 10px;">FCFA</span>
                    </td>
                </tr>
            </table>
        </div>

        @include('pdf.partials.footer')
    </div>
</body>
</html>
