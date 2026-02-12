# Audit des rôles & autorisations — Ontario (2026-02-12)

## 1) Compréhension globale du projet

L’application est une plateforme Laravel de gestion immobilière multi-modules (biens, locataires, contrats, loyers, paiements, dépenses, rapports), avec un tableau de bord unique décliné par rôle. Le socle sécurité repose sur :

- un RBAC via **Spatie Permission**,
- un champ legacy `users.role` conservé pour compatibilité,
- des middlewares maison `role` et `permission`,
- des contrôles d’affichage côté Blade (`PermissionHelper::can(...)`, `Auth::user()->role`).

## 2) Modèle cible déclaré

### Rôles métiers déclarés

- `admin` : accès complet.
- `direction` : supervision/lecture + rapports.
- `gestionnaire` : gestion opérationnelle du patrimoine.
- `comptable` : gestion financière.

Ce positionnement est documenté dans le README et dans la commande d’initialisation des permissions.

### Permissions disponibles

Le projet définit des permissions granulaires par domaine (`biens.*`, `locataires.*`, `contrats.*`, `loyers.*`, `paiements.*`, `depenses.*`, `proprietaires.*`, `rapports.*`, `documents.*`, `users.*`, `settings.*`, `roles.manage`).

## 3) Audit de cohérence (écarts observés)

## 3.1. Écart majeur : la Direction dispose de droits d’écriture via les routes

Le groupe de routes “gestion du patrimoine” inclut `role:admin|direction|gestionnaire` **tout en exposant des endpoints mutatifs** (`resource` + `POST/PUT/DELETE`) sur propriétaires, biens, locataires, contrats, loyers, révisions, dépenses.

➡️ Cela contredit le cadrage métier de la Direction (supervision/lecture). Dans un modèle moderne, la Direction devrait être en lecture + exports, pas en CRUD opérationnel.

## 3.2. Écart finance : la Direction incluse dans un groupe avec mutation paiements

Le groupe “gestion financière” inclut `role:admin|direction|comptable|gestionnaire`, avec `store` et `destroy` de paiements.

➡️ La Direction peut donc potentiellement agir sur l’encaissement/suppression, ce qui n’est généralement pas souhaitable (séparation des tâches et traçabilité financière).

## 3.3. Source d’autorité fragmentée (Spatie + legacy + helper statique)

L’autorisation réelle dépend de 3 couches qui peuvent diverger :

1. tables Spatie (`roles`, `permissions`, pivots),
2. `users.role` (fallback middleware + UI),
3. matrice codée en dur dans `PermissionHelper`.

➡️ Risque de drift élevé entre backend, UI, et policy.

## 3.4. Granularité Spatie sous-exploitée dans les routes

Le middleware `permission` existe et est aliasé, mais le routage principal est gouverné par `role:...` plutôt que `permission:...`.

➡️ On perd la finesse attendue d’un RBAC moderne (least privilege par action).

## 3.5. UI et backend pas toujours alignés

Exemple : section paiements/dépenses. Les boutons d’action sont parfois filtrés par `Auth::user()->role` côté vue, alors que les routes acceptent un périmètre différent.

➡️ Un utilisateur peut ne pas voir un bouton mais garder un endpoint accessible (ou l’inverse), ce qui crée incohérence UX/sécurité.

## 3.6. Rôle `proprietaire` partiellement implémenté

Le dashboard gère un cas `proprietaire`, mais l’enum legacy `users.role` ne contient pas cette valeur.

➡️ Incohérence de modèle pouvant générer blocages fonctionnels selon le chemin d’authentification/provisionnement des comptes.

## 3.7. Seed utilisateurs tests non aligné RBAC Spatie

Le seeder de comptes tests renseigne `role` mais n’assigne pas explicitement les rôles Spatie (`assignRole`).

➡️ En environnement où le fallback legacy serait retiré, le comportement des comptes seedés devient incertain.

## 4) Évaluation par rapport à une gestion immobilière moderne

### Points positifs

- Bonne structuration métier des modules.
- Permissions déjà nommées de façon exploitable.
- Intention de migration vers un RBAC standard (Spatie) pertinente.

### Points à corriger pour être “production-grade moderne”

- **Séparation des responsabilités** insuffisante pour la Direction.
- **Least privilege** non strict (route groups trop larges).
- **Single source of truth** non atteinte (legacy + helper statique).
- **Cohérence UI/API** à renforcer.

## 5) Recommandations priorisées

1. **Durcir les routes immédiatement**
   - Retirer `direction` des routes mutatives (CRUD patrimoine/finance), ne garder que lecture + rapports/export.
2. **Basculer progressivement vers `permission:` dans le routage**
   - Exemples : `permission:paiements.create`, `permission:depenses.delete`, etc.
3. **Unifier le modèle d’autorisation**
   - Faire de Spatie la source unique, réduire puis supprimer le fallback `users.role`.
4. **Refactor `PermissionHelper`**
   - Le brancher sur `$user->can(...)` (Spatie) au lieu d’une matrice locale statique.
5. **Harmoniser UI avec backend**
   - Même règle d’autorisation pour boutons, sections et endpoints.
6. **Clarifier le rôle propriétaire**
   - Soit le supporter de bout en bout (migrations + rôles + routes + onboarding), soit le retirer du flux tant qu’il n’est pas complet.
7. **Mettre à niveau les seeders/tests**
   - Assigner explicitement les rôles Spatie dans les seeders de test.

## 6) Verdict

Le projet est **bien avancé structurellement** et dispose d’une base RBAC solide, mais l’implémentation actuelle est **partiellement cohérente** avec les standards de gestion immobilière moderne.

Le point bloquant principal est la **sur-permission de la Direction** dans des flux opérationnels et financiers mutatifs, combinée à une **coexistence non maîtrisée** entre logique legacy et Spatie.

> Niveau de maturité autorisation estimé : **intermédiaire (5.5/10)**. Avec les correctifs ci-dessus, le projet peut rapidement monter vers un niveau “audit-ready”.
