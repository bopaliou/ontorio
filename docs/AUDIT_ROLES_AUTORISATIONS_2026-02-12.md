# Audit RBAC complet — Ontario Group (2026-02-12, version mise à jour)

## 1) Compréhension synthétique du projet

Le projet est une application Laravel de gestion immobilière couvrant :
- gestion du patrimoine (biens, propriétaires, locataires, contrats),
- gestion financière (loyers, paiements, dépenses),
- reporting (rapports mensuels, impayés, commissions),
- administration (utilisateurs, rôles, système).

Le contrôle d’accès repose sur :
1. Spatie Permission (rôles/permissions en base),
2. un champ legacy `users.role`,
3. des middlewares `role` et `permission`.

## 2) Rôles et périmètres observés

### Rôles principaux
- `admin`
- `direction`
- `gestionnaire`
- `comptable`
- `proprietaire` (présent dans le flux dashboard)

### Découpage des accès par routes

Le routage est désormais structuré en groupes explicites :
- **Lecture opérationnelle** : `admin|direction|gestionnaire|comptable`
- **Écriture opérationnelle** : `admin|gestionnaire`
- **Écriture financière** : `admin|comptable`
- **Administration système** : `admin`

Le modèle est globalement cohérent avec une séparation moderne des responsabilités.

## 3) Cohérence avec la gestion immobilière moderne

## ✅ Points cohérents

1. **Séparation des tâches renforcée**
   - La direction n’a plus accès aux mutations de gestion patrimoniale/financière.

2. **Granularité par permission activement utilisée**
   - Les routes sensibles sont contrôlées via `permission:*` (ex: `biens.create`, `paiements.delete`, `rapports.mensuel`).

3. **Stats dashboard protégées**
   - Les endpoints `/api/stats/*` et `/api/alerts` sont protégés par `auth + role` avec throttle.

4. **Fallback legacy toujours présent**
   - Utile pour continuité pendant migration progressive vers source d’autorité unique.

## ⚠️ Points à surveiller / améliorer

1. **Double source d’autorité encore active**
   - Spatie + `users.role` + fallback middleware.
   - Risque de divergence si les synchronisations ne sont pas strictes.

2. **Rôle `proprietaire` partiellement transversal**
   - Présent côté dashboard et enum users, mais doit être validé de bout en bout (routes métiers dédiées, UX, policy explicite).

3. **Compatibilité tests/règles d’autorisation**
   - Le socle semble cohérent, mais chaque refactor de route doit conserver les endpoints utilisés en sécurité (`/api/stats/kpis` notamment).

## 4) Évaluation globale

- **Architecture RBAC** : Bonne
- **Séparation métier (opérationnel/finance/direction)** : Bonne
- **Maturité sécurité authorization** : Intermédiaire+ à avancée
- **Alignement gestion immobilière moderne** : **Oui, globalement cohérent**, sous réserve d’unification finale vers Spatie-only.

## 5) Recommandations prioritaires (prochaine itération)

1. **Finaliser la migration vers Spatie comme source unique**
   - Réduire puis supprimer les dépendances à `users.role` dans le contrôle d’accès.

2. **Formaliser une matrice RBAC contractuelle**
   - Document unique (rôle × action × module) validé métier + technique.

3. **Ajouter des tests de non-régression orientés sécurité**
   - Cas d’accès interdit/autorisé par rôle sur routes critiques.

4. **Créer des policies (ou gates) par ressource sensible**
   - Pour une défense en profondeur au-delà du seul routage.

## 6) Conclusion

L’état actuel est **nettement plus cohérent** avec une gestion immobilière moderne qu’au démarrage de l’audit :
- meilleure séparation des responsabilités,
- permissions plus granulaires,
- endpoints sensibles correctement protégés.

La priorité restante est la **convergence vers une source d’autorité unique** (Spatie) pour éliminer définitivement les écarts potentiels legacy.


## 7) Matrice contractuelle

La matrice de référence a été formalisée dans `RBAC_MATRICE_CONTRACTUELLE.md` pour alignement métier/technique.
