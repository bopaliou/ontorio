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
            font-size: 9px;
            color: #334155; 
            line-height: 1.4;
            background-color: #fff;
            margin: 0;
            padding: 0;
        }

        .page-container {
            position: relative;
            padding: 30px 40px;
            height: 100%;
            box-sizing: border-box;
        }

        /* ----- COLORS ----- */
        .text-brand-red { color: #cb2d2d; }
        .text-brand-blue { color: #1a2e3d; }
        
        /* ----- WATERMARK ----- */
        .watermark {
            position: absolute;
            top: 40%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-35deg);
            font-size: 90px;
            font-weight: 900;
            color: #cb2d2d;
            opacity: 0.03; 
            white-space: nowrap;
            z-index: -10;
            text-transform: uppercase;
            letter-spacing: 20px;
            pointer-events: none;
        }

        /* ----- INFO CARDS (Tenant / Property) ----- */
        .info-section {
            width: 100%;
            margin-bottom: 20px;
            border-collapse: collapse;
        }
        
        .info-box {
            background-color: #f8fafc;
            border: 1px solid #f1f5f9;
            border-left: 3px solid #cb2d2d;
            border-radius: 6px; 
            padding: 12px 15px;
            width: 48%; 
            vertical-align: top;
        }

        .box-title {
            font-size: 7px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #94a3b8; 
            font-weight: 800;
            margin-bottom: 4px;
        }
        .box-main {
            font-size: 11px;
            font-weight: 900;
            color: #0f172a;
            margin-bottom: 2px;
            text-transform: uppercase;
        }
        .box-detail {
            font-size: 8.5px;
            color: #64748b;
            line-height: 1.3;
        }

        /* ----- NARRATION BLOCK ----- */
        .narration-box {
            background-color: #fafaf9;
            border-radius: 6px;
            padding: 12px 15px;
            margin-bottom: 20px;
            font-size: 9.5px;
            color: #475569;
            text-align: justify;
            line-height: 1.5;
            border-left: 2px solid #e2e8f0;
        }
        .narration-box strong {
            color: #0f172a;
            font-weight: 800;
        }

        /* ----- TABLE STYLES ----- */
        .invoice-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        .invoice-table th {
            text-align: left;
            padding: 8px 12px;
            background-color: #f8fafc;
            color: #475569;
            font-size: 7.5px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 1px;
            border-bottom: 2px solid #e2e8f0;
        }
        .invoice-table td {
            padding: 10px 12px;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: middle;
        }
        .invoice-table tr:last-child td {
            border-bottom: 1px solid #cbd5e1; 
        }

        .item-title {
            font-size: 9.5px;
            font-weight: 800;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .item-desc {
            font-size: 8px;
            color: #64748b;
            margin-top: 2px;
        }
        .item-amount {
            font-size: 11px;
            font-weight: 800;
            color: #0f172a;
            text-align: right;
        }

        /* ----- TOTALS SECTION ----- */
        .totals-container {
            width: 100%;
            margin-top: 5px;
        }
        .totals-table {
            width: 250px;
            float: right;
            border-collapse: collapse;
        }
        .totals-table td {
            padding: 5px 0;
            text-align: right;
        }
        .total-label {
            font-size: 8.5px;
            color: #64748b;
            font-weight: 700;
            padding-right: 15px;
            text-transform: uppercase;
        }
        .total-value-sm {
            font-size: 10px;
            font-weight: 800;
            color: #334155;
        }
        
        .grand-total-row td {
            padding-top: 10px;
            padding-bottom: 10px;
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

        .payment-info {
            float: left;
            width: 50%;
            font-size: 8px;
            color: #64748b;
            line-height: 1.5;
            padding-top: 10px;
        }

        .clearfix::after {
            content: "";
            clear: both;
            display: table;
        }
        
        .signature-zone {
            margin-top: 40px;
            width: 200px;
            float: right;
            text-align: center;
        }
        .signature-line {
            border-bottom: 1px dashed #cbd5e1;
            height: 60px;
            margin-bottom: 5px;
        }
        .signature-label {
            font-size: 7px;
            font-weight: 800;
            color: #94a3b8;
            text-transform: uppercase;
        }
    </style>
</head>
<body>
    <div class="page-container">
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
            'ref' => $refPrefix . date('Y') . '-' . str_pad($loyer->id, 5, '0', STR_PAD_LEFT),
            'date' => date('d/m/Y')
        ])

        <!-- INFO SECTION (Tenant / Property) -->
        <table class="info-section">
            <tr>
                <!-- Tenant Card -->
                <td class="info-box">
                    <div class="box-title">Identité du Locataire</div>
                    <div class="box-main">{{ $loyer->contrat->locataire->nom }}</div>
                    <div class="box-detail">
                        Réf : LOC-{{ str_pad($loyer->contrat->locataire->id, 4, '0', STR_PAD_LEFT) }}<br>
                        Contact : {{ $loyer->contrat->locataire->telephone ?? '--' }}
                    </div>
                </td>
                
                <!-- Spacer -->
                <td style="width: 4%;"></td>

                <!-- Property Card -->
                <td class="info-box" style="border-left-color: #1a2e3d;">
                    <div class="box-title">Désignation du Bien</div>
                    <div class="box-main">{{ $loyer->contrat->bien->nom }}</div>
                    <div class="box-detail">
                        Type : {{ ucfirst($loyer->contrat->bien->type) }}<br>
                        Adresse : {{ $loyer->contrat->bien->adresse ?? 'Sénégal' }}
                    </div>
                </td>
            </tr>
        </table>

        <!-- NARRATION -->
        <div class="narration-box">
            @if($isPaid)
                Nous soussignés, <strong>ONTARIO GROUP S.A.</strong>, reconnaissons avoir reçu la somme de <strong>{{ format_money($totalDu) }}</strong> en règlement libératoire intégral du loyer pour la période de <strong style="text-transform: uppercase;">{{ \Carbon\Carbon::parse($loyer->mois)->translatedFormat('F Y') }}</strong>, dont quittance sans préjudice de toutes autres causes ou créances éventuelles.
            @elseif($isPartial)
                Nous accusons réception d'un paiement partiel de <strong>{{ format_money($dejaPaye) }}</strong> sur le loyer couvrant la période de <strong style="text-transform: uppercase;">{{ \Carbon\Carbon::parse($loyer->mois)->translatedFormat('F Y') }}</strong>. <strong>Reste à charge : {{ format_money($resteAPayer) }} FCFA.</strong>
            @elseif($isLate)
                Sauf erreur de notre part, votre loyer de la période de <strong style="text-transform: uppercase;">{{ \Carbon\Carbon::parse($loyer->mois)->translatedFormat('F Y') }}</strong> présente un arriéré. Veuillez régulariser ce montant de <strong>{{ format_money($totalDu) }} FCFA</strong> dans les plus brefs délais pour éviter des poursuites.
            @elseif($isCancelled)
                Ce document annule et remplace tout avis d'émission précédent concernant la période de <strong style="text-transform: uppercase;">{{ \Carbon\Carbon::parse($loyer->mois)->translatedFormat('F Y') }}</strong>. (Motif : {{ $loyer->note_annulation ?? 'Annulation administrative' }})
            @else
                Le présent avis tient lieu d'appel formel pour le loyer couvrant la période de <strong style="text-transform: uppercase;">{{ \Carbon\Carbon::parse($loyer->mois)->translatedFormat('F Y') }}</strong>. Le paiement doit nous parvenir avant l'échéance convenue.
            @endif
        </div>

        <!-- LINE ITEMS TABLE -->
        <table class="invoice-table">
            <thead>
                <tr>
                    <th style="width: 65%;">Description des charges</th>
                    <th style="width: 35%; text-align: right;">Montant Total</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <div class="item-title">Loyer Principal</div>
                        <div class="item-desc">Mensualité contractuelle - {{ \Carbon\Carbon::parse($loyer->mois)->translatedFormat('F Y') }}</div>
                    </td>
                    <td class="item-amount">
                        {{ format_money($loyer->montant, '') }} <span style="font-size: 8px; color: #94a3b8;">FCFA</span>
                    </td>
                </tr>
                @if($loyer->penalite > 0)
                <tr>
                    <td>
                        <div class="item-title text-brand-red">Pénalités de Retard</div>
                        <div class="item-desc">Frais administratifs suite au dépassement de l'échéance</div>
                    </td>
                    <td class="item-amount text-brand-red">
                        {{ format_money($loyer->penalite, '') }} <span style="font-size: 8px; color: #cbd5e1;">FCFA</span>
                    </td>
                </tr>
                @endif
            </tbody>
        </table>

        <!-- TOTALS SECTION -->
        <div class="totals-container clearfix">
            <div class="payment-info">
                @if($isPaid || $isPartial)
                    <strong style="color: #0f172a; font-size: 9px; text-transform: uppercase;">Détails d'encaissement :</strong><br>
                    Mode : {{ ucfirst($loyer->mode_paiement ?? 'Non défini') }}<br>
                    Date de valeur : {{ $loyer->date_paiement ? \Carbon\Carbon::parse($loyer->date_paiement)->format('d/m/Y') : date('d/m/Y') }}
                @elseif($isLate)
                     <strong style="color: #cb2d2d; font-size: 9px; text-transform: uppercase;">Mise en demeure</strong><br>
                     Échéance initiale : {{ \Carbon\Carbon::parse($loyer->mois)->endOfMonth()->format('d/m/Y') }}
                @elseif($isCancelled)
                     <strong style="color: #94a3b8; font-size: 9px; text-transform: uppercase;">Statut annulé</strong>
                @else
                    <strong style="color: #0f172a; font-size: 9px; text-transform: uppercase;">Échéance :</strong> {{ \Carbon\Carbon::parse($loyer->mois)->endOfMonth()->format('d/m/Y') }}<br>
                    Le non-paiement aux dates convenues entraînera des pénalités.
                @endif
            </div>

            <table class="totals-table">
                <tr>
                    <td class="total-label">Total du Loyer</td>
                    <td class="total-value-sm">{{ format_money($totalDu, '') }} FCFA</td>
                </tr>

                @if($isPartial)
                <tr>
                    <td class="total-label" style="color: #1a2e3d;">Acompte Versé</td>
                    <td class="total-value-sm" style="color: #1a2e3d;">{{ format_money($dejaPaye, '') }} FCFA</td>
                </tr>
                <tr class="grand-total-row">
                    <td class="grand-total-label">RESTE À PAYER</td>
                    <td class="grand-total-value">
                        {{ format_money($resteAPayer, '') }} 
                        <span style="font-size: 10px;">FCFA</span>
                    </td>
                </tr>
                @elseif($isPaid)
                <tr class="grand-total-row">
                    <td class="grand-total-label">MONTANT RÉGLÉ</td>
                    <td class="grand-total-value">
                        {{ format_money($totalDu, '') }} 
                        <span style="font-size: 10px;">FCFA</span>
                    </td>
                </tr>
                @elseif($isLate)
                <tr class="grand-total-row">
                    <td class="grand-total-label">EXIGIBLE IMMÉDIAT</td>
                    <td class="grand-total-value">
                        {{ format_money($totalDu, '') }} 
                        <span style="font-size: 10px;">FCFA</span>
                    </td>
                </tr>
                @else
                <tr class="grand-total-row">
                    <td class="grand-total-label">NET À PAYER HT</td>
                    <td class="grand-total-value">
                        {{ format_money($totalDu, '') }} 
                        <span style="font-size: 10px;">FCFA</span>
                    </td>
                </tr>
                @endif
            </table>
        </div>
        
        <div class="clearfix"></div>
        
        @if($isPaid || $isPartial)
        <div class="signature-zone">
            <div class="signature-line"></div>
            <div class="signature-label">Le Gestionnaire / L'Agence<br><span style="color: #cb2d2d;">POUR ACQUIT</span></div>
        </div>
        @endif

        <!-- FOOTER INCLUDE -->
        @include('pdf.partials.footer')
    </div>
</body>
</html>
