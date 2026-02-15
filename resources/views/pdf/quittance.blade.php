<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>{{ $loyer->statut === 'payé' ? 'Quittance' : 'Appel' }} de Loyer - {{ $loyer->contrat->locataire->nom ?? 'Locataire' }}</title>
    <style>
        @page {
            margin: 0; 
            size: A4;
        }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 9px; /* Reduced from 10px */
            color: #334155; 
            line-height: 1.3; /* Reduced from 1.4 */
            background-color: #fff;
            margin: 0;
            padding: 0;
        }

        .page-container {
            position: relative;
            padding: 25px 40px; /* Reduced from 40px 50px */
        }

        /* ----- COLORS ----- */
        .text-brand-red { color: #cb2d2d; }
        .text-brand-blue { color: #1a2e3d; }
        .bg-brand-red { background-color: #cb2d2d; }
        .bg-brand-blue { background-color: #1a2e3d; }
        .border-brand-red { border-color: #cb2d2d; }
        
        /* ----- WATERMARK ----- */
        .watermark {
            position: absolute;
            top: 45%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-30deg);
            font-size: 80px; /* Reduced from 100px */
            font-weight: 900;
            color: #cb2d2d;
            opacity: 0.04; 
            white-space: nowrap;
            z-index: -10;
            text-transform: uppercase;
            letter-spacing: 15px;
            pointer-events: none;
        }

        /* ----- INFO CARDS (Tenant / Property) ----- */
        .info-section {
            width: 100%;
            margin-top: 15px; /* Reduced from 30px */
            margin-bottom: 20px; /* Reduced from 25px */
            border-collapse: separate;
            border-spacing: 15px 0; 
        }
        
        .info-box {
            background-color: #f8fafc;
            border-left: 4px solid #cb2d2d;
            border-radius: 4px; 
            padding: 10px 15px; /* Reduced from 15px 20px */
            width: 46%; 
        }

        .box-title {
            font-size: 7px; /* Reduced from 8px */
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #94a3b8; 
            font-weight: 700;
            margin-bottom: 5px; /* Reduced from 8px */
        }
        .box-main {
            font-size: 11px; /* Reduced from 13px */
            font-weight: 800;
            color: #1a2e3d;
            margin-bottom: 2px; /* Reduced from 4px */
            text-transform: uppercase;
        }
        .box-detail {
            font-size: 8px; /* Reduced from 9px */
            color: #64748b;
        }

        /* ----- NARRATION BLOCK ----- */
        .narration-box {
            background-color: #fff;
            border: 1px dashed #cbd5e1;
            border-radius: 6px;
            padding: 10px 15px; /* Reduced from 15px 20px */
            margin-bottom: 20px; /* Reduced from 30px */
            font-size: 10px; /* Reduced from 11px */
            color: #334155;
            text-align: justify;
            line-height: 1.5; /* Reduced from 1.6 */
        }
        .narration-box strong {
            color: #1a2e3d;
            font-weight: 800;
        }

        /* ----- TABLE STYLES ----- */
        .invoice-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px; /* Reduced from 20px */
        }
        .invoice-table th {
            text-align: left;
            padding: 8px 12px; /* Reduced from 12px 15px */
            background-color: #1a2e3d;
            color: #ffffff;
            font-size: 8px; /* Reduced from 9px */
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .invoice-table td {
            padding: 10px 12px; /* Reduced from 15px */
            border-bottom: 1px solid #e2e8f0;
            vertical-align: top;
        }
        .invoice-table tr:last-child td {
            border-bottom: 2px solid #1a2e3d; 
        }

        .item-title {
            font-size: 10px; /* Reduced from 11px */
            font-weight: 800;
            color: #1a2e3d;
            margin-bottom: 2px;
        }
        .item-desc {
            font-size: 8px; /* Reduced from 9px */
            color: #64748b;
        }
        .item-amount {
            font-size: 11px; /* Reduced from 12px */
            font-weight: 700;
            color: #334155;
            text-align: right;
        }

        /* ----- TOTALS SECTION ----- */
        .totals-container {
            width: 100%;
            margin-top: 5px; /* Reduced from 10px */
        }
        .totals-table {
            width: 250px;
            float: right;
            border-collapse: collapse;
        }
        .totals-table td {
            padding: 4px 0; /* Reduced from 6px */
            text-align: right;
        }
        .total-label {
            font-size: 9px; /* Reduced from 10px */
            color: #64748b;
            font-weight: 600;
            padding-right: 15px;
        }
        .total-value-sm {
            font-size: 10px; /* Reduced from 11px */
            font-weight: 700;
            color: #334155;
        }
        
        .grand-total-row td {
            padding-top: 8px; /* Reduced from 12px */
            padding-bottom: 8px; /* Reduced from 12px */
            border-top: 2px solid #cb2d2d; 
        }
        .grand-total-label {
            font-size: 10px; /* Reduced from 11px */
            font-weight: 900;
            color: #cb2d2d;
            text-transform: uppercase;
            padding-right: 15px;
        }
        .grand-total-value {
            font-size: 14px; /* Reduced from 18px */
            font-weight: 900;
            color: #cb2d2d;
        }

        .payment-info {
            position: absolute;
            bottom: 110px; /* Adjusted from 130px */
            left: 40px; /* Adjusted from 50px */
            font-size: 8px; /* Reduced from 9px */
            color: #64748b;
            line-height: 1.4;
        }

        .clearfix::after {
            content: "";
            clear: both;
            display: table;
        }
    </style>
</head>
<body>
    <div class="page-container">
        <!-- Watermark -->
        @php
            $isPaid = $loyer->statut === 'payé';
            $isPartial = $loyer->statut === 'partiellement_payé';
            $isCancelled = $loyer->statut === 'annulé';
            $isLate = $loyer->statut === 'en_retard';
            
            if ($isPaid) {
                $title = 'QUITTANCE DE LOYER';
                $watermark = 'QUITTANCE';
                $refPrefix = 'Q';
            } elseif ($isPartial) {
                $title = 'REÇU DE PAIEMENT PARTIEL';
                $watermark = 'PARTIEL';
                $refPrefix = 'P';
            } elseif ($isLate) {
                $title = 'AVIS DE RETARD DE PAIEMENT';
                $watermark = 'RETARD';
                $refPrefix = 'R';
            } elseif ($isCancelled) {
                $title = 'DOCUMENT ANNULÉ';
                $watermark = 'ANNULÉ';
                $refPrefix = 'X';
            } else {
                $title = 'APPEL DE LOYER';
                $watermark = 'APPEL';
                $refPrefix = 'A';
            }

            // Calculs
            $totalDu = $loyer->montant + ($loyer->penalite ?? 0);
            $dejaPaye = $loyer->paiements->sum('montant');
            $resteAPayer = $totalDu - $dejaPaye;
        @endphp
        <div class="watermark">{{ $watermark }}</div>

        <!-- HEADER INCLUDE -->
        @include('pdf.partials.header', [
            'title' => $title,
            'ref' => $refPrefix . date('Y') . '-' . str_pad($loyer->id, 4, '0', STR_PAD_LEFT),
            'date' => date('d/m/Y')
        ])

        <!-- INFO SECTION (Tenant / Property) -->
        <table class="info-section">
            <tr>
                <!-- Tenant Card -->
                <td class="info-box">
                    <div class="box-title">Locataire</div>
                    <div class="box-main">{{ strtoupper($loyer->contrat->locataire->nom) }}</div>
                    <div class="box-detail">
                        Réf : LOC-{{ str_pad($loyer->contrat->locataire->id, 3, '0', STR_PAD_LEFT) }}<br>
                        Tél : {{ $loyer->contrat->locataire->telephone ?? 'N/A' }}
                    </div>
                </td>
                
                <!-- Spacer -->
                <td style="width: 20px;"></td>

                <!-- Property Card -->
                <td class="info-box" style="border-left-color: #1a2e3d;"> <!-- Blue accent for property -->
                    <div class="box-title">Bien Loué</div>
                    <div class="box-main">{{ $loyer->contrat->bien->nom }}</div>
                    <div class="box-detail">
                        {{ ucfirst($loyer->contrat->bien->type) }}<br>
                        {{ $loyer->contrat->bien->adresse ?? 'Dakar, Sénégal' }}
                    </div>
                </td>
            </tr>
        </table>

        <!-- NARRATION -->
        <!-- NARRATION -->
        <div class="narration-box">
            @if($isPaid)
                Nous, <strong>ONTARIO GROUP S.A.</strong>, certifions avoir reçu de 
                <strong>{{ $loyer->contrat->locataire->nom }}</strong>
                la somme de <strong>{{ format_money($totalDu) }}</strong>
                en règlement libératoire du loyer pour la période de 
                <strong style="text-transform: uppercase;">{{ \Carbon\Carbon::parse($loyer->mois)->translatedFormat('F Y') }}</strong>.
            @elseif($isPartial)
                Nous, <strong>ONTARIO GROUP S.A.</strong>, certifions avoir reçu de 
                <strong>{{ $loyer->contrat->locataire->nom }}</strong>
                un acompte de <strong>{{ format_money($dejaPaye) }}</strong>
                sur un total de <strong>{{ format_money($totalDu) }}</strong>
                pour le loyer de la période de 
                <strong style="text-transform: uppercase;">{{ \Carbon\Carbon::parse($loyer->mois)->translatedFormat('F Y') }}</strong>.
                <br>
                <strong>Reste à payer : {{ format_money($resteAPayer) }} FCFA.</strong>
            @elseif($isLate)
                <strong>MISE EN DEMEURE :</strong><br>
                Sauf erreur ou omission de notre part, nous constatons que le loyer pour la période de 
                <strong style="text-transform: uppercase;">{{ \Carbon\Carbon::parse($loyer->mois)->translatedFormat('F Y') }}</strong>
                concernant le bien <strong>{{ $loyer->contrat->bien->nom }} ({{ ucfirst($loyer->contrat->bien->type) }})</strong> 
                n'a pas été réglé à ce jour.
                <br>
                <strong>Montant total réclamé : {{ format_money($totalDu) }} FCFA</strong> 
                (incluant les éventuelles pénalités de retard).
                <br>
                Nous vous prions de régulariser votre situation dans les plus brefs délais.
            @elseif($isCancelled)
                <strong>DOCUMENT ANNULÉ :</strong><br>
                Ce document annule et remplace tout document précédent concernant le loyer de la période de 
                <strong style="text-transform: uppercase;">{{ \Carbon\Carbon::parse($loyer->mois)->translatedFormat('F Y') }}</strong>.
                <br>
                Motif : {{ $loyer->note_annulation ?? 'Annulation administrative' }}
            @else
                <strong>AVIS D'ÉCHÉANCE :</strong><br>
                Nous vous prions de bien vouloir procéder au règlement du loyer pour la période de 
                <strong style="text-transform: uppercase;">{{ \Carbon\Carbon::parse($loyer->mois)->translatedFormat('F Y') }}</strong>
                concernant le bien <strong>{{ $loyer->contrat->bien->nom }} ({{ ucfirst($loyer->contrat->bien->type) }})</strong> situé à <strong>{{ $loyer->contrat->bien->adresse }}</strong>.
            @endif
        </div>

        <!-- LINE ITEMS TABLE -->
        <table class="invoice-table">
            <thead>
                <tr>
                    <th style="width: 65%; border-top-left-radius: 4px; border-bottom-left-radius: 4px;">Désignation</th>
                    <th style="width: 35%; text-align: right; border-top-right-radius: 4px; border-bottom-right-radius: 4px;">Montant</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <div class="item-title">Loyer Principal</div>
                        <div class="item-desc">Période : {{ \Carbon\Carbon::parse($loyer->mois)->translatedFormat('F Y') }}</div>
                    </td>
                    <td class="item-amount">
                        {{ format_money($loyer->montant, '') }} <span style="font-size: 9px; font-weight: 400; color: #94a3b8;">FCFA</span>
                    </td>
                </tr>
                @if($loyer->penalite > 0)
                <tr>
                    <td>
                        <div class="item-title text-brand-red">Pénalités de Retard</div>
                        <div class="item-desc">Frais appliqués pour retard de paiement</div>
                    </td>
                    <td class="item-amount text-brand-red">
                        {{ format_money($loyer->penalite, '') }} <span style="font-size: 9px; font-weight: 400; color: #cbd5e1;">FCFA</span>
                    </td>
                </tr>
                @endif
            </tbody>
        </table>

        <!-- TOTALS SECTION -->
        <div class="totals-container clearfix">
            <div class="payment-info">
                @if($isPaid || $isPartial)
                    <strong>Mode de paiement :</strong> {{ ucfirst($loyer->mode_paiement ?? 'Espèces / Virement') }}<br>
                    <strong>Date de valeur :</strong> {{ $loyer->date_paiement ? \Carbon\Carbon::parse($loyer->date_paiement)->format('d/m/Y') : date('d/m/Y') }}
                @elseif($isLate)
                     <span style="color: #cb2d2d; font-weight: bold;">PAIEMENT EN RETARD</span><br>
                     Merci de régulariser immédiatement.
                @elseif($isCancelled)
                     <span style="color: #cb2d2d; font-weight: bold;">ANNULÉ</span>
                @else
                    <strong>Date d'échéance :</strong> {{ \Carbon\Carbon::parse($loyer->mois)->endOfMonth()->format('d/m/Y') }}<br>
                    <span style="color: #cb2d2d; font-weight: bold;">À régler avant l'échéance.</span>
                @endif
            </div>

            <table class="totals-table">
                <tr>
                    <td class="total-label">Total du Loyer</td>
                    <td class="total-value-sm">{{ format_money($totalDu, '') }} FCFA</td>
                </tr>

                @if($isPartial)
                <tr>
                    <td class="total-label" style="color: #1a2e3d;">Déjà Versé</td>
                    <td class="total-value-sm" style="color: #1a2e3d;">{{ format_money($dejaPaye, '') }} FCFA</td>
                </tr>
                <tr class="grand-total-row">
                    <td class="grand-total-label">RESTE À PAYER</td>
                    <td class="grand-total-value">
                        {{ format_money($resteAPayer, '') }} 
                        <span style="font-size: 11px; font-weight: 600;">FCFA</span>
                    </td>
                </tr>
                @elseif($isPaid)
                <tr class="grand-total-row">
                    <td class="grand-total-label">NET PAYÉ</td>
                    <td class="grand-total-value">
                        {{ format_money($totalDu, '') }} 
                        <span style="font-size: 11px; font-weight: 600;">FCFA</span>
                    </td>
                </tr>
                @elseif($isLate)
                <tr class="grand-total-row">
                    <td class="grand-total-label">TOTAL EXIGIBLE</td>
                    <td class="grand-total-value" style="color: #cb2d2d;">
                        {{ format_money($totalDu, '') }} 
                        <span style="font-size: 11px; font-weight: 600;">FCFA</span>
                    </td>
                </tr>
                @else
                <tr class="grand-total-row">
                    <td class="grand-total-label">NET À PAYER</td>
                    <td class="grand-total-value">
                        {{ format_money($totalDu, '') }} 
                        <span style="font-size: 11px; font-weight: 600;">FCFA</span>
                    </td>
                </tr>
                @endif
            </table>
        </div>



        <!-- FOOTER INCLUDE -->
        @include('pdf.partials.footer')
    </div>
</body>
</html>
