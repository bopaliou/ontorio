# 🏢 Ontario Group - Gestion Immobilière

> Plateforme de gestion immobilière complète, sécurisée et optimisée pour les agences et gestionnaires de biens.

![Laravel](https://img.shields.io/badge/Laravel-12.x-red?style=flat-square&logo=laravel)
![PHP](https://img.shields.io/badge/PHP-8.2+-777BB4?style=flat-square&logo=php)
![TailwindCSS](https://img.shields.io/badge/TailwindCSS-3.x-06B6D4?style=flat-square&logo=tailwindcss)
![MySQL](https://img.shields.io/badge/MySQL-8.0+-4479A1?style=flat-square&logo=mysql)
![Pest](https://img.shields.io/badge/Testing-Pest_&_PHPUnit-green?style=flat-square)

---

## 📋 Fonctionnalités Principales

### 🏢 Gestion du Parc Immobilier
- **Biens & Locataires** : CRUD complet, statuts (Libre/Occupé), historique.
- **Dossiers Numérisés** : Upload sécurisé de documents, contrats, justificatifs, et CNI.
- **Contrats & Baux** : Génération fluide, liaison automatique, gestion des résiliations.

### 💰 Comptabilité & Finance (Audit 2026)
- **Services Centralisés** : Tous les calculs complexes sont gérés par `DashboardStatsService`, `LoyerService`, et `PaymentService`.
- **Loyers & Commissions** : Génération automatisée avec calcul exact de la base commissionnaire (excluant les pénalités).
- **Pénalités Versionnées** : Module de pénalités de retard intelligent selon le type de bail (Résidentiel/Commercial).
- **Révisions de Loyer** : Historique des révisions avec recalcule automatique des échéances futures et commissions.
- **Tableau de Bord Financier** : KPIs temps réel (Arriérés, Taux de Recouvrement, Vacance Économique, Gross Potential Rent).

### 📑 Exports & Rapports PDF Modernisés
- Quittances de loyer mensuelles.
- Contrats de bail professionnels.
- Bilan financier exhaustif par propriétaire (Revenus nets - Commissions - Dépenses).
- Rapports d'impayés et de vieillissement des arriérés.

### 🔒 Sécurité & Robustesse
- **OWASP HTTP Headers** : Middleware SecurityHeaders (HSTS, X-Frame-Options, X-XSS-Protection).
- **Rate Limiting** : Protection contre le bruteforce sur l'authentification.
- **Tests Automatisés** : Suite de tests SQLite in-memory isolée (`.env.testing`) certifiant les calculs financiers complexes.
- **Rôles & Permissions (RBAC)** : Accès stricts via Spatie Permissions (Admin, Direction, Gestionnaire, Comptable).

---

## 🚀 Installation & Déploiement

### Prérequis
- PHP 8.2+
- Composer
- Node.js 18+
- MySQL 8.0+

### Déploiement Local

```bash
# 1. Cloner le projet
git clone https://github.com/ontariogroup/gestion-immobiliere.git
cd gestion-immobiliere

# 2. Installer les dépendances
composer install
npm install

# 3. Configuration de l'environnement
cp .env.example .env
# Configurer DB_DATABASE, DB_USERNAME, DB_PASSWORD dans .env
php artisan key:generate

# 4. Base de données & Stockage
php artisan migrate
php artisan storage:link

# 5. Seeding (Essentiel pour les rôles et le compte Admin de base)
php artisan db:seed --class=TestUsersSeeder

# 6. Lancement du serveur
npm run build
php artisan serve
```

Comptes par défaut (mot de passe : `password`) :
- `admin@test.com` (Admin)
- `gestionnaire@test.com` (Gestionnaire)
- `comptable@test.com` (Comptable)
- `direction@test.com` (Direction)

---

## 🧪 Tests Unitaires

Le système utilise une base SQLite en mémoire pour garantir la vitesse et l'isolation absolue des tests.

```bash
# Lancer toute la suite de tests
php artisan test

# Tester spécifiquement la logique financière et de pénalités
php artisan test tests/Unit/Models/LoyerTest.php
php artisan test tests/Unit/Services/DashboardStatsServiceTest.php
```

---

## 📁 Architecture

L'application suit scrupuleusement le pattern MVC avec un fort accent sur l'architecture orientée **Services** (Service Layer) pour découpler la logique métier pesante des contrôleurs.

- `app/Services/` : Contient toute la logique de calcul ("Fat Models / Skinny Controllers").
- `app/Models/` : Contient les relations Eloquent et les Accessors.
- `app/Http/Controllers/` : Routage HTTP et parsing des requêtes.
- `docs/` : Contient tous les documents de conception interne, d'audits de sécurité et de spécifications.

---

## 📝 Changelog Récent

### v1.4.0 (2026-03)
- 🧹 Grand nettoyage du référentiel (Suppression des logs/debug files de développement).
- 💰 Résolution et standardisation globale des calculs financiers (Arriérés, Base Commissionnaire, Pénalités).
- 🧪 Implémentation isolée de `.env.testing` pour éviter les collisions SQLite/MySQL.
- 🎨 Interface UI modernisée (PDF Premium, KPIs Lazy-loaded).

*(Pour l'historique complet, consulter les commits ou les fichiers `docs/`)*

---

## 📄 Licence
Ce projet est propriétaire - © 2026 Ontario Group. Tous droits réservés.
