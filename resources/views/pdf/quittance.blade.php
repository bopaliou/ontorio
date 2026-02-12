<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Quittance de Loyer - {{ $loyer->contrat->locataire->nom ?? 'Locataire' }}</title>
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
            width: 100%;
            height: 100%;
            padding: 30px 40px;
            box-sizing: border-box;
        }

        /* Essential Colors */
        .text-primary { color: #cb2d2d; }
        .text-secondary { color: #1a2e3d; }

        /* Decorative Watermark */
        .watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-35deg);
            font-size: 80px;
            font-weight: 900;
            color: rgba(203, 45, 45, 0.03);
            white-space: nowrap;
            z-index: -1;
            text-transform: uppercase;
            letter-spacing: 10px;
        }

        /* Info Grid - Main Content */
        .info-grid {
            width: 100%;
            margin: 20px 0;
            border-collapse: collapse;
        }
        .info-card {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            padding: 15px;
            border-radius: 8px;
        }
        .info-col {
            width: 48%;
            vertical-align: top;
        }
        .spacer-col { width: 4%; }

        .card-label {
            font-size: 8px;
            font-weight: 900;
            color: #cb2d2d;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 5px;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 3px;
        }
        .card-value {
            font-size: 11px;
            font-weight: 800;
            color: #1a2e3d;
            margin-top: 5px;
        }
        .card-subtext {
            font-size: 9px;
            color: #64748b;
            margin-top: 3px;
            line-height: 1.3;
        }

        /* Narrative Statement */
        .narration {
            background-color: #fef2f2;
            padding: 15px;
            margin-bottom: 20px;
            border-left: 4px solid #cb2d2d;
            font-size: 10.5px;
            color: #334155;
            font-style: italic;
        }

        /* Table */
        .receipt-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
            page-break-inside: avoid;
        }
        .receipt-table th {
            background-color: #1a2e3d;
            color: #fff;
            text-align: left;
            padding: 10px 15px;
            font-size: 8.5px;
            font-weight: 900;
            text-transform: uppercase;
        }
        .receipt-table th.right { text-align: right; }

        .receipt-table td {
            padding: 12px 15px;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: middle;
        }
        .receipt-table .desc { color: #1a2e3d; font-weight: 700; font-size: 10px; }
        .receipt-table .sub-desc { color: #94a3b8; font-size: 8px; margin-top: 2px; }
        .receipt-table .amount { text-align: right; font-weight: 900; color: #1a2e3d; font-size: 11px; }

        /* Totals Block */
        .totals-section {
            width: 100%;
            margin-top: 15px;
            page-break-inside: avoid;
        }
        .totals-box {
            float: right;
            width: 220px;
        }
        .total-row {
            padding: 8px 0;
            border-bottom: 1px solid #f1f5f9;
        }
        .total-label { font-size: 9px; font-weight: 700; color: #94a3b8; text-transform: uppercase; }
        .total-val { font-size: 11px; font-weight: 800; color: #1a2e3d; text-align: right; float: right; }

        .final-total {
            background-color: #cb2d2d;
            color: #fff;
            padding: 12px 15px;
            border-radius: 6px;
            margin-top: 8px;
        }
        .final-label { font-size: 10px; font-weight: 900; text-transform: uppercase; }
        .final-val { font-size: 16px; font-weight: 900; text-align: right; float: right; line-height: 1; }

        /* Stamp */
        .paid-stamp {
            position: absolute;
            bottom: 180px;
            right: 250px;
            width: 100px;
            height: 100px;
            border: 5px solid #10b981;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            transform: rotate(-20deg);
            opacity: 0.15;
            z-index: 10;
        }
        .stamp-text {
            font-size: 24px;
            font-weight: 900;
            color: #10b981;
            text-transform: uppercase;
            letter-spacing: 3px;
        }

        .clearfix { clear: both; }
    </style>
</head>
<body>
    <div class="page">
        <div class="watermark">ONTARIO GROUP</div>

        <!-- HEADER -->
        @include('pdf.partials.header', [
            'title' => 'Quittance de Loyer',
            'ref' => 'Q' . date('Y') . '-' . str_pad($loyer->id, 4, '0', STR_PAD_LEFT),
            'date' => date('d/m/Y')
        ])

        <!-- INFO GRID -->
        <table class="info-grid">
            <tr>
                <td class="info-col">
                    <div class="info-card">
                        <div class="card-label">Identité du Locataire</div>
                        <div class="card-value">{{ strtoupper($loyer->contrat->locataire->nom) }}</div>
                        <div class="card-subtext">
                            <strong>Réf :</strong> LOC-{{ str_pad($loyer->contrat->locataire->id, 3, '0', STR_PAD_LEFT) }}<br>
                            <strong>Tél :</strong> {{ $loyer->contrat->locataire->telephone ?? 'N/A' }}
                        </div>
                    </div>
                </td>
                <td class="spacer-col"></td>
                <td class="info-col">
                    <div class="info-card">
                        <div class="card-label">Désignation du Bien</div>
                        <div class="card-value">{{ $loyer->contrat->bien->nom }}</div>
                        <div class="card-subtext">
                            <strong>Type :</strong> {{ ucfirst($loyer->contrat->bien->type) }}<br>
                            <strong>Adresse :</strong> {{ $loyer->contrat->bien->adresse ?? 'Dakar, Sénégal' }}
                        </div>
                    </div>
                </td>
            </tr>
        </table>

        <!-- STATEMENT -->
        <div class="narration">
            Nous, <strong>ONTARIO GROUP S.A.</strong>, certifions avoir reçu de M./Mme/Mlle <strong>{{ $loyer->contrat->locataire->nom }}</strong>,
            la somme de <strong>{{ format_money($loyer->montant + ($loyer->penalite ?? 0)) }}</strong>
            en règlement libératoire du loyer pour le mois de <strong>{{ \Carbon\Carbon::parse($loyer->mois)->translatedFormat('F Y') }}</strong>.
        </div>

        <!-- LINE ITEMS -->
        <table class="receipt-table">
            <thead>
                <tr>
                    <th style="width: 70%">Détails du terme</th>
                    <th style="width: 30%" class="right">Montant (FCFA)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <div class="desc">Loyer Principal</div>
                        <div class="sub-desc">Appel de loyer pour la période {{ \Carbon\Carbon::parse($loyer->mois)->translatedFormat('F Y') }}</div>
                    </td>
                    <td class="amount">{{ format_money($loyer->montant, '') }}</td>
                </tr>
                @if($loyer->penalite > 0)
                <tr>
                    <td>
                        <div class="desc" style="color: #cb2d2d;">Pénalités de Retard</div>
                        <div class="sub-desc">Frais de retard appliqués</div>
                    </td>
                    <td class="amount" style="color: #cb2d2d;">{{ format_money($loyer->penalite, '') }}</td>
                </tr>
                @endif
            </tbody>
        </table>

        <!-- TOTALS AREA -->
        <div class="totals-section">
            <div class="totals-box">
                <div class="total-row">
                    <span class="total-label">Total HT</span>
                    <span class="total-val">{{ format_money($loyer->montant + ($loyer->penalite ?? 0), '') }}</span>
                    <div class="clearfix"></div>
                </div>
                <div class="final-total">
                    <span class="final-label">Net Perçu</span>
                    <span class="final-val">{{ format_money($loyer->montant + ($loyer->penalite ?? 0)) }}</span>
                    <div class="clearfix"></div>
                </div>
            </div>
            <div class="clearfix"></div>
        </div>

        @if($loyer->reste_a_payer == 0)
        <div class="paid-stamp">
            <div class="stamp-text">PAYÉ</div>
        </div>
        @endif

        <div style="margin-top: 30px; font-size: 9px; color: #64748b;">
            <strong>Mode de versement :</strong> {{ $loyer->mode_paiement ?? 'Espèces / Virement' }}<br>
            <strong>Date d'encaissement :</strong> {{ $loyer->date_paiement ? \Carbon\Carbon::parse($loyer->date_paiement)->format('d/m/Y') : date('d/m/Y') }}
        </div>

        @include('pdf.partials.footer')
    </div>
</body>
</html>
