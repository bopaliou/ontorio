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
            font-size: 11px;
            color: #1e293b;
            line-height: 1.6;
            background-color: #fff;
            margin: 0;
            padding: 0;
        }

        .page {
            position: relative;
            width: 100%;
            height: 100%;
            padding: 40px 50px;
            box-sizing: border-box;
        }

        /* Essential Colors */
        .text-primary { color: #cb2d2d; }
        .text-secondary { color: #1a2e3d; }
        .bg-secondary { background-color: #1a2e3d; }

        /* Decorative Watermark */
        .watermark {
            position: absolute;
            top: 55%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-35deg);
            font-size: 100px;
            font-weight: 900;
            color: rgba(203, 45, 45, 0.03);
            white-space: nowrap;
            z-index: -1;
            text-transform: uppercase;
            letter-spacing: 15px;
        }

        /* Header */
        .header {
            width: 100%;
            margin-bottom: 35px;
        }
        .header-content {
            border-bottom: 3px solid #cb2d2d;
            padding-bottom: 25px;
        }
        .agency-name {
            font-size: 24px;
            font-weight: 900;
            color: #1a2e3d;
            letter-spacing: -1px;
            margin: 0;
        }
        .agency-info {
            font-size: 9.5px;
            color: #64748b;
            line-height: 1.5;
            margin-top: 8px;
        }

        /* Document Title Badge */
        .doc-title-section {
            text-align: right;
            margin-top: -85px;
        }
        .doc-title {
            display: inline-block;
            background-color: #1a2e3d;
            color: #fff;
            padding: 10px 30px;
            border-radius: 4px;
            font-size: 15px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 2px;
            box-shadow: 0 4px 6px rgba(26, 46, 61, 0.1);
        }
        .doc-ref {
            display: block;
            font-size: 9px;
            color: #94a3b8;
            margin-top: 10px;
            font-weight: 800;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        /* Info Grid - Main Content */
        .info-grid {
            width: 100%;
            margin: 30px 0;
            border-collapse: separate;
            border-spacing: 0;
        }
        .info-card {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 20px;
        }
        .info-col {
            width: 48%;
            vertical-align: top;
        }
        .spacer-col { width: 4%; }

        .card-label {
            font-size: 8.5px;
            font-weight: 900;
            color: #cb2d2d;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            margin-bottom: 10px;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 5px;
        }
        .card-value { 
            font-size: 12px; 
            font-weight: 800; 
            color: #1a2e3d; 
            margin-top: 5px;
        }
        .card-subtext { 
            font-size: 10px; 
            color: #64748b; 
            margin-top: 5px;
            line-height: 1.4;
        }

        /* Narrative Statement */
        .narration {
            background-color: #fff;
            padding: 20px;
            margin-bottom: 30px;
            border-left: 5px solid #cb2d2d;
            font-size: 11.5px;
            color: #334155;
            background: linear-gradient(to right, #fef2f2, #ffffff);
            font-style: italic;
        }

        /* Table */
        .receipt-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .receipt-table th {
            background-color: #1a2e3d;
            color: #fff;
            text-align: left;
            padding: 12px 20px;
            font-size: 9px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .receipt-table th.right { text-align: right; }
        
        .receipt-table td {
            padding: 18px 20px;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: middle;
        }
        .receipt-table .desc { color: #1a2e3d; font-weight: 700; font-size: 11px; }
        .receipt-table .sub-desc { color: #94a3b8; font-size: 9px; font-weight: normal; margin-top: 3px; }
        .receipt-table .amount { text-align: right; font-weight: 900; color: #1a2e3d; font-size: 12px; }

        /* Totals Block */
        .totals-section {
            width: 100%;
            margin-top: 25px;
        }
        .totals-box {
            float: right;
            width: 250px;
            padding: 0;
        }
        .total-row {
            padding: 10px 0;
            border-bottom: 1px solid #f1f5f9;
        }
        .total-label { font-size: 10px; font-weight: 700; color: #94a3b8; text-transform: uppercase; }
        .total-val { font-size: 13px; font-weight: 800; color: #1a2e3d; text-align: right; float: right; }
        
        .final-total {
            background-color: #cb2d2d;
            color: #fff;
            padding: 15px 20px;
            border-radius: 8px;
            margin-top: 10px;
            box-shadow: 0 4px 12px rgba(203, 45, 45, 0.2);
        }
        .final-label { font-size: 11px; font-weight: 900; text-transform: uppercase; letter-spacing: 1px; }
        .final-val { font-size: 20px; font-weight: 900; text-align: right; float: right; line-height: 1; }

        /* Stamp */
        .paid-stamp {
            position: absolute;
            bottom: 220px;
            right: 280px;
            width: 130px;
            height: 130px;
            border: 6px solid #10b981;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            transform: rotate(-20deg);
            opacity: 0.12;
            z-index: 10;
        }
        .stamp-text {
            font-size: 30px;
            font-weight: 900;
            color: #10b981;
            text-transform: uppercase;
            letter-spacing: 4px;
        }

        /* Footer & Signatures */
        .footer {
            position: absolute;
            bottom: 50px;
            left: 50px;
            right: 50px;
        }
        .signature-area {
            width: 100%;
            margin-bottom: 40px;
        }
        .sign-box {
            border: 1px dashed #cbd5e1;
            background-color: #f8fafc;
            border-radius: 8px;
            height: 110px;
            position: relative;
        }
        .sign-label {
            position: absolute;
            top: 15px;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 8px;
            font-weight: 900;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .bottom-legal {
            text-align: center;
            font-size: 8.5px;
            color: #94a3b8;
            border-top: 1px solid #f1f5f9;
            padding-top: 20px;
            line-height: 1.6;
        }

        .clearfix { clear: both; }
    </style>
</head>
<body>
    <div class="page">
        <div class="watermark">ONTARIO GROUP</div>

        <!-- HEADER -->
        <div class="header">
            <div class="header-content">
                <table width="100%">
                    <tr>
                        <td width="50%" valign="bottom">
                            <h1 class="agency-name">ONTARIO GROUP S.A.</h1>
                            <div class="agency-info">
                                <strong>Pionnier de la Gestion Immobilière au Sénégal</strong><br>
                                Siége Social : 5 Félix Faure x Colbert, Dakar Plateau<br>
                                NINEA : 006421045 2G3 | RCCM : SN.DKR.2017.B.15234<br>
                                <strong>Assistance :</strong> +221 33 822 32 67 | commercial@ontariogroup.net
                            </div>
                        </td>
                        <td width="50%" align="right" valign="top">
                            <img src="{{ public_path('images/ontorio-logo.png') }}" style="max-height: 75px;">
                        </td>
                    </tr>
                </table>
            </div>
            
            <div class="doc-title-section">
                <div class="doc-title">Quittance de Loyer</div>
                <div class="doc-ref">N° Q{{ date('Y') }}-{{ str_pad($loyer->id, 4, '0', STR_PAD_LEFT) }} | ÉTABLI LE {{ date('d/m/Y') }}</div>
            </div>
        </div>

        <!-- INFO GRID -->
        <table class="info-grid">
            <tr>
                <td class="info-col">
                    <div class="info-card">
                        <div class="card-label">Identité du Locataire</div>
                        <div class="card-value">{{ strtoupper($loyer->contrat->locataire->nom) }}</div>
                        <div class="card-subtext">
                            <strong>Référence Dossier :</strong> LOC-{{ str_pad($loyer->contrat->locataire->id, 3, '0', STR_PAD_LEFT) }}<br>
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
            la somme de <strong>{{ number_format($loyer->montant + ($loyer->penalite ?? 0), 0, ',', ' ') }} FCFA</strong>
            en règlement libératoire du loyer pour le mois de <strong>{{ \Carbon\Carbon::parse($loyer->mois)->translatedFormat('F Y') }}</strong>.
        </div>

        <!-- LINE ITEMS -->
        <table class="receipt-table">
            <thead>
                <tr>
                    <th width="70%">Description des Terminologies du Loyer</th>
                    <th width="30%" class="right">Montant (FCFA)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <div class="desc">Loyer Principal</div>
                        <div class="sub-desc">Appel de loyer standard pour la période {{ \Carbon\Carbon::parse($loyer->mois)->translatedFormat('F Y') }}</div>
                    </td>
                    <td class="amount">{{ number_format($loyer->montant, 0, ',', ' ') }}</td>
                </tr>
                @if($loyer->penalite > 0)
                <tr>
                    <td>
                        <div class="desc" style="color: #cb2d2d;">Pénalités de Retard</div>
                        <div class="sub-desc">Frais administratifs suite au retard de régularisation</div>
                    </td>
                    <td class="amount" style="color: #cb2d2d;">{{ number_format($loyer->penalite, 0, ',', ' ') }}</td>
                </tr>
                @endif
            </tbody>
        </table>

        <!-- TOTALS AREA -->
        <div class="totals-section">
            <div class="totals-box">
                <div class="total-row">
                    <span class="total-label">Sous-total HT</span>
                    <span class="total-val">{{ number_format($loyer->montant + ($loyer->penalite ?? 0), 0, ',', ' ') }}</span>
                    <div class="clearfix"></div>
                </div>
                <div class="final-total">
                    <span class="final-label">Net Perçu</span>
                    <span class="final-val">{{ number_format($loyer->montant + ($loyer->penalite ?? 0), 0, ',', ' ') }} F</span>
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

        <!-- SIGNATURES & FOOTER -->
        <div class="footer">
            <table width="100%" class="signature-area">
                <tr>
                    <td width="65%" style="font-size: 9px; color: #64748b; padding-right: 50px;">
                        <div style="font-weight: 900; color: #1a2e3d; text-transform: uppercase; margin-bottom: 8px; letter-spacing: 0.5px;">Informations de Paiement</div>
                        <strong>Mode de versement :</strong> {{ $loyer->mode_paiement ?? 'Espèces / Chèque / Virement' }}<br>
                        <strong>Date d'encaissement :</strong> {{ $loyer->date_paiement ? \Carbon\Carbon::parse($loyer->date_paiement)->format('d/m/Y') : date('d/m/Y') }}<br>
                        <br>
                        <em>La présente quittance libère le locataire pour le mois indiqué. Elle n'emporte pas présomption de paiement des termes antérieurs non encore soldés.</em>
                    </td>
                    <td width="35%">
                        <div class="sign-box">
                            <div class="sign-label">Cachet de l'Agence & Signature</div>
                        </div>
                    </td>
                </tr>
            </table>

            <div class="bottom-legal">
                <strong>ONTARIO GROUP S.A.</strong> - Société Anonyme au capital de 10.000.000 FCFA<br>
                Une plateforme éditée par le département digital de Ontario Group.<br>
                L'authenticité de ce document peut être vérifiée sur notre portail client : https://ontariogroup.net
            </div>
        </div>
    </div>
</body>
</html>
