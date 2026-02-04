<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Bilan Financier - {{ $proprietaire->nom }}</title>
    <style>
        body { font-family: 'Helvetica', sans-serif; color: #333; line-height: 1.5; padding: 20px; }
        .header { border-bottom: 2px solid #cb2d2d; padding-bottom: 20px; margin-bottom: 30px; }
        .logo { float: left; width: 150px; }
        .company-info { float: right; text-align: right; font-size: 11px; color: #64748b; }
        .title { clear: both; text-align: center; color: #1A365D; margin-top: 40px; }
        .section-title { background: #f8fafc; padding: 10px; border-left: 4px solid #cb2d2d; font-weight: bold; margin-top: 30px; font-size: 13px; text-transform: uppercase; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; font-size: 11px; }
        th { background: #1A365D; color: white; padding: 12px 10px; text-align: left; text-transform: uppercase; letter-spacing: 1px; }
        td { border-bottom: 1px solid #eee; padding: 10px; }
        .text-right { text-align: right; }
        .total-row { background: #f8fafc; font-weight: bold; }
        .footer { margin-top: 50px; font-size: 9px; text-align: center; color: #94a3b8; border-top: 1px solid #eee; padding-top: 20px; }
        .profit { color: #16a34a; }
        .loss { color: #dc2626; }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo">
            <strong style="font-size: 24px; color: #cb2d2d;">ONTARIO</strong><br>
            <small style="color: #64748b; letter-spacing: 2px;">GROUP</small>
        </div>
        <div class="company-info">
            <strong>Ontario Group SARL</strong><br>
            Avenue Cheikh Anta Diop, Dakar<br>
            Tél: +221 33 822 00 00<br>
            Email: contact@ontariogroup.net
        </div>
    </div>

    <div class="title">
        <h1 style="margin-bottom: 5px;">RELEVÉ DE COMPTE PROPRIÉTAIRE</h1>
        <p style="font-size: 12px; color: #64748b;">Généré le {{ date('d/m/Y') }} pour la période consolidée</p>
    </div>

    <div class="section-title">Identité du Propriétaire</div>
    <div style="margin-top: 10px; font-size: 13px; color: #1e293b;">
        <strong>Nom :</strong> {{ $proprietaire->nom }} {{ $proprietaire->prenom }}<br>
        <strong>Patrimoine :</strong> {{ $biens->count() }} Biens sous gestion active
    </div>

    <div class="section-title">Recettes Locatives (Encaissées)</div>
    <table>
        <thead>
            <tr>
                <th>Référence Bien</th>
                <th>Dernier Locataire</th>
                <th class="text-right">Total Encaissé</th>
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
                    <td style="font-weight: bold;">{{ $bien->nom }}</td>
                    <td>{{ $bien->contrats->where('statut', 'actif')->first()->locataire->nom ?? 'N/A' }}</td>
                    <td class="text-right" style="font-weight: bold; color: #16a34a;">{{ number_format($encaisséBien, 0, ',', ' ') }} F</td>
                </tr>
            @endforeach
            <tr class="total-row">
                <td colspan="2">TOTAL DES RECETTES LOCATIVES</td>
                <td class="text-right" style="font-size: 14px; color: #16a34a;">{{ number_format($totalRecettes, 0, ',', ' ') }} F CFA</td>
            </tr>
        </tbody>
    </table>

    <div class="section-title">État des Dépenses & Maintenance</div>
    <table>
        <thead>
            <tr>
                <th style="width: 80px;">Date</th>
                <th>Bien concerné</th>
                <th>Libellé de la dépense</th>
                <th class="text-right">Montant</th>
            </tr>
        </thead>
        <tbody>
            @php $totalDepenses = 0; @endphp
            @php $hasDepenses = false; @endphp
            @foreach($biens as $bien)
                @foreach($bien->depenses as $dep)
                    @php $totalDepenses += $dep->montant; $hasDepenses = true; @endphp
                    <tr>
                        <td>{{ $dep->date_depense->format('d/m/Y') }}</td>
                        <td>{{ $bien->nom }}</td>
                        <td>{{ $dep->titre }} <small style="color: #64748b;">({{ $dep->categorie }})</small></td>
                        <td class="text-right" style="font-weight: bold; color: #dc2626;">{{ number_format($dep->montant, 0, ',', ' ') }} F</td>
                    </tr>
                @endforeach
            @endforeach
            @if(!$hasDepenses)
            <tr>
                <td colspan="4" style="text-align: center; color: #94a3b8; font-style: italic; padding: 20px;">Aucune dépense enregistrée sur cette période.</td>
            </tr>
            @endif
            <tr class="total-row">
                <td colspan="3">TOTAL DES CHARGES DÉDUCTIBLES</td>
                <td class="text-right" style="font-size: 14px; color: #dc2626;">{{ number_format($totalDepenses, 0, ',', ' ') }} F CFA</td>
            </tr>
        </tbody>
    </table>

    <div class="section-title">Synthèse Financière (Bilan Net)</div>
    <table style="font-size: 14px; margin-top: 10px;">
        <tr>
            <td style="border: none;">Recettes Brutes (A)</td>
            <td class="text-right" style="border: none; font-weight: bold;">{{ number_format($totalRecettes, 0, ',', ' ') }} F</td>
        </tr>
        <tr>
            <td style="border: none;">Total Charges & Dépenses (B)</td>
            <td class="text-right" style="border: none; font-weight: bold; color: #dc2626;">- {{ number_format($totalDepenses, 0, ',', ' ') }} F</td>
        </tr>
        <tr style="background: #1A365D; color: white;">
            <td style="padding: 15px; font-weight: bold; font-size: 16px;">NET À REVERSER (A - B)</td>
            <td class="text-right" style="padding: 15px; font-weight: bold; font-size: 20px;">
                {{ number_format($totalRecettes - $totalDepenses, 0, ',', ' ') }} F CFA
            </td>
        </tr>
    </table>

    <div style="margin-top: 30px; font-size: 11px; border: 1px solid #e2e8f0; padding: 15px; border-radius: 10px; color: #475569;">
        <strong>Note de Gestion :</strong> Ce relevé présente la situation nette de votre compte au {{ date('d/m/Y') }}.
        Le montant "Net à reverser" sera transféré sur votre compte après validation par le département comptable.
    </div>

    <div class="footer">
        Ontario Group ERP - Module de Gestion de Patrimoine v2.0<br>
        Ce document est une pièce comptable interne générée automatiquement.
    </div>
</body>
</html>
