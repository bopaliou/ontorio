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
            font-size: 11px;
            color: #1e293b;
            line-height: 1.6;
            margin: 0;
            padding: 0;
            background-color: #fff;
        }

        .page {
            position: relative;
            padding: 50px 60px;
            box-sizing: border-box;
        }

        /* Essential Colors */
        .text-primary { color: #cb2d2d; }
        .text-secondary { color: #1a2e3d; }
        .font-black { font-weight: 900; }
        .font-bold { font-weight: 700; }

        /* Header */
        .header {
            border-bottom: 4px solid #cb2d2d;
            padding-bottom: 25px;
            margin-bottom: 40px;
        }
        .agency-info {
            font-size: 9.5px;
            color: #64748b;
            line-height: 1.5;
            margin-top: 10px;
        }

        /* Document Header */
        .doc-header {
            text-align: center;
            margin-bottom: 45px;
        }
        .doc-type {
            font-size: 10px;
            font-weight: 900;
            color: #cb2d2d;
            text-transform: uppercase;
            letter-spacing: 5px;
            margin-bottom: 8px;
        }
        .doc-title {
            font-size: 26px;
            font-weight: 900;
            color: #1a2e3d;
            text-transform: uppercase;
            letter-spacing: -1px;
            line-height: 1;
        }
        .doc-ref {
            font-size: 9px;
            color: #94a3b8;
            font-weight: 700;
            margin-top: 10px;
            text-transform: uppercase;
        }

        /* Sections */
        .section {
            margin-bottom: 35px;
        }
        .section-header {
            background-color: #1a2e3d;
            color: #fff;
            padding: 8px 20px;
            font-size: 10px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 2px;
            border-radius: 4px;
            margin-bottom: 20px;
        }

        /* Boxes & Grids */
        .party-grid {
            width: 100%;
            margin-bottom: 35px;
            border-collapse: separate;
            border-spacing: 0;
        }
        .party-box {
            width: 48%;
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 20px;
            vertical-align: top;
        }
        .party-label {
            font-size: 8px;
            font-weight: 900;
            color: #cb2d2d;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            margin-bottom: 12px;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 6px;
        }
        .party-name {
            font-size: 13px;
            font-weight: 800; 
            color: #1a2e3d;
            margin-bottom: 8px;
        }
        .party-details {
            font-size: 10px;
            color: #64748b;
            line-height: 1.6;
        }

        /* Tables */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .data-table td {
            padding: 12px 15px;
            border-bottom: 1px solid #f1f5f9;
            font-size: 11px;
        }
        .data-table .label {
            width: 30%;
            font-weight: 800;
            color: #64748b;
            text-transform: uppercase;
            font-size: 9px;
            letter-spacing: 0.5px;
            background-color: #f8fafc;
        }
        .data-table .value {
            font-weight: 700;
            color: #1a2e3d;
        }

        /* Terms & Clauses */
        .clauses {
            text-align: justify;
            font-size: 10.5px;
            color: #475569;
        }
        .clause {
            margin-bottom: 18px;
        }
        .clause-title {
            font-weight: 900;
            color: #1a2e3d;
            text-transform: uppercase;
            font-size: 11px;
            margin-bottom: 6px;
            display: block;
        }

        /* Signatures */
        .signature-section {
            margin-top: 50px;
            page-break-inside: avoid;
        }
        .signature-row {
            width: 100%;
            margin-top: 30px;
        }
        .signature-box {
            width: 45%;
            height: 140px;
            border: 1px dashed #cbd5e1;
            background-color: #f8fafc;
            border-radius: 8px;
            padding: 15px;
            position: relative;
        }
        .sign-hint {
            position: absolute;
            top: 15px;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 7.5px;
            font-weight: 900;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .sign-name {
            position: absolute;
            bottom: 20px;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 11px;
            font-weight: 900;
            color: #1a2e3d;
        }

        /* Footer */
        .footer {
            position: absolute;
            bottom: 30px;
            left: 60px;
            right: 60px;
            text-align: center;
            font-size: 8px;
            color: #94a3b8;
            border-top: 1px solid #f1f5f9;
            padding-top: 15px;
        }

        .clear { clear: both; }
    </style>
</head>
<body>
    <div class="page">
        <!-- HEADER -->
        <div class="header">
            <table width="100%">
                <tr>
                    <td width="30%">
                        <img src="{{ public_path('images/ontorio-logo.png') }}" style="max-height: 85px;">
                    </td>
                    <td width="70%" align="right">
                        <div class="font-black text-secondary" style="font-size: 18px; letter-spacing: -0.5px;">ONTARIO GROUP S.A.</div>
                        <div class="agency-info">
                            Gestion Immobilière & Patrimoniale<br>
                            5 Félix Faure x Colbert, Dakar Plateau<br>
                            Sénégal | Tél : +221 33 822 32 67<br>
                            Email : commercial@ontariogroup.net
                        </div>
                    </td>
                </tr>
            </table>
        </div>

        <!-- DOCUMENT TITLE -->
        <div class="doc-header">
            <div class="doc-type">Contrat Officiel</div>
            <h1 class="doc-title">Bail à Usage d'Habitation</h1>
            <div class="doc-ref">RÉF : #{{ str_pad($contrat->id, 5, '0', STR_PAD_LEFT) }} | DATE : {{ now()->format('d/m/Y') }}</div>
        </div>

        <!-- PARTIES -->
        <table class="party-grid">
            <tr>
                <td class="party-box">
                    <div class="party-label">Le Bailleur</div>
                    <div class="party-name">ONTARIO GROUP S.A.</div>
                    <div class="party-details">
                        Représenté par son département de gestion locative.<br>
                        Dakar Plateau, Sénégal.
                    </div>
                </td>
                <td width="4%"></td>
                <td class="party-box">
                    <div class="party-label">Le Preneur</div>
                    <div class="party-name">{{ strtoupper($contrat->locataire->nom) }}</div>
                    <div class="party-details">
                        <strong>Tél :</strong> {{ $contrat->locataire->telephone ?? 'Non renseigné' }}<br>
                        <strong>Email :</strong> {{ $contrat->locataire->email ?? 'Non renseigné' }}<br>
                        <strong>CNI/Passeport :</strong> {{ $contrat->locataire->cni ?? 'Non renseigné' }}
                    </div>
                </td>
            </tr>
        </table>

        <!-- ARTICLE 1: OBJET -->
        <div class="section">
            <div class="section-header">Article 1 : Désignation des Lieux Loués</div>
            <table class="data-table">
                <tr>
                    <td class="label">Dénomination</td>
                    <td class="value">{{ $contrat->bien->nom }}</td>
                </tr>
                <tr>
                    <td class="label">Type de Bien</td>
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
                    <td class="label">Loyer Principal HT</td>
                    <td class="value" style="font-size: 14px; color: #cb2d2d;">{{ number_format($contrat->loyer_montant, 0, ',', ' ') }} FCFA</td>
                </tr>
                <tr>
                    <td class="label">Échéance de Paiement</td>
                    <td class="value">Le {{ $contrat->jour_paiement ?? '05' }} de chaque mois d'avance</td>
                </tr>
                <tr>
                    <td class="label">Duree du Bail</td>
                    <td class="value">Début le {{ \Carbon\Carbon::parse($contrat->date_debut)->translatedFormat('d F Y') }}</td>
                </tr>
            </table>
        </div>

        <!-- CLAUSES -->
        <div class="section">
            <div class="section-header">Article 3 : Dispositions Générales</div>
            <div class="clauses">
                <div class="clause">
                    <span class="clause-title">3.1 Obligations du Preneur</span>
                    Le Preneur s'engage à maintenir les lieux en bon état d'entretien et de propreté. Il est tenu d'effectuer à sa charge toutes les réparations locatives d'entretien courant. Toute transformation doit faire l'objet d'un accord écrit du Bailleur.
                </div>
                <div class="clause">
                    <span class="clause-title">3.2 Clause Résolutoire</span>
                    À défaut de paiement d'un seul terme de loyer à son échéance exacte, et un mois après une sommation de payer restée infructueuse, le présent contrat sera résilié de plein droit, sans formalité judiciaire.
                </div>
            </div>
        </div>

        <!-- SIGNATURES -->
        <div class="signature-section">
            <p style="font-size: 10px; color: #64748b; text-align: center; margin-bottom: 25px;">
                Fait à <strong>Dakar</strong>, le <strong>{{ now()->format('d/m/Y') }}</strong>, en deux exemplaires originaux.
            </p>
            <table width="100%">
                <tr>
                    <td class="signature-box">
                        <div class="sign-hint">Cachet et Signature du Bailleur (Ontario)</div>
                    </td>
                    <td width="10%"></td>
                    <td class="signature-box">
                        <div class="sign-hint">Signature du Preneur (Précédée de "Lu et Approuvé")</div>
                        <div class="sign-name">{{ strtoupper($contrat->locataire->nom) }}</div>
                    </td>
                </tr>
            </table>
        </div>

        <div class="footer">
            Ontario Group S.A. - Document contractuel généré numériquement - Page 1/1
        </div>
    </div>
</body>
</html>
