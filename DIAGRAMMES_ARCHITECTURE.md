# DIAGRAMMES ARCHITECTURAUX

## 1. Modèle de Données - Entités & Relations

```
Proprietaire (1)
    │
    ├─── hasMany ──────────> Bien (1..*)
    │                           │
    │                           ├─── hasMany ──────────> BienImage (0..*)
    │                           │
    │                           ├─── hasMany ──────────> Contrat
    │                           │                           │
    └───────────────────────────┴─── hasMany ──────────> Depense

                                    Contrat (Centre du modèle)
                                        │
                        Belong ─────────┼─────────► Bien
                        Belong ─────────┼─────────► Locataire
                        hasMany ────────┼─────────► Loyer (mensuel)
                        hasMany ────────┼─────────► RevisionLoyer
                                        │
                                        └─── hasManyThrough ──> Paiement
                                                                    │
                        Loyer ────────────────────────────────► Paiement
                         │                                          │
                         ├─── montant, mois                       ├─── montant
                         ├─── statut (payé/émis/retard)          ├─── date_paiement
                         ├─── pénalité                           ├─── mode (virement/espèces)
                         └─── note_annulation                    └─── user_id (audit)


Locataire
    │
    ├─── hasMany ──────────> Contrat
    ├─── hasMany ──────────> Garant
    └─── morphMany ────────> Document (polymorphe)


ActivityLog (AUDIT TRAIL)
    └─── Enregistre toutes les actions critiques
         (CRUD, paiements, révisions, créations contrats)
```

## 2. Flux des Données - Cycle de Vie

```
                    CRÉATION CONTRAT
                           │
                    ┌──────┴──────┐
                    │             │
                  Bien ←────── Locataire
                    │             │
            +──────┴──────+       │
            │             │       │
        Images      Depenses      │
            │                 Garants
            │                     │
        Contrat (créé) ───────────┼──> ActivityLog
            │                     │
            │         ┌───────────┘
            │         │
            ▼         ▼
        LOYERS GÉNÉRÉS MENSUELLEMENT (via LoyerService)
            │
            ├─ Montant = Contrat.loyer_montant
            ├─ Mois = Y-m (courant/futur)
            ├─ Statut = "émis"
            └─ Commission = calculée
                   │
                   ▼
            ATTENDRE PAIEMENT
                   │
            ┌──────┴──────────────┐
            │                     │
         Payé          Partiellement_payé
            │                     │
            └──────────┬──────────┘
                       │
                    Paiement(s)
                       │
                   ├─ Montant
                   ├─ Mode
                   ├─ Date
                   ├─ Preuve (document)
                   └─ user_id (qui a enregistré)
                       │
                       ▼
                   ActivityLog
                       │
                       ▼
                DASHBOARD UPDATES
                (KPIs, Taux Recouvrement, Arriérés)
```

## 3. Architecture Système - Layers

```
┌─────────────────────────────────────────────────────────┐
│                    PRESENTATION                         │
│  ┌──────────────┐   ┌──────────────┐  ┌──────────────┐ │
│  │ Blade Views  │   │ Alpine.js    │  │ TailwindCSS  │ │
│  │ (Templates)  │   │ (Reactivity) │  │ (Styling)    │ │
│  └──────────────┘   └──────────────┘  └──────────────┘ │
└──────────────────────────┬──────────────────────────────┘
                           │ HTTP Requests
                           ▼
┌─────────────────────────────────────────────────────────┐
│                   API LAYER                             │
│  ┌─────────────────────────────────────────────────┐   │
│  │             ROUTING (web.php)                   │   │
│  │  GET/POST/PUT/DELETE mounted RESOURCES         │   │
│  └──────────────────┬──────────────────────────────┘   │
│                     │                                   │
│  ┌─────────────────┴┬──────────────────────────────┐   │
│  │    MIDDLEWARE   │ (Auth, Role, Permission,     │   │
│  │                 │  CSRF, XSS, SecurityHeaders) │   │
│  └────────┬────────┴──────────────────────────────┘   │
└───────────┼──────────────────────────────────────────────┘
            │
            ▼
┌─────────────────────────────────────────────────────────┐
│               CONTROLLER LAYER (16)                     │
│  ┌─────────────────────────────────────────────────┐   │
│  │ DashboardController • BienController           │   │
│  │ LocataireController • ContratController        │   │
│  │ LoyerController • PaiementController           │   │
│  │ ProprietaireController • DepenseController     │   │
│  │ DocumentController • UserController • ...      │   │
│  └──────────────┬───────────┬──────────────────────┘   │
│                 │           │                         │
└─────────────────┼───────────┼─────────────────────────┘
                  │           │
          ┌───────┴┐      ┌───┴───────┐
          ▼        │      │           ▼
┌──────────────────┼──────┴─────────────────┐
│  SERVICE LAYER   │                        │
│  ┌──────────────────────────────────┐   │
│  │ DashboardStatsService (KPIs)     │   │
│  │ LoyerService (génération)        │   │
│  │ PermissionHelper                 │   │
│  │ ActivityLogger                   │   │
│  └──────────────────────────────────┘   │
└──────────────────┬──────────────────────┘
                   │
                   ▼
┌─────────────────────────────────────────────────────────┐
│                 MODEL LAYER (13)                        │
│  ┌─────────────────────────────────────────────────┐   │
│  │ Bien • Proprietaire • Locataire • Contrat      │   │
│  │ Loyer • Paiement • Depense • Document          │   │
│  │ Garant • RevisionLoyer • BienImage             │   │
│  │ ActivityLog • User                             │   │
│  └──────────────────────────────────────────────────┘   │
└──────────────────┬──────────────────────────────────────┘
                   │ Eloquent ORM
                   ▼
┌─────────────────────────────────────────────────────────┐
│          DATABASE LAYER (MySQL)                         │
│  ┌─────────────────────────────────────────────────┐   │
│  │ 32 Migrations • Indexes Performance             │   │
│  │ 13 Tables + Permission Tables (Spatie)         │   │
│  │ Foreign Keys + Relationships                    │   │
│  └─────────────────────────────────────────────────┘   │
└─────────────────────────────────────────────────────────┘
```

