# Vérification des calculs et cohérence métier (gestion immobilière)

## Périmètre audité
- `app/Models/Loyer.php`
- `app/Services/DashboardStatsService.php`
- `app/Services/PaymentService.php`
- `app/Http/Requests/StorePaiementRequest.php`
- `app/Http/Controllers/RapportController.php`
- `tests/Unit/Models/LoyerTest.php`
- `tests/Unit/Services/DashboardStatsServiceTest.php`

## Conclusion synthétique
Globalement, les calculs sont **bien structurés** (reste à payer, taux de recouvrement, occupation, commissions).

Des écarts ont toutefois été corrigés pour mieux coller aux standards d'une gestion immobilière moderne:
1. les **arriérés** doivent inclure les **pénalités**;
2. le **vieillissement des impayés (aging)** doit être basé sur la **date d'échéance**, pas uniquement sur le début du mois de loyer.

## Détail des vérifications

### 1) Loyer: échéance, retard, reste à payer
- Échéance: 5 du mois suivant (`date_echeance`) ✅
- Jours de retard: différence entre aujourd'hui et échéance si statut impayé/retard ✅
- Reste à payer: `montant + penalite - paiements` ✅

### 2) Paiements: cohérence de saisie
- Validation empêche un paiement supérieur au reste dû ✅
- Service transactionnel avec verrouillage pessimiste (`lockForUpdate`) ✅
- Statut loyer recalculé après chaque encaissement/suppression ✅

### 3) KPI financiers dashboard
- Loyers facturés: somme des loyers du mois ✅
- Encaissements mensuels: somme des paiements sur la période ✅
- Taux de recouvrement: paiements des loyers du mois / loyers facturés ✅
- Occupation financière: loyers facturés / potentiel brut ✅

### 4) Corrections appliquées
- Arriérés: passage de `SUM(montant)` à `SUM(montant + penalite)` ✅
- Aging: bucket calculé à partir de `date_echeance`; si non échu, classement en `0-30` ✅

## Alignement gestion immobilière moderne
Le moteur de calcul est maintenant mieux aligné avec les pratiques courantes:
- pilotage des créances au **solde réel dû** (principal + pénalités);
- lecture du risque via **aging basé échéance** (DSO/recouvrement);
- contrôle anti-surpaiement et robustesse transactionnelle.

## Recommandations complémentaires
- ✅ Taux de commission externalisé dans la configuration (`config/real_estate.php`).
- ✅ Règles de pénalité versionnées (grille par `type_bail`).
- ✅ KPI ajouté: `economic_vacancy_rate` + `economic_vacancy_loss`.
