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
            font-size: 8.5px;
            color: #334155;
            line-height: 1.35;
            margin: 0;
            padding: 0;
            background-color: #fff;
        }

        .page {
            position: relative;
            padding: 25px 35px;
            height: 100%;
            box-sizing: border-box;
        }

        /* Branding Colors */
        .text-brand-red { color: #cb2d2d; }
        .text-brand-blue { color: #0f172a; }

        /* Document Title */
        .doc-title {
            text-align: center;
            font-size: 13px;
            font-weight: 900;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            margin-bottom: 20px;
            border-bottom: 1.5px solid #cb2d2d;
            padding-bottom: 8px;
        }

        /* Boxes & Grids */
        .grid-container {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            page-break-inside: avoid;
        }
        .grid-col {
            width: 48%;
            vertical-align: top;
        }
        .grid-gap {
            width: 4%;
        }

        /* Parties */
        .party-box {
            background-color: #f8fafc;
            border: 1px solid #f1f5f9;
            border-top: 3px solid #0f172a;
            border-radius: 6px;
            padding: 10px 12px;
        }
        .party-box-alt { border-top-color: #cb2d2d; }
        
        .party-label {
            font-size: 7px;
            font-weight: 800;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
        }
        .party-name {
            font-size: 11px;
            font-weight: 900;
            color: #0f172a;
            text-transform: uppercase;
            margin-bottom: 4px;
        }
        .party-details {
            font-size: 8px;
            color: #475569;
            line-height: 1.4;
        }

        /* Essential Sections */
        .section-header {
            background-color: #f8fafc;
            color: #0f172a;
            padding: 6px 10px;
            font-size: 8.5px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 1px;
            border-bottom: 2px solid #e2e8f0;
            border-left: 3px solid #cb2d2d;
            margin-bottom: 10px;
        }

        /* Data Tables */
        .data-table {
            width: 100%;
            border-collapse: collapse;
        }
        .data-table td {
            padding: 6px 8px;
            border-bottom: 1px solid #f1f5f9;
            font-size: 8.5px;
            vertical-align: middle;
        }
        .data-table .label {
            width: 40%;
            font-weight: 800;
            color: #64748b;
            text-transform: uppercase;
            font-size: 7px;
            background-color: #f8fafc;
        }
        .data-table .value {
            font-weight: 800;
            color: #0f172a;
        }
        .data-table .val-red {
            color: #cb2d2d;
        }

        /* Clauses & Legal Text */
        .legal-block {
            margin-top: 20px;
            padding: 15px;
            background-color: #fafaf9;
            border: 1px solid #f1f5f9;
            border-radius: 6px;
            page-break-inside: avoid;
        }
        .clauses {
            text-align: justify;
            font-size: 7.5px;
            color: #334155;
            column-count: 2;
            column-gap: 20px;
            -webkit-column-count: 2;
            -webkit-column-gap: 20px;
            -moz-column-count: 2;
            -moz-column-gap: 20px;
        }
        /* Fallback for DOMPDF if columns don't work */
        .clause-item {
            margin-bottom: 8px;
        }
        .clause-title {
            font-weight: 900;
            color: #0f172a;
            text-transform: uppercase;
            font-size: 7.5px;
            display: inline-block;
            margin-right: 5px;
        }

        /* Signatures */
        .signature-section {
            margin-top: 30px;
            page-break-inside: avoid;
        }
        .signature-box {
            width: 45%;
            height: 90px;
            border: 1px dashed #cbd5e1;
            background-color: #f8fafc;
            border-radius: 6px;
            position: relative;
            float: left;
        }
        .signature-gap { width: 10%; float: left; height: 10px; }
        .sign-hint {
            position: absolute;
            top: 10px;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 7px;
            font-weight: 800;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .sign-name {
            position: absolute;
            bottom: 12px;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 9px;
            font-weight: 900;
            color: #0f172a;
            text-transform: uppercase;
        }

        .clear { clear: both; }
        .clearfix::after { content: ""; clear: both; display: table; }
    </style>
</head>
<body>
    <div class="page">
        <!-- HEADER -->
        @include('pdf.partials.header', [
            'title' => 'CONTRAT DE BAIL',
            'ref' => 'BAIL-' . str_pad($contrat->id, 5, '0', STR_PAD_LEFT),
            'date' => now()->format('d/m/Y')
        ])

        <div class="doc-title">Contrat de Location à Usage {{ ucfirst($contrat->type_bail ?? 'Habitation') }}</div>

        <!-- PARTIES (Side by Side) -->
        <table class="grid-container">
            <tr>
                <td class="grid-col party-box">
                    <div class="party-label">Le Bailleur (Mandataire)</div>
                    <div class="party-name">ONTARIO GROUP S.A.</div>
                    <div class="party-details">
                        Gestionnaire de Patrimoine Immobilier<br>
                        Siège : Liberté 6 Extension, Dakar<br>
                        Agissant au nom et pour le compte du Propriétaire.
                    </div>
                </td>
                <td class="grid-gap"></td>
                <td class="grid-col party-box party-box-alt">
                    <div class="party-label">Le Preneur (Locataire)</div>
                    <div class="party-name">{{ $contrat->locataire->nom }}</div>
                    <div class="party-details">
                        <strong>Tél :</strong> {{ $contrat->locataire->telephone ?? 'Non renseigné' }}<br>
                        <strong>Email :</strong> {{ $contrat->locataire->email ?? 'Non renseigné' }}<br>
                        <strong>Pièce d'Identité :</strong> {{ $contrat->locataire->cni ?? 'Non renseigné' }}
                    </div>
                </td>
            </tr>
        </table>

        <!-- ARTICLE 1 & 2 (Side by Side) -->
        <table class="grid-container">
            <tr>
                <!-- Article 1 -->
                <td class="grid-col" style="padding-right: 15px;">
                    <div class="section-header">Art. 1 : Objet & Désignation</div>
                    <table class="data-table">
                        <tr>
                            <td class="label">Dénomination</td>
                            <td class="value">{{ $contrat->bien->nom }}</td>
                        </tr>
                        <tr>
                            <td class="label">Typologie</td>
                            <td class="value">{{ ucfirst($contrat->bien->type) }} ({{ $contrat->bien->surface ?? '--' }} m²)</td>
                        </tr>
                        <tr>
                            <td class="label">Localisation</td>
                            <td class="value">{{ $contrat->bien->adresse ?? 'Sénégal' }}</td>
                        </tr>
                        <tr>
                            <td class="label">Date d'effet</td>
                            <td class="value">{{ $contrat->date_debut ? \Carbon\Carbon::parse($contrat->date_debut)->format('d/m/Y') : 'À la signature' }}</td>
                        </tr>
                    </table>
                </td>
                
                <!-- Article 2 -->
                <td class="grid-col" style="padding-left: 15px; border-left: 1px dashed #cbd5e1;">
                    <div class="section-header">Art. 2 : Conditions Financières</div>
                    <table class="data-table">
                        <tr>
                            <td class="label">Loyer Hors Taxe</td>
                            <td class="value val-red">{{ format_money($contrat->loyer_montant) }}</td>
                        </tr>
                        <tr>
                            <td class="label">Dépôt de Garantie</td>
                            <td class="value">{{ format_money($contrat->caution ?? 0) }}</td>
                        </tr>
                        <tr>
                            <td class="label">Frais de Dossier</td>
                            <td class="value">{{ format_money($contrat->frais_dossier ?? 0) }}</td>
                        </tr>
                        <tr>
                            <td class="label">Mode de Paiement</td>
                            <td class="value">Virement ou Espèces</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        <!-- ARTICLE 3: CLAUSES (Compact block) -->
        <div class="legal-block">
            <div class="section-header" style="margin-top: 0;">Art. 3 : Dispositions Générales & Engagement</div>
            <div class="clauses">
                <div class="clause-item">
                    <span class="clause-title">3.1 État des Lieux :</span>
                    Le Preneur prend les lieux dans l'état où ils se trouvent au jour de l'entrée en jouissance, tels qu'ils seront décrits dans l'état des lieux contradictoire dressé lors de la remise des clés. Il s'engage à les rendre dans le même état.
                </div>
                <div class="clause-item">
                    <span class="clause-title">3.2 Paiement du Loyer :</span>
                    Le loyer est payable mensuellement et d'avance, au plus tard le 05 de chaque mois. Tout retard de paiement justifié entraînera l'application de pénalités de retard selon la réglementation en vigueur (10% de majoration après le 10 du mois).
                </div>
                <div class="clause-item">
                    <span class="clause-title">3.3 Dépôt de garantie :</span>
                    La somme remise à titre de caution ne produit aucun intérêt et ne pourra en aucun cas être imputée sur le paiement des derniers mois de loyer. Elle sera restituée dans un délai de 30 jours après la remise des clés, déduction faite des éventuelles sommes dues au Bailleur.
                </div>
                <div class="clause-item">
                    <span class="clause-title">3.4 Entretien & Réparations :</span>
                    Le Preneur est tenu d'assurer l'entretien courant des lieux loués et les menues réparations locatives. Les grosses réparations relevant du clos et du couvert restent à la charge du Propriétaire/Mandataire.
                </div>
                <div class="clause-item">
                    <span class="clause-title">3.5 Usage & Sous-location :</span>
                    Le Preneur s'engage à occuper les lieux en "bon père de famille". Toute sous-location, cession de bail ou changement d'affectation des locaux est rigoureusement interdite sans l'accord écrit et préalable du Bailleur.
                </div>
                <div class="clause-item">
                    <span class="clause-title">3.6 Clause Résolutoire :</span>
                    À défaut de paiement d'un seul terme de loyer à son échéance exacte, ou en cas d'inexécution d'une seule des clauses du présent bail, celui-ci sera résilié de plein droit après une simple mise en demeure restée infructueuse pendant huit (8) jours.
                </div>
                <div class="clear"></div>
            </div>
            
            <p style="text-align: center; margin-top: 15px; font-size: 8px; font-weight: 800; color: #0f172a; text-transform: uppercase;">
                Lu et approuvé. Bon pour accord mutuel.
            </p>
        </div>

        <!-- SIGNATURES -->
        <div class="signature-section clearfix">
            <p style="font-size: 8px; color: #64748b; text-align: center; margin-bottom: 12px; font-style: italic;">
                Fait en double exemplaire à Dakar, le {{ now()->format('d/m/Y') }}
            </p>
            <div class="signature-box">
                <div class="sign-hint">Pour Le Bailleur (L'Agence)</div>
            </div>
            <div class="signature-gap"></div>
            <div class="signature-box">
                <div class="sign-hint">Pour Le Preneur (Locataire)</div>
                <div class="sign-name">{{ $contrat->locataire->nom }}</div>
            </div>
        </div>

        @include('pdf.partials.footer')
    </div>
</body>
</html>
