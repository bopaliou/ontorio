<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Contrat de Bail - {{ $contrat->locataire->nom }}</title>
    <style>
        @page {
            margin: 0;
            size: A4;
        }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 10.5px;
            color: #1e293b;
            line-height: 1.5;
            margin: 0;
            padding: 0;
            background-color: #fff;
        }

        .page {
            position: relative;
            padding: 40px 50px;
            box-sizing: border-box;
        }

        /* Essential Colors */
        .text-primary { color: #cb2d2d; }
        .text-secondary { color: #1a2e3d; }

        /* Sections */
        .section {
            margin-bottom: 25px;
            page-break-inside: avoid;
        }
        .section-header {
            background-color: #1a2e3d;
            color: #fff;
            padding: 6px 15px;
            font-size: 9.5px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            border-radius: 4px;
            margin-bottom: 15px;
        }

        /* Boxes & Grids */
        .party-grid {
            width: 100%;
            margin-bottom: 25px;
            border-collapse: collapse;
        }
        .party-box {
            width: 48%;
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 15px;
            vertical-align: top;
        }
        .party-label {
            font-size: 7.5px;
            font-weight: 900;
            color: #cb2d2d;
            text-transform: uppercase;
            margin-bottom: 8px;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 4px;
        }
        .party-name {
            font-size: 12px;
            font-weight: 800;
            color: #1a2e3d;
            margin-bottom: 5px;
        }
        .party-details {
            font-size: 9px;
            color: #64748b;
            line-height: 1.4;
        }

        /* Tables */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        .data-table td {
            padding: 10px 12px;
            border-bottom: 1px solid #f1f5f9;
            font-size: 10px;
        }
        .data-table .label {
            width: 35%;
            font-weight: 800;
            color: #64748b;
            text-transform: uppercase;
            font-size: 8px;
            background-color: #f8fafc;
        }
        .data-table .value {
            font-weight: 700;
            color: #1a2e3d;
        }

        /* Terms & Clauses */
        .clauses {
            text-align: justify;
            font-size: 10px;
            color: #475569;
        }
        .clause {
            margin-bottom: 15px;
        }
        .clause-title {
            font-weight: 900;
            color: #1a2e3d;
            text-transform: uppercase;
            font-size: 10px;
            margin-bottom: 4px;
            display: block;
        }

        /* Signatures */
        .signature-section {
            margin-top: 30px;
            page-break-inside: avoid;
        }
        .signature-box {
            width: 45%;
            height: 120px;
            border: 1px dashed #cbd5e1;
            background-color: #f8fafc;
            border-radius: 8px;
            position: relative;
        }
        .sign-hint {
            position: absolute;
            top: 12px;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 7px;
            font-weight: 900;
            color: #94a3b8;
            text-transform: uppercase;
        }
        .sign-name {
            position: absolute;
            bottom: 15px;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 10px;
            font-weight: 900;
            color: #1a2e3d;
        }

        .clear { clear: both; }
    </style>
</head>
<body>
    <div class="page">
        <!-- HEADER -->
        @include('pdf.partials.header', [
            'title' => 'Contrat de Bail Immobilier',
            'ref' => '#' . str_pad($contrat->id, 5, '0', STR_PAD_LEFT),
            'date' => now()->format('d/m/Y')
        ])

        <!-- PARTIES -->
        <table class="party-grid">
            <tr>
                <td class="party-box">
                    <div class="party-label">Le Bailleur</div>
                    <div class="party-name">ONTARIO GROUP S.A.</div>
                    <div class="party-details">
                        Gestionnaire de Patrimoine mandaté.<br>
                        Dakar Plateau, Sénégal.
                    </div>
                </td>
                <td style="width: 4%"></td>
                <td class="party-box">
                    <div class="party-label">Le Preneur (Locataire)</div>
                    <div class="party-name">{{ strtoupper($contrat->locataire->nom) }}</div>
                    <div class="party-details">
                        <strong>Tél :</strong> {{ $contrat->locataire->telephone ?? '--' }}<br>
                        <strong>CNI :</strong> {{ $contrat->locataire->cni ?? '--' }}
                    </div>
                </td>
            </tr>
        </table>

        <!-- ARTICLE 1: OBJET -->
        <div class="section">
            <div class="section-header">Article 1 : Désignation des Lieux</div>
            <table class="data-table">
                <tr>
                    <td class="label">Nom du Bien</td>
                    <td class="value">{{ $contrat->bien->nom }}</td>
                </tr>
                <tr>
                    <td class="label">Usage & Type</td>
                    <td class="value">{{ ucfirst($contrat->bien->type) }} ({{ $contrat->bien->surface ?? '--' }} m²)</td>
                </tr>
                <tr>
                    <td class="label">Localisation</td>
                    <td class="value">{{ $contrat->bien->adresse ?? 'Dakar, Sénégal' }}</td>
                </tr>
            </table>
        </div>

        <!-- ARTICLE 2: FINANCES -->
        <div class="section">
            <div class="section-header">Article 2 : Conditions Financières</div>
            <table class="data-table">
                <tr>
                    <td class="label">Loyer Mensuel HT</td>
                    <td class="value" style="color: #cb2d2d;">{{ format_money($contrat->loyer_montant) }}</td>
                </tr>
                <tr>
                    <td class="label">Dépôt de Garantie</td>
                    <td class="value">{{ format_money($contrat->caution ?? 0) }}</td>
                </tr>
                <tr>
                    <td class="label">Échéance</td>
                    <td class="value">Le 05 de chaque mois</td>
                </tr>
            </table>
        </div>

        <!-- ARTICLE 3: CLAUSES -->
        <div class="section">
            <div class="section-header">Article 3 : Dispositions Générales</div>
            <div class="clauses">
                <div class="clause">
                    <span class="clause-title">3.1 État des Lieux</span>
                    Le Preneur accepte les lieux dans l'état où ils se trouvent. Un état des lieux contradictoire sera annexé au présent contrat.
                </div>
                <div class="clause">
                    <span class="clause-title">3.2 Clause Résolutoire</span>
                    À défaut de paiement d'un seul terme de loyer à son échéance, le présent contrat pourra être résilié de plein droit après mise en demeure restée infructueuse.
                </div>
            </div>
        </div>

        <!-- SIGNATURES -->
        <div class="signature-section">
            <p style="font-size: 9px; color: #64748b; text-align: center; margin-bottom: 15px;">
                Fait à Dakar, le {{ now()->format('d/m/Y') }}
            </p>
            <table style="width: 100%">
                <tr>
                    <td class="signature-box">
                        <div class="sign-hint">Le Bailleur (Ontario Group)</div>
                    </td>
                    <td style="width: 10%"></td>
                    <td class="signature-box">
                        <div class="sign-hint">Le Preneur (Locataire)</div>
                        <div class="sign-name">{{ strtoupper($contrat->locataire->nom) }}</div>
                    </td>
                </tr>
            </table>
        </div>

        @include('pdf.partials.footer')
    </div>
</body>
</html>
