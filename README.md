# 🏢 Ontario Group - Gestion Immobilière

> Plateforme de gestion immobilière complète pour agences et gestionnaires de biens.

![Laravel](https://img.shields.io/badge/Laravel-11.x-red?style=flat-square&logo=laravel)
![PHP](https://img.shields.io/badge/PHP-8.2+-777BB4?style=flat-square&logo=php)
![TailwindCSS](https://img.shields.io/badge/TailwindCSS-3.x-06B6D4?style=flat-square&logo=tailwindcss)
![MySQL](https://img.shields.io/badge/MySQL-8.0+-4479A1?style=flat-square&logo=mysql)

---

## 📋 Fonctionnalités

### Gestion des Biens
- ✅ Création et modification de biens immobiliers
- ✅ Upload d'images multiples par bien
- ✅ Suivi des statuts (Libre, Occupé)
- ✅ Informations détaillées (type, surface, loyer, charges)

### Gestion des Locataires
- ✅ Dossiers locataires complets
- ✅ Upload de documents numérisés (CNI, contrats signés, attestations)
- ✅ Historique des contrats
- ✅ Informations de contact

### Gestion des Contrats
- ✅ Création de baux
- ✅ Suivi des contrats actifs/résiliés
- ✅ Génération de PDF des contrats
- ✅ Liaison automatique bien/locataire

### Gestion des Loyers
- ✅ Génération mensuelle des loyers
- ✅ Suivi des paiements (Payé, Partiel, Impayé)
- ✅ Export de quittances PDF
- ✅ Tableau de bord des impayés

### Gestion des Propriétaires
- ✅ Fiche propriétaire complète
- ✅ Configuration de l'agence
- ✅ Commissions et suivis

### Gestion des Dépenses & Travaux
- ✅ Suivi des dépenses par bien (maintenance, travaux, taxes)
- ✅ Upload de justificatifs
- ✅ Catégorisation automatique
- ✅ Bilan financier propriétaire (revenus - dépenses)

### Sécurité
- ✅ Middleware SecurityHeaders (OWASP)
- ✅ Protection CSRF, XSS, Clickjacking
- ✅ Logging des erreurs côté serveur
- ✅ Audit de sécurité intégré

### Administration
- ✅ Gestion des utilisateurs multi-rôles
- ✅ Système de permissions granulaires
- ✅ Logs d'activité
- ✅ Rapports mensuels

---

## 👥 Rôles et Permissions

| Rôle | Description | Accès |
|------|-------------|-------|
| **Admin** | Administrateur système | Accès complet |
| **Direction** | Direction de l'agence | Rapports, supervision |
| **Gestionnaire** | Gestionnaire de biens | CRUD complet immobilier |
| **Comptable** | Service comptabilité | Paiements, rapports financiers |

---

## 🚀 Installation

### Prérequis
- PHP 8.2+
- Composer
- Node.js 18+
- MySQL 8.0+

### Étapes

```bash
# 1. Cloner le projet
git clone https://github.com/ontariogroup/gestion-immobiliere.git
cd gestion-immobiliere

# 2. Installer les dépendances PHP
composer install

# 3. Installer les dépendances Node
npm install

# 4. Copier le fichier d'environnement
cp .env.example .env

# 5. Générer la clé d'application
php artisan key:generate

# 6. Configurer la base de données dans .env
# DB_DATABASE=gestion_immobiliere
# DB_USERNAME=root
# DB_PASSWORD=

# 7. Exécuter les migrations
php artisan migrate

# 8. Créer le lien symbolique pour le storage
php artisan storage:link

# 9. (Optionnel) Seeder les données de test
php artisan db:seed

# 10. Compiler les assets
npm run build

# 11. Lancer le serveur
php artisan serve
```

L'application sera accessible sur `http://localhost:8000`

---

## 📁 Structure du Projet

```
├── app/
│   ├── Http/
│   │   ├── Controllers/      # Contrôleurs
│   │   └── Middleware/       # Middlewares (SecurityHeaders, etc.)
│   ├── Models/               # Modèles Eloquent
│   └── Helpers/              # Helpers (Permissions, ActivityLogger)
├── database/
│   ├── migrations/           # Migrations de BDD
│   └── seeders/              # Seeders de données
├── resources/
│   ├── views/
│   │   ├── dashboard/        # Vues du tableau de bord
│   │   └── auth/             # Vues d'authentification
│   ├── css/                  # Styles
│   └── js/                   # JavaScript
├── routes/
│   └── web.php               # Routes de l'application
├── storage/
│   └── app/public/           # Fichiers uploadés
└── public/                   # Assets publics
```

---

## 🛠️ Commandes Utiles

```bash
# Lancer le serveur de développement
php artisan serve

# Compiler les assets en mode watch
npm run dev

# Exécuter les migrations
php artisan migrate

# Rollback des migrations
php artisan migrate:rollback

# Vider le cache
php artisan cache:clear
php artisan config:clear
php artisan view:clear

# Régénérer l'autoload
composer dump-autoload

# Seeder les biens du site public
php artisan db:seed --class=OntarioPublicSiteSeeder
```

---

## 📊 Technologies Utilisées

- **Backend**: Laravel 11.x
- **Frontend**: Blade + TailwindCSS 3.x
- **Base de données**: MySQL 8.0
- **Authentification**: Laravel Breeze
- **PDF**: DomPDF
- **Storage**: Laravel Storage (local)

---

## 📝 Changelog

### v1.3.0 (2026-02-04)
- 🔒 **Middleware SecurityHeaders** (X-Frame-Options, X-XSS-Protection, HSTS)
- 🔒 Correction de 10 erreurs exposées (logging serveur + messages génériques)
- ✨ **Gestion des dépenses** (maintenance, travaux, taxes, assurances)
- ✨ Bilan financier global propriétaire (revenus - dépenses = bénéfice net)
- ✨ **OntarioPublicSiteSeeder** avec 18 biens immobiliers réalistes
- 🗑️ Nettoyage des fichiers temporaires

### v1.2.1 (2026-02-03)
- 🔒 Audit de sécurité complet
- 🔒 Protection Mass Assignment (utilisation de `$request->only()`)
- 🔒 Rate limiting sur les routes d'authentification
- 🔒 Validation renforcée des uploads (mimetypes)
- 🔒 Logging des erreurs côté serveur
- ✨ Ajout de l'upload de documents pour les locataires
- 🎨 Amélioration de l'UX (boutons d'actions toujours visibles)

### v1.1.0 (2026-01-26)
- ✨ Upload d'images multiples pour les biens
- ✨ Génération de PDF des contrats
- ✨ Système de logs d'activité

### v1.0.0 (2026-01-25)
- 🎉 Version initiale
- ✨ CRUD complet (Propriétaires, Biens, Locataires, Contrats, Loyers)
- ✨ Système de rôles et permissions
- ✨ Dashboard multi-rôles

---

## 📄 Licence

Ce projet est propriétaire - © 2026 Ontario Group. Tous droits réservés.

---

## 👨‍💻 Développement

Développé par l'équipe Ontario Group.

Pour toute question ou support, contactez : support@ontariogroup.net
