<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Quittance de Loyer - {{ $loyer->contrat->locataire->nom ?? 'Locataire' }}</title>
    <style>
        @page { margin: 30px; }
        body { 
            font-family: 'Helvetica', 'Arial', sans-serif; 
            font-size: 11px; 
            color: #333; 
            line-height: 1.3;
        }
        
        /* Brand Colors */
        .text-brand { color: #274256; }
        .bg-brand { background-color: #274256; color: white; }
        
        .header { 
            border-bottom: 2px solid #274256; 
            padding-bottom: 15px; 
            margin-bottom: 25px;
        }
        .logo-text { 
            font-size: 18px; 
            font-weight: 900; 
            color: #274256; 
            text-transform: uppercase;
        }
        .logo-sub {
            font-size: 8px;
            color: #64748b;
            letter-spacing: 1px;
            text-transform: uppercase;
        }
 
        .doc-title {
            text-align: center;
            font-size: 18px;
            font-weight: bold;
            text-transform: uppercase;
            color: #274256;
            margin-bottom: 5px;
            border: 1px solid #274256;
            padding: 8px;
            border-radius: 4px;
            background-color: #f8fafc;
        }
 
        .info-grid {
            width: 100%;
            margin-bottom: 20px;
        }
        .info-box {
            background-color: #f1f5f9;
            padding: 12px;
            border-radius: 6px;
            height: 85px;
        }
        .box-title {
            font-size: 9px;
            font-weight: bold;
            color: #64748b;
            text-transform: uppercase;
            border-bottom: 1px solid #cbd5e1;
            padding-bottom: 3px;
            margin-bottom: 8px;
        }
 
        .main-content {
            margin: 20px 0;
            line-height: 1.5;
            text-align: justify;
        }
 
        .amount-box {
            text-align: center;
            margin: 20px 0;
            padding: 15px;
            background-color: #ecfdf5;
            border: 1px dashed #059669;
            border-radius: 8px;
        }
        .amount-value {
            font-size: 20px;
            font-weight: 900;
            color: #047857;
        }
        .amount-text {
            font-size: 11px;
            color: #065f46;
            font-style: italic;
        }
 
        .details-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
        }
        .details-table th {
            text-align: left;
            padding: 8px;
            background-color: #274256;
            color: white;
            font-size: 9px;
            text-transform: uppercase;
        }
        .details-table td {
            padding: 8px;
            border-bottom: 1px solid #e2e8f0;
        }
 
        .signature {
            border-top: 1px solid #e2e8f0;
            padding-top: 15px;
            margin-top: 30px;
        }
        .stamp-box {
            float: right;
            width: 180px;
            height: 80px;
            border: 1px solid #cbd5e1;
            background-color: #f8fafc;
            text-align: center;
            padding-top: 60px;
            font-size: 9px;
            color: #94a3b8;
        }
 
        .footer {
            position: fixed;
            bottom: -15px;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 8px;
            color: #94a3b8;
            border-top: 1px solid #e2e8f0;
            padding-top: 8px;
        }
    </style>
</head>
<body>
    <!-- HEADER -->
    <div class="header">
        <table style="width: 100%;">
            <tr>
                <td style="width: 50%; vertical-align: top;">
                    <img src="{{ public_path('images/ontorio-logo.png') }}" style="max-height: 60px; margin-bottom: 5px;">
                    <div class="logo-sub">Bien loger dans un bon logement</div>
                    <div style="margin-top: 10px; font-size: 10px; color: #64748b;">
                        5 Félix Faure x Colbert<br>
                        Dakar Plateau, Sénégal<br>
                        BP 06813
                    </div>
                </td>
                <td style="width: 50%; text-align: right; vertical-align: top; font-size: 10px;">
                    <strong>Contact Agence</strong><br>
                    Tél : +221 33 822 32 67 / 33 842 05 80<br>
                    Mobile : 78 105 35 54<br>
                    Email : commercial@ontariogroup.net
                </td>
            </tr>
        </table>
    </div>

    <div class="doc-title">QUITTANCE DE LOYER</div>

    <!-- INFOS -->
    <table class="info-grid" cellpadding="0" cellspacing="10">
        <tr>
            <td width="50%">
                <div class="info-box">
                    <div class="box-title">Bailleur / Propriétaire</div>
                    <strong>ONTARIO GROUP S.A.</strong><br>
                    Pour le compte du propriétaire.<br>
                    <i>Gestion Immobilière & Patrimoine</i>
                </div>
            </td>
            <td width="50%">
                <div class="info-box">
                    <div class="box-title">Locataire</div>
                    <strong>{{ strtoupper($loyer->contrat->locataire->nom) }}</strong><br>
                    {{ $loyer->contrat->bien->adresse ?? $loyer->contrat->bien->ville }}<br>
                    Contrat #C-{{ $loyer->contrat->id }}
                </div>
            </td>
        </tr>
    </table>

    <!-- TEXTE PRINCIPAL -->
    <div class="main-content">
        Je soussigné, <strong>ONTARIO GROUP S.A.</strong>, agissant en qualité de gérant de l'immeuble/bien sis à 
        <strong>{{ $loyer->contrat->bien->nom }}</strong>,
        déclare avoir reçu de Monsieur/Madame <strong>{{ $loyer->contrat->locataire->nom }}</strong>,
        la somme indiquée ci-dessous, en paiement du loyer et des charges pour la période de : 
        <strong>{{ \Carbon\Carbon::parse($loyer->mois)->translatedFormat('F Y') }}</strong>.
    </div>

    <!-- MONTANT -->
    <div class="amount-box">
        <div class="amount-value">{{ number_format($loyer->montant, 0, ',', ' ') }} F CFA</div>
        <div class="amount-text">Détail : Loyer {{ number_format($loyer->montant, 0, ',', ' ') }} F</div>
    </div>

    <!-- TABLEAU DETAIL -->
    <table class="details-table">
        <thead>
            <tr>
                <th width="50%">Désignation</th>
                <th width="25%">Période</th>
                <th width="25%" style="text-align: right;">Montant Payé</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    <strong>Loyer d'habitation</strong><br>
                    <span style="font-size: 10px; color: #64748b;">{{ $loyer->contrat->bien->nom }} - {{ ucfirst($loyer->contrat->bien->type) }}</span>
                </td>
                <td>{{ \Carbon\Carbon::parse($loyer->mois)->translatedFormat('F Y') }}</td>
                <td style="text-align: right; font-weight: bold;">{{ number_format($loyer->montant, 0, ',', ' ') }} F</td>
            </tr>
            <!-- Ligne vide pour aérer si besoin ou charges -->
        </tbody>
        <tfoot>
            <tr>
                <td colspan="2" style="border-bottom: none; text-align: right; padding-top: 15px; font-weight: bold;">TOTAL PERÇU</td>
                <td style="border-bottom: none; text-align: right; padding-top: 15px; font-weight: bold; background-color: #f8fafc;">{{ number_format($loyer->montant, 0, ',', ' ') }} F</td>
            </tr>
        </tfoot>
    </table>

    <div style="font-size: 11px; color: #64748b; font-style: italic; margin-bottom: 30px;">
        Cette quittance annule tous les reçus qui auraient pu être donnés pour acompte versé sur le présent terme. 
        En cas de congé, elle vaudra pièce justificative pour l'établissement de l'état des lieux de sortie.
    </div>

    <!-- SIGNATURE -->
    <div class="signature">
        <div style="float: left; margin-top: 20px;">
            Fait à <strong>Dakar</strong>, le <strong>{{ $loyer->date_paiement ? \Carbon\Carbon::parse($loyer->date_paiement)->format('d/m/Y') : date('d/m/Y') }}</strong><br>
            <span style="font-size: 10px; color: #94a3b8;">Référence Paiement : {{ $loyer->paiements->first()->reference ?? 'ESP-' . $loyer->id }}</span>
        </div>
        
        <div class="stamp-box">
            Cachet et Signature
        </div>
        <div style="clear: both;"></div>
    </div>

    <!-- FOOTER -->
    <div class="footer">
        Quittance #Q-{{ $loyer->id }}-{{ date('Y') }} | Généré le {{ date('d/m/Y H:i') }} | Page 1/1
    </div>
</body>
</html>
