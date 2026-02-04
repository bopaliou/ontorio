<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Contrat de Bail - {{ $contrat->locataire->nom }}</title>
    <style>
        @page { margin: 40px; }
        body { 
            font-family: 'Helvetica', 'Arial', sans-serif; 
            font-size: 11px; 
            color: #333; 
            line-height: 1.4;
        }
        
        /* Color Palette */
        .text-brand { color: #274256; }
        .bg-brand { background-color: #274256; color: white; }
        .border-brand { border-color: #274256; }
        .bg-gray { background-color: #f3f4f6; }

        /* Helpers */
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-bold { font-weight: bold; }
        .uppercase { text-transform: uppercase; }
        .w-full { width: 100%; }
        .w-half { width: 48%; display: inline-block; vertical-align: top; }
        .mb-2 { margin-bottom: 8px; }
        .mb-4 { margin-bottom: 16px; }
        .mb-6 { margin-bottom: 24px; }
        
        /* Layout Elements */
        .header { 
            border-bottom: 2px solid #274256; 
            padding-bottom: 20px; 
            margin-bottom: 30px;
        }
        .logo-text { 
            font-size: 24px; 
            font-weight: 900; 
            color: #274256; 
            letter-spacing: -1px;
        }
        .logo-sub {
            font-size: 10px;
            color: #666;
            letter-spacing: 2px;
            text-transform: uppercase;
        }

        .document-title {
            text-align: center;
            font-size: 18px;
            font-weight: bold;
            text-transform: uppercase;
            background-color: #274256;
            color: white;
            padding: 8px;
            margin-bottom: 20px;
            border-radius: 4px;
        }

        .party-box {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 6px;
        }
        .party-title {
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
            color: #64748b;
            margin-bottom: 5px;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 5px;
        }

        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table.data-table th {
            text-align: left;
            padding: 8px;
            background-color: #f1f5f9;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
            color: #475569;
            border-bottom: 1px solid #cbd5e1;
        }
        table.data-table td {
            padding: 8px;
            border-bottom: 1px solid #e2e8f0;
        }

        .clauses {
            text-align: justify;
        }
        .clause-item {
            margin-bottom: 12px;
        }
        .clause-title {
            font-weight: bold;
            color: #274256;
            text-decoration: underline;
            margin-bottom: 4px;
            display: block;
        }

        .signatures {
            margin-top: 40px;
            page-break-inside: avoid;
        }
        .sig-box {
            width: 45%;
            float: left;
            border: 1px solid #cbd5e1;
            background-color: #f8fafc;
            height: 120px;
            padding: 15px;
        }
        .sig-box-right {
            float: right;
        }

        .footer {
            position: fixed;
            bottom: -20px;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 8px;
            color: #94a3b8;
            border-top: 1px solid #e2e8f0;
            padding-top: 10px;
        }
        
        .page-break { page-break-after: always; }
    </style>
</head>
<body>
    <!-- HEADER -->
    <div class="header">
        <table style="width: 100%;">
            <tr>
                <td style="width: 60%;">
                    <img src="{{ public_path('images/ontorio-logo.png') }}" alt="Ontario Group Logo" style="max-height: 70px; margin-bottom: 10px;">
                    <div class="logo-sub">Bien loger dans un bon logement</div>
                </td>
                <td style="width: 40%; text-align: right; font-size: 10px; color: #64748b;">
                    <strong>Agence Principale</strong><br>
                    5 Félix Faure x Colbert<br>
                    Dakar Plateau, Sénégal<br>
                    Tél: +221 33 822 32 67 / 33 842 05 80<br>
                    Email: commercial@ontariogroup.net
                </td>
            </tr>
        </table>
    </div>

    <!-- DOCUMENT TITLE -->
    <div class="document-title">
        CONTRAT DE BAIL À USAGE D'HABITATION
    </div>

    <!-- PARTIES -->
    <div style="margin-bottom: 30px;">
        <div class="w-half party-box">
            <div class="party-title">Le Bailleur (Propriétaire)</div>
            <p>
                <strong>ONTARIO GROUP S.A.</strong><br>
                Agissant pour le compte du propriétaire.<br>
                Siège Social : 5 Félix Faure x Colbert, Dakar<br>
                commercial@ontariogroup.net
            </p>
        </div>
        <div class="w-half party-box" style="float: right;">
            <div class="party-title">Le Preneur (Locataire)</div>
            <p>
                <strong>M./Mme {{ strtoupper($contrat->locataire->nom) }}</strong><br>
                Téléphone : {{ $contrat->locataire->telephone ?? 'Non renseigné' }}<br>
                Email : {{ $contrat->locataire->email ?? 'Non renseigné' }}<br>
                CNI / Passeport : {{ $contrat->locataire->cni ?? 'Non renseigné' }}
            </p>
        </div>
        <div style="clear: both;"></div>
    </div>

    <!-- BIEN LOUE -->
    <div class="mb-6">
        <h3 class="text-brand uppercase" style="border-bottom: 1px solid #e2e8f0; padding-bottom: 5px;">1. Désignation des Lieux Loués</h3>
        <table class="data-table">
            <tr>
                <th width="20%">Bien Immobilier</th>
                <td class="text-bold">{{ $contrat->bien->nom }}</td>
            </tr>
            <tr>
                <th>Type de bien</th>
                <td>{{ ucfirst($contrat->bien->type) }}</td>
                <th>Surface aprox.</th>
                <td>{{ $contrat->bien->surface ? $contrat->bien->surface . ' m²' : 'Non précisée' }}</td>
            </tr>
            <tr>
                <th>Adresse</th>
                <td colspan="3">{{ $contrat->bien->adresse ?? $contrat->bien->ville }}</td>
            </tr>
            <tr>
                <th>Consistance</th>
                <td colspan="3">{{ $contrat->bien->description ?? 'Conforme à l\'état des lieux d\'entrée.' }}</td>
            </tr>
        </table>
    </div>

    <!-- CONDITIONS FINANCIERES -->
    <div class="mb-6">
        <h3 class="text-brand uppercase" style="border-bottom: 1px solid #e2e8f0; padding-bottom: 5px;">2. Conditions Financières</h3>
        <table class="data-table">
            <tr>
                <th width="40%">Loyer Mensuel Principal</th>
                <td class="text-right text-bold" style="font-size: 14px;">{{ number_format($contrat->loyer_montant, 0, ',', ' ') }} F CFA</td>
            </tr>
            <tr>
                <th>Date de paiement</th>
                <td class="text-right">Le {{ $contrat->jour_paiement ?? '05' }} de chaque mois au plus tard</td>
            </tr>
            <tr>
                <th style="background-color: #fff7ed; color: #9a3412;">TOTAL À PAYER MENSUELLEMENT</th>
                <td class="text-right text-bold" style="background-color: #fff7ed; color: #9a3412; font-size: 14px;">{{ number_format($contrat->loyer_montant, 0, ',', ' ') }} F CFA</td>
            </tr>
        </table>
    </div>

    <!-- DUREE -->
    <div class="mb-6">
        <h3 class="text-brand uppercase" style="border-bottom: 1px solid #e2e8f0; padding-bottom: 5px;">3. Durée du Contrat</h3>
        <p>
            Le présent bail est consenti pour une durée déterminée ou indéterminée commençant le 
            <strong>{{ \Carbon\Carbon::parse($contrat->date_debut)->translatedFormat('d F Y') }}</strong>
            @if($contrat->date_fin)
             et se terminant le <strong>{{ \Carbon\Carbon::parse($contrat->date_fin)->translatedFormat('d F Y') }}</strong>
            @endif.
        </p>
    </div>

    <!-- CLAUSES JURIDIQUES -->
    <div class="clauses">
        <h3 class="text-brand uppercase" style="border-bottom: 1px solid #e2e8f0; padding-bottom: 5px;">4. Clauses et Conditions Générales</h3>
        
        <div class="clause-item">
            <span class="clause-title">4.1 - PAIEMENT DU LOYER</span>
            Le loyer est payable d'avance au domicile du Bailleur ou de son représentant. Tout retard de paiement pourra entraîner l'application de pénalités de retard et la résiliation du bail.
        </div>

        <div class="clause-item">
            <span class="clause-title">4.2 - USAGE DES LIEUX</span>
            Les locaux sont loués à usage exclusif d'habitation (ou professionnel selon le cas). Le Preneur s'interdit tout changement de destination sans l'accord écrit du Bailleur. Il devra jouir des lieux "en bon père de famille".
        </div>

        <div class="clause-item">
            <span class="clause-title">4.3 - ENTRETIEN ET RÉPARATIONS</span>
            Le Preneur prendra les lieux dans l'état où ils se trouvent lors de l'entrée en jouissance. Il sera tenu d'effectuer les réparations locatives et d'entretien courant. Les grosses réparations (Art. 606 du Code Civil) restent à la charge du Bailleur.
        </div>

        <div class="clause-item">
            <span class="clause-title">4.4 - CESSION ET SOUS-LOCATION</span>
            Toute sous-location ou cession de bail est rigoureusement interdite sauf accord écrit et préalable du Bailleur.
        </div>

        <div class="clause-item">
            <span class="clause-title">4.5 - DÉPÔT DE GARANTIE (CAUTION)</span>
            Si une caution a été versée, elle sera restituée au Preneur en fin de bail, déduction faite des sommes dues au Bailleur et des frais de remise en état éventuels constatés lors de l'état des lieux de sortie.
        </div>

        <div class="clause-item">
            <span class="clause-title">4.6 - RÉSILIATION</span>
            Le présent contrat pourra être résilié par l'une ou l'autre des parties moyennant un préavis (congé) de 2 mois (ou selon législation en vigueur), notifié par lettre recommandée ou par acte d'huissier. En cas de défaut de paiement, la clause résolutoire s'appliquera de plein droit.
        </div>
    </div>

    <!-- SIGNATURES -->
    <div class="signatures">
        <p style="margin-bottom: 15px;">Fait à <strong>Dakar</strong>, le <strong>{{ now()->format('d/m/Y') }}</strong>, en deux exemplaires originaux.</p>
        
        <div class="sig-box">
            <div class="party-title text-center">Le Bailleur (Ontario Group)</div>
            <div style="font-size: 9px; text-align: center; margin-bottom: 40px; color: #94a3b8;">(Lu et approuvé, cachet et signature)</div>
        </div>
        
        <div class="sig-box sig-box-right">
            <div class="party-title text-center">Le Preneur</div>
            <div style="font-size: 9px; text-align: center; margin-bottom: 40px; color: #94a3b8;">(Lu et approuvé, signature précédée de la mention manuscrite)</div>
            <div class="text-center text-bold">{{ strtoupper($contrat->locataire->nom) }}</div>
        </div>
        <div style="clear: both;"></div>
    </div>

    <!-- FOOTER -->
    <div class="footer">
        Page 1/1 - Contrat Réf: #{{ str_pad($contrat->id, 5, '0', STR_PAD_LEFT) }} - Généré par Ontario Group App le {{ now()->format('d/m/Y H:i:s') }}
    </div>
</body>
</html>