## 4. Flow Authentification & Autorisation

```
USER ACCEDES
      │
      ▼
  ROUTE (middleware 'auth')
      │
      ├─ Pas authentifié → redirect /login
      │
      ▼ (Authentifié)
  
  MIDDLEWARE CHAIN
      │
      ├─ CheckRole         (ex: 'role:admin|gestionnaire')
      │  └─ user->role ∈ allowed_roles?
      │     └─ YES → suivant
      │     └─ NO → 403 Forbidden
      │
      ├─ CheckPermission   (ex: 'permission:edit-contrat')
      │  └─ user->hasPermissionTo('edit-contrat')?
      │     └─ YES → suivant
      │     └─ NO → 403 Forbidden
      │
      └─ SecurityHeaders   (OWASP)
         └─ Ajoute X-Frame-Options, CSP, etc.
            
      ▼ (Toutes vérifications OK)
      
  CONTROLLER ACTION
      │
      └─ Exécute logique métier
         ├─ ActivityLogger::log() → ActivityLog
         └─ Retourne View/JSON
```

## 5. Dashboard KPI Calculation Flow

```
┌─ DashboardController::index()
│
├─ getFinancialKPIs(mois='2026-02')    ──────► DashboardStatsService
│  │
│  ├─ Loyer::where('mois', '2026-02')
│  │  └─ SUM(montant), SUM(payé), COUNT, etc.
│  │     → loyers_factures, nb_payes, nb_impayes
│  │
│  ├─ Paiement::whereMonth('2026-02')
│  │  └─ SUM(montant)
│  │     → loyers_encaisses (réel recouvrement)
│  │
│  ├─ Depense::whereMonth('2026-02')
│  │  └─ SUM(montant)
│  │     → depenses_mois
│  │
│  ├─ Loyer::whereIn('statut', [...impayés...])
│  │  └─ Calcul reste par loyer
│  │     → arrieres_total
│  │
│  ├─ Bien::sum('loyer_mensuel')
│  │  └─ Potentiel 100% loué
│  │     → gross_potential_rent
│  │
│  └─ Formules Finales
│     ├─ solde_net = encaisses - depenses (NOI)
│     ├─ taux_recouvrement = (encaisses / factures) × 100
│     ├─ taux_occupation_financier = (factures / potentiel) × 100
│     └─ arrears_aging (ventilation 0-30, 31-60, 61-90, 90+)
│        
├─ getParcStats()
│  ├─ Contrat::where('statut', 'actif')->count distinct biens
│  ├─ taux_occupation = biens_occupes / total_biens
│  └─ contratsExpirants = date_fin within 60 days
│
└─ RETOUR CONTROLLER
   └─ Passe toutes les stats à dashboard.blade.php
      Affiche KPIs, graphiques, tableaux
```

## 6. Génération Loyers - Processus Mensuel

```
┌─ Date: 1er du mois (ou cron job)
│
├─ LoyerController (ou Command/Job)
│  │
│  └─ foreach Contrat::where('statut', 'actif')
│     │
│     ├─ Crée Loyer
│     │  ├─ contrat_id
│     │  ├─ mois = "2026-02"
│     │  ├─ montant = contrat.loyer_montant
│     │  ├─ commission = calculée
│     │  ├─ statut = "émis"
│     │  └─ penalite = calculée si retard précédent
│     │
│     ├─ ActivityLogger::log('Création Loyer', ...)
│     │
│     └─ Envoie notification (optionnel)
│        └─ Email au gestionnaire
│           
└─ RÉSULTAT
   └─ Loyers prêts à être suivis/payés
      Appear sur dashboard actuel
      Prêt pour paiement enregistrement
```

