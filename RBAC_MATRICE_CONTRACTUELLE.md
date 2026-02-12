# Matrice RBAC contractuelle (rôle × action × module)

Cette matrice formalise le contrat d'autorisation cible pour l'exploitation immobilière moderne.

Légende:
- ✅ autorisé
- 👁️ lecture seule
- ❌ interdit

| Module / Action | Admin | Direction | Gestionnaire | Comptable | Propriétaire |
|---|---|---|---|---|---|
| Biens - consulter | ✅ | 👁️ | ✅ | 👁️ | ❌ |
| Biens - créer/modifier/supprimer | ✅ | ❌ | ✅ | ❌ | ❌ |
| Locataires - consulter | ✅ | 👁️ | ✅ | 👁️ | ❌ |
| Locataires - créer/modifier/supprimer | ✅ | ❌ | ✅ | ❌ | ❌ |
| Contrats - consulter/imprimer | ✅ | 👁️ | ✅ | 👁️ | ❌ |
| Contrats - créer/modifier/supprimer | ✅ | ❌ | ✅ | ❌ | ❌ |
| Loyers - consulter | ✅ | 👁️ | ✅ | 👁️ | ❌ |
| Loyers - générer | ✅ | ❌ | ✅ | ✅ | ❌ |
| Paiements - consulter | ✅ | 👁️ | 👁️ | ✅ | ❌ |
| Paiements - créer/supprimer | ✅ | ❌ | ❌ | ✅ | ❌ |
| Dépenses - consulter | ✅ | 👁️ | ✅ | 👁️ | ❌ |
| Dépenses - créer/modifier | ✅ | ❌ | ✅ | ✅ | ❌ |
| Dépenses - supprimer | ✅ | ❌ | ✅ | ❌ | ❌ |
| Rapports - consulter/exporter | ✅ | ✅ | ✅ | ✅ | ❌ |
| Utilisateurs/Rôles/Système | ✅ | ❌ | ❌ | ❌ | ❌ |
| Stats API dashboard | ✅ | ✅ | ✅ | ✅ | ❌ |

## Mapping technique (permissions Spatie)

- Biens: `biens.view`, `biens.create`, `biens.edit`, `biens.delete`
- Locataires: `locataires.view`, `locataires.create`, `locataires.edit`, `locataires.delete`
- Contrats: `contrats.view`, `contrats.create`, `contrats.edit`, `contrats.delete`, `contrats.print`
- Loyers: `loyers.view`, `loyers.generate`, `loyers.quittance`
- Paiements: `paiements.view`, `paiements.create`, `paiements.edit`, `paiements.delete`
- Dépenses: `depenses.view`, `depenses.create`, `depenses.edit`, `depenses.delete`
- Propriétaires: `proprietaires.view`, `proprietaires.create`, `proprietaires.edit`, `proprietaires.delete`, `proprietaires.bilan`
- Rapports: `rapports.view`, `rapports.export`, `rapports.mensuel`
- Admin: `users.*`, `roles.manage`, `settings.*`

## Règles d'implémentation

1. Le middleware `role` et `permission` doivent s'appuyer uniquement sur Spatie (pas de fallback legacy dans le contrôle d'accès).
2. Les contrôleurs sensibles appliquent des **gates** (`$this->authorize(...)`) en défense en profondeur.
3. Les tests de non-régression valident explicitement les cas autorisés/interdits sur routes critiques.
