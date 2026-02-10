# Audit technique du projet Ontorio

_Date : 2026-02-10_

## 1) Objectif et périmètre

Audit de compréhension + évaluation technique d’une application Laravel de gestion immobilière (back-office métier + auth + reporting), basé sur :

- lecture du code (`app/`, `routes/`, `database/`, `tests/`, `config/`),
- exécution de checks locaux non destructifs,
- revue sécurité/maintenabilité/performance.

## 2) Snapshot du projet

- Stack principale : **Laravel 12**, **PHP 8.2+**, **Blade + Tailwind**, MySQL/SQLite selon environnement.
- Taille observée (approx.) :
  - **25 contrôleurs**
  - **13 modèles**
  - **8 services**
  - **35 migrations**
  - **21 tests Feature** + **4 tests Unit**

Le périmètre fonctionnel couvre bien les domaines attendus : biens, locataires, contrats, loyers, paiements, propriétaires, dépenses, dashboard et exports.

## 3) Compréhension de l’architecture

### 3.1 Organisation générale

Architecture Laravel classique avec séparation par couches :

- **Controllers** pour l’orchestration HTTP,
- **Services** pour des calculs métier (ex. KPI dashboard),
- **Models Eloquent** pour la persistance,
- **Form requests / middleware** pour validation et contrôle d’accès,
- **Tests Feature/Unit** déjà présents.

### 3.2 Contrôle d’accès

Le projet utilise actuellement **deux mécanismes en parallèle** :

1. Rôle legacy (`users.role`) utilisé par les middlewares custom (`CheckRole`, `CheckPermission`) + helpers.
2. Spatie Permission (`HasRoles`, tables permissions/roles, contrôleur de gestion des rôles).

Cette coexistence est techniquement possible, mais crée un risque de divergence métier (règles incohérentes selon l’endroit où l’autorisation est vérifiée).

### 3.3 Sécurité HTTP et résilience

Points positifs observés :

- Headers sécurité ajoutés via middleware global web.
- Rate limiting déclaré pour routes sensibles.
- Gestion d’exceptions API pour éviter les messages techniques bruts quand `APP_DEBUG=false`.
- Validation des uploads (extensions + mimetypes + taille) sur documents.

## 4) Constat d’audit (forces)

1. **Couverture fonctionnelle solide** du métier immobilier.
2. **Modularité correcte** (services dédiés pour logique dashboard/statistiques).
3. **Culture qualité présente** (tests, middleware sécurité, index/perf via migrations dédiées).
4. **Intentions DevOps visibles** (route de migration, scripts de déploiement, cache invalidation).

## 5) Constat d’audit (risques & dette)

## P1 — Critique / Élevé

### P1.1 — Endpoint de migration déclenchable en HTTP

La route `/system/migrate` exécute des commandes d’exploitation (`migrate --force`, clear/cache route/config/view) depuis le web. Même protégée (auth admin + token + throttle), cela reste une surface d’attaque sensible (erreur config, fuite token, attaque interne, replay).

**Recommandation** : supprimer l’accès HTTP en production et basculer sur pipeline CI/CD ou exécution SSH out-of-band.

### P1.2 — Modèle d’autorisation dupliqué

Présence conjointe de :

- middlewares custom basés sur `user->role`,
- helper de permissions statique,
- Spatie roles/permissions.

**Risque** : drift fonctionnel (permissions différentes selon middleware/helper/Spatie), coût de maintenance élevé, complexité de debug.

**Recommandation** : converger vers **une seule source de vérité** (idéalement Spatie partout) et décommissionner le legacy progressivement.

## P2 — Moyen

### P2.1 — Fuite d’informations potentielle via logs et erreurs

- Slow query logging en mode debug inclut SQL + bindings + URL.
- `SystemController` renvoie `exception->getMessage()` en JSON 500 (utile en debug, risqué sinon).

**Recommandation** : appliquer une policy stricte de redaction/masquage des données et messages neutres côté client.

### P2.2 — Reproductibilité build incomplète

Lors de l’audit :

- `composer validate` signale lock désynchronisé.
- installation des dépendances bloquée par accès réseau GitHub (403), empêchant l’exécution complète des tests applicatifs dans cet environnement.

**Recommandation** : normaliser la chaîne CI (validate + install + tests) et verrouiller les artefacts.

## P3 — Opportunités d’amélioration

### P3.1 — Dette de cohérence dans la couche utilitaire

Le trait `OptimizedQueries` expose une API avancée (`scopeWithAggregate`) qui semble peu/mal utilisée et potentiellement fragile (construction de sous-requête relationnelle générique délicate).

**Recommandation** : soit supprimer ce code mort, soit couvrir par tests unitaires ciblés + exemples d’usage réels.

### P3.2 — Stratégie de cache à clarifier

Présence de plusieurs stratégies de cache (service dashboard + classe cache dédiée). La méthode `flushAll()` effectue un `Cache::flush()` global (impact large).

**Recommandation** : définir une stratégie de clés/tags par domaine et éviter les flush globaux hors maintenance.

## 6) Vérifications exécutées

### Vérifications passées

- Lint syntaxique PHP de `app/`, `routes/`, `database/`, `tests/` : **OK**.
- Inventaire structurel (controllers/models/services/migrations/tests) : **OK**.
- Revue ciblée sécurité des middlewares, routage admin/système, contrôleurs sensibles : **OK** (constats documentés ci-dessus).

### Vérifications avec limitation d’environnement

- `composer install --no-interaction --prefer-dist` : **KO** (blocage réseau GitHub 403 dans cet environnement).
- `php artisan test` : non exécutable tant que l’installation dépendances n’est pas complète.

## 7) Plan d’action recommandé (30 jours)

### Semaine 1 — réduction du risque immédiat

1. Geler/retirer la route `/system/migrate` en production.
2. Désactiver tout retour d’erreur technique côté client pour endpoints sensibles.
3. Aligner `composer.lock` sur `composer.json`.

### Semaines 2–3 — refonte autorisations

4. Choisir la source de vérité authz (Spatie recommandé).
5. Migrer middleware/helper legacy vers politiques/gates/permissions unifiées.
6. Ajouter tests de non-régression authz (cas positifs et négatifs par rôle).

### Semaine 4 — industrialisation

7. Mettre en place pipeline CI minimale : lint + validate + tests + contrôles sécurité basiques.
8. Encadrer logs applicatifs (redaction, niveau par environnement, rétention).
9. Publier un runbook unique d’exploitation et incidents.

## 8) KPIs de suivi proposés

- 0 endpoint d’administration critique exposé publiquement.
- 100% des règles d’accès centralisées dans un mécanisme unique.
- 100% pipeline CI verte sur branche principale.
- 0 retour client d’exception brute sur endpoints sensibles.

## 9) Conclusion

Le projet est **fonctionnellement riche** et dispose déjà de bonnes fondations Laravel. Le principal enjeu n’est pas la capacité métier, mais la **maturité opérationnelle** : réduire la surface d’attaque d’exploitation, unifier les autorisations, et fiabiliser la chaîne build/test.

Avec ces correctifs, la plateforme gagnera fortement en sécurité, prédictibilité et maintenabilité.