## 7. Structure Répertoires

```
gestion-immobiliere/
│
├─── app/
│    ├─ Models/              (13 modèles Eloquent)
│    ├─ Controllers/         (16 contrôleurs)
│    ├─ Http/
│    │  ├─ Middleware/       (3 middlewares)
│    │  └─ Requests/         (Form Requests)
│    ├─ Services/            (DashboardStatsService, etc.)
│    ├─ Helpers/             (ActivityLogger, PermissionHelper)
│    ├─ Providers/           (AppServiceProvider)
│    └─ Console/Commands/    (Artisan commands)
│
├─── config/
│    ├─ app.php
│    ├─ auth.php
│    ├─ database.php
│    ├─ permission.php       (Spatie)
│    ├─ filesystems.php
│    └─ ... autres
│
├─── database/
│    ├─ migrations/          (32 migrations)
│    ├─ factories/           (7 factories)
│    └─ seeders/             (seeders)
│
├─── routes/
│    ├─ web.php              (50+ routes)
│    ├─ auth.php             (Breeze routes)
│    └─ console.php
│
├─── resources/
│    ├─ css/                 (Tailwind)
│    ├─ js/                  (Alpine.js)
│    └─ views/
│        ├─ auth/            (login, register)
│        ├─ dashboard/
│        ├─ biens/
│        ├─ locataires/
│        ├─ contrats/
│        ├─ loyers/
│        ├─ paiements/
│        ├─ proprietaires/
│        ├─ depenses/
│        ├─ rapports/
│        └─ layouts/
│
├─── storage/
│    ├─ app/                 (user uploads)
│    ├─ framework/           (cache, sessions)
│    └─ logs/
│
├─── public/
│    ├─ images/              (assets statiques)
│    ├─ build/               (Vite build output)
│    └─ index.php            (entry point)
│
├─── tests/
│    ├─ Feature/             (tests intégration)
│    └─ Unit/                (tests unitaires)
│
├─── vendor/                 (composer packages)
│
├─ composer.json             (PHP dépendances)
├─ package.json              (JS dépendances)
├─ phpunit.xml               (config tests)
├─ pint.json                 (code style)
├─ tailwind.config.js        (Tailwind config)
├─ vite.config.js            (Vite config)
├─ .env                      (environnement)
└─ README.md
```

## 8. Dépendances & Versions

```
BACKEND (PHP)
├─ Laravel 12.x              (Framework core)
├─ PHP 8.2+                  (Runtime)
├─ MySQL 8.0+                (Database)
├─ Laravel Breeze ^2.3       (Auth scaffolding)
├─ Spatie Permission         (Roles/Permissions)
├─ Laravel Tinker ^2.10      (REPL)
└─ Dev: PHPUnit, Mockery, Faker, Pint

FRONTEND (JS)
├─ Vite 7.0                  (Build tool)
├─ TailwindCSS 3.1           (Styling)
├─ Alpine.js 3.4             (Interactivity)
├─ Axios 1.11                (HTTP client)
├─ PostCSS 8.4               (CSS processing)
├─ Autoprefixer 10.4         (CSS vendor prefixes)
└─ Laravel Vite Plugin ^2.0

BUILD & DEPLOYMENT
├─ npm run dev               (development)
├─ npm run build             (production)
└─ Artisan commands (migrate, seed, etc.)
```

---

## RÉSUMÉ VISUEL FINAL

```
                    ┌─────────────────┐
                    │  USER INTERFACE │
                    │ (Blade + Alpine)│
                    └────────┬────────┘
                             │ HTTP
                             ▼
        ┌────────────────────────────────────────┐
        │        AUTHENTICATION & MIDDLEWARE     │
        │  (Auth, Role, Permission, Security)   │
        └────────────────┬───────────────────────┘
                         │
                         ▼
        ┌────────────────────────────────────────┐
        │    16 CONTROLLERS                      │
        │  (CRUD, Dashboard, Reports)            │
        └────────────────┬───────────────────────┘
                         │
        ┌────────────────┴───────────────────────┐
        │                                        │
        ▼                                        ▼
    SERVICES                            MODELS (13)
    └─ DashboardStatsService            ├─ Bien
    └─ LoyerService                     ├─ Proprietaire
    └─ PermissionHelper                 ├─ Locataire
    └─ ActivityLogger                   ├─ Contrat
                                        ├─ Loyer
                                        ├─ Paiement
                                        └─ ... otherss
                                        
        └────────────────┬───────────────────────┘
                         │ Eloquent ORM
                         ▼
        ┌────────────────────────────────────────┐
        │   MYSQL DATABASE (32 MIGRATIONS)       │
        │  (13 Tables + Permissions Structure)   │
        └────────────────────────────────────────┘
```

